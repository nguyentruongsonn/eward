<?php

namespace App\Services\Files;

use App\Exceptions\ApiException;
use App\Models\HoSoResultFile;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResultFileSignatureService
{
    public function sign(HoSoXuLy $application, HoSoResultFile $file, Nguoi $actor, ?string $note = null): HoSoXuLy
    {
        if (! Storage::disk('local')->exists($file->path)) {
            throw new ApiException('Không tìm thấy file kết quả.', 'RESULT_FILE_NOT_FOUND', 404);
        }

        $hash = hash('sha256', Storage::disk('local')->get($file->path));
        $note = trim((string) $note);

        return DB::transaction(function () use ($application, $file, $actor, $hash, $note): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $signatures = is_array($locked->file_signatures)
                ? $locked->file_signatures
                : (json_decode((string) $locked->file_signatures, true) ?: []);
            $signatureKey = 'result:'.$file->getKey();
            if (isset($signatures[$signatureKey])) {
                throw new ApiException('File đã được ký trước đó.', 'RESULT_FILE_ALREADY_SIGNED', 409);
            }

            $signatures[$signatureKey] = [
                'result_file_id' => $file->getKey(),
                'signed_by' => $actor->getKey(),
                'signed_at' => now()->toIso8601String(),
                'signature_method' => 'internal_sha256',
                'file_sha256' => $hash,
                'note' => $note !== '' ? $note : null,
            ];
            $locked->file_signatures = json_encode($signatures, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $line = '['.now()->format('Y-m-d H:i').'] '.$actor->hoTen.' đã xác nhận file kết quả #'.$file->getKey().' bằng SHA-256: '.$hash;
            $locked->ghiChu = trim((string) $locked->ghiChu);
            $locked->ghiChu = $locked->ghiChu === '' ? $line : $locked->ghiChu."\n".$line;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc', 'resultFiles']);
        });
    }
}
