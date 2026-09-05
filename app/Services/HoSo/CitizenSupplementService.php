<?php

namespace App\Services\HoSo;

use App\Contracts\Files\FileStorage;
use App\Enums\HoSoStatus;
use App\Exceptions\ApiException;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Services\Files\ApplicationDocumentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CitizenSupplementService
{
    public function __construct(
        private readonly ApplicationDocumentService $documents,
        private readonly FileStorage $storage,
    ) {}

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, int|string>  $documentTypes
     */
    public function submit(
        Nguoi $actor,
        HoSoXuLy $application,
        array $files,
        array $documentTypes,
    ): array {
        if ($files === []) {
            throw new ApiException('Phải tải lên ít nhất một tài liệu bổ sung.', 'SUPPLEMENT_FILES_EMPTY', 422);
        }
        if (count($files) !== count($documentTypes)) {
            throw new ApiException('Số loại giấy tờ phải khớp với số tệp tải lên.', 'SUPPLEMENT_DOCUMENTS_MISMATCH', 422);
        }

        /** @var array<int, \App\Models\TaiLieuNop> $stored */
        $stored = [];

        try {
            return DB::transaction(function () use ($actor, $application, $files, $documentTypes, &$stored): array {
                $locked = HoSoXuLy::query()
                    ->whereKey($application->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ((int) $locked->maTrangThai !== HoSoStatus::SupplementRequested->value) {
                    throw new ApiException(
                        'Hồ sơ không ở trạng thái yêu cầu bổ sung.',
                        'SUPPLEMENT_STATUS_INVALID',
                        409,
                    );
                }

                $rawRequestData = $locked->yeu_cau_bo_sung;
                $metadataPresent = is_array($rawRequestData)
                    ? $rawRequestData !== []
                    : (is_string($rawRequestData) ? trim($rawRequestData) !== '' : $rawRequestData !== null);
                $requestData = is_array($rawRequestData)
                    ? $rawRequestData
                    : ($metadataPresent ? json_decode((string) $rawRequestData, true) : null);
                if ($metadataPresent && (! is_array($requestData) || ! array_key_exists('document_ids', $requestData) || ! is_array($requestData['document_ids']) || $requestData['document_ids'] === [])) {
                    throw new ApiException('Thông tin yêu cầu bổ sung không hợp lệ.', 'SUPPLEMENT_REQUEST_MALFORMED', 409);
                }
                $requestedIds = array_values(array_unique(array_map('intval', (array) ($requestData['document_ids'] ?? []))));
                $submittedIds = array_map('intval', $documentTypes);
                $missingIds = array_values(array_diff($requestedIds, $submittedIds));
                $extraIds = array_values(array_diff($submittedIds, $requestedIds));
                if ($requestedIds !== [] && ($missingIds !== [] || $extraIds !== [])) {
                    throw new ApiException('Tài liệu tải lên phải khớp danh sách được yêu cầu bổ sung.', 'SUPPLEMENT_DOCUMENTS_INCOMPLETE', 422, [
                        'missing_document_ids' => $missingIds,
                        'extra_document_ids' => $extraIds,
                    ]);
                }

                $mappedIds = DB::table('thanhphanhoso')
                    ->join('thanhphangiayto', 'thanhphangiayto.maThanhPhan', '=', 'thanhphanhoso.maThanhPhan')
                    ->where('thanhphanhoso.maTTHC', $locked->maTTHC)
                    ->pluck('thanhphangiayto.maGiayTo')
                    ->map(static fn ($id): int => (int) $id)
                    ->unique()
                    ->all();
                if ($mappedIds !== []) {
                    $invalidIds = array_values(array_diff($submittedIds, $mappedIds));
                    if ($invalidIds !== []) {
                        throw new ApiException('Có giấy tờ không thuộc thành phần thủ tục của hồ sơ.', 'SUPPLEMENT_DOCUMENT_TYPE_INVALID', 422, [
                            'invalid_document_ids' => $invalidIds,
                        ]);
                    }
                }

                foreach ($files as $index => $file) {
                    $stored[] = $this->documents->storeSupplementDocument(
                        $file,
                        $locked,
                        (int) ($documentTypes[$index] ?? 0),
                        $actor,
                        $this->storage,
                    );
                }

                $locked->forceFill([
                    'maTrangThai' => HoSoStatus::Accepted->value,
                    'maTrangThai_backup' => null,
                    'yeu_cau_bo_sung' => null,
                    'ghiChu' => trim((string) $locked->ghiChu)."\n[".now()->format('d/m/Y H:i').'] Công dân đã bổ sung '.count($stored).' tài liệu.',
                ])->save();

                return $stored;
            });
        } catch (\Throwable $exception) {
            $paths = array_values(array_filter(array_map(
                static fn (\App\Models\TaiLieuNop $document): ?string => $document->duongDan ?: null,
                $stored,
            )));

            if ($paths !== []) {
                Storage::disk('local')->delete($paths);
            }

            throw $exception;
        }
    }
}
