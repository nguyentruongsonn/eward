<?php

namespace App\Services\HoSo;

use App\Enums\HoSoStatus;
use App\Exceptions\ApiException;
use App\Jobs\SendSupplementRequestJob;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;

class ApplicationSupplementService
{
    /** @param list<int> $documentIds */
    public function request(HoSoXuLy $application, Nguoi $actor, array $documentIds, ?string $note = null): HoSoXuLy
    {
        $documentIds = array_values(array_unique(array_map('intval', $documentIds)));
        $documents = DB::table('giayto')
            ->whereIn('maGiayTo', $documentIds)
            ->get(['maGiayTo', 'tenGiayTo'])
            ->keyBy('maGiayTo');

        if ($documents->count() !== count($documentIds)) {
            throw new ApiException('Giấy tờ yêu cầu bổ sung không tồn tại.', 'SUPPLEMENT_DOCUMENT_INVALID', 422);
        }

        $names = array_map(fn (int $id): string => (string) $documents[$id]->tenGiayTo, $documentIds);
        $note = trim((string) $note);
        $updated = DB::transaction(function () use ($application, $actor, $documentIds, $names, $note): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $current = HoSoStatus::tryFrom((int) $locked->maTrangThai);
            if (! $current || ! in_array(HoSoStatus::SupplementRequested->value, $current->allowedTransitions(), true)) {
                throw new ApiException('Không thể yêu cầu bổ sung ở trạng thái hiện tại.', 'WORKFLOW_TRANSITION_INVALID', 409, [
                    'from' => $current?->value,
                    'to' => HoSoStatus::SupplementRequested->value,
                ]);
            }

            $mappedDocumentIds = DB::table('thanhphanhoso')
                ->join('thanhphangiayto', 'thanhphangiayto.maThanhPhan', '=', 'thanhphanhoso.maThanhPhan')
                ->where('thanhphanhoso.maTTHC', $locked->maTTHC)
                ->pluck('thanhphangiayto.maGiayTo')
                ->map(static fn ($id): int => (int) $id)
                ->unique()
                ->all();
            if ($mappedDocumentIds !== []) {
                $invalidDocumentIds = array_values(array_diff($documentIds, $mappedDocumentIds));
                if ($invalidDocumentIds !== []) {
                    throw new ApiException('Giấy tờ yêu cầu bổ sung không thuộc thành phần của thủ tục.', 'SUPPLEMENT_DOCUMENT_TYPE_INVALID', 422, [
                        'invalid_document_ids' => $invalidDocumentIds,
                    ]);
                }
            }

            $locked->maTrangThai_backup = $current->value;
            $locked->yeu_cau_bo_sung = json_encode([
                'document_ids' => $documentIds,
                'documents' => $names,
                'note' => $note !== '' ? $note : null,
                'requested_by' => $actor->getKey(),
                'requested_at' => now()->toIso8601String(),
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $locked->maTrangThai = HoSoStatus::SupplementRequested->value;
            $locked->ghiChu = trim((string) $locked->ghiChu);
            $line = '['.now()->format('Y-m-d H:i').'] '.$actor->hoTen.' yêu cầu bổ sung giấy tờ: '.implode(', ', $names);
            $locked->ghiChu = $locked->ghiChu === '' ? $line : $locked->ghiChu."\n".$line;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });

        SendSupplementRequestJob::dispatch($updated->getKey(), $actor->getKey(), $names, $note !== '' ? $note : null)->afterCommit();

        return $updated;
    }
}
