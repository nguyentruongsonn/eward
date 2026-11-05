<?php

namespace App\Services\HoSo;

use App\Contracts\Files\FileStorage;
use App\Enums\HoSoStatus;
use App\Exceptions\ApiException;
use App\Models\CongDan;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Models\TaiLieuNop;
use App\Models\TrangThaiHoSo;
use App\Models\TTHC;
use App\Services\Files\ApplicationDocumentService;
use App\Support\Idempotency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ApplicationSubmissionService
{
    public function __construct(
        private readonly Idempotency $idempotency,
        private readonly ApplicationDocumentService $documents,
        private readonly FileStorage $storage,
    ) {}

    /** @return Collection<int, TrangThaiHoSo> */
    public function statuses(): Collection
    {
        return TrangThaiHoSo::query()->orderBy('maTrangThai')->get();
    }

    public function listForUser(
        Nguoi $user,
        int $perPage = 15,
        array $filters = [],
        ?int $page = null,
    ): LengthAwarePaginator {
        $query = HoSoXuLy::query()
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->with(['trangThai', 'tthc.thanhPhanHoSos.giayTos', 'files', 'paymentHistories', 'paymentIntents']);

        if (($serviceName = trim((string) ($filters['ten_dich_vu'] ?? ''))) !== '') {
            $query->whereHas('tthc', fn ($procedure) => $procedure->where('tenTTHC', 'like', "%{$serviceName}%"));
        }

        if (($applicationCode = trim((string) ($filters['ma_ho_so'] ?? ''))) !== '') {
            $query->where('maHSXL', $applicationCode);
        }

        $status = trim((string) ($filters['trang_thai'] ?? ''));
        if ($status === 'da_hoan_thanh') {
            $query->whereNotNull('ngayKetThucXuLy');
        } elseif ($status === 'dang_xu_ly') {
            $query->whereNull('ngayKetThucXuLy');
        } elseif ($status !== '') {
            $query->where('maTrangThai', $status);
        }

        if (($dateFrom = trim((string) ($filters['date_from'] ?? ''))) !== '') {
            $query->whereDate('ngayNop', '>=', $dateFrom);
        }

        if (($dateTo = trim((string) ($filters['date_to'] ?? ''))) !== '') {
            $query->whereDate('ngayNop', '<=', $dateTo);
        }

        $direction = ($filters['sort'] ?? 'latest') === 'oldest' ? 'asc' : 'desc';

        return $query
            ->orderBy('ngayNop', $direction)
            ->orderBy('maHSXL', $direction)
            ->paginate(
                max(1, min($perPage, 100)),
                ['*'],
                'page',
                $page === null ? null : max(1, $page),
            )
            ->withQueryString();
    }

    public function findForUser(Nguoi $user, string $applicationId): HoSoXuLy
    {
        return HoSoXuLy::query()
            ->whereKey($applicationId)
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->with(['trangThai', 'tthc.thanhPhanHoSos.giayTos', 'files', 'congdan.nguoi'])
            ->firstOrFail();
    }

    public function updateDraft(HoSoXuLy $application, array $data): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $data): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if (! in_array((int) $locked->maTrangThai, [HoSoStatus::PendingPayment->value, HoSoStatus::PendingReception->value], true)) {
                throw new ApiException('Hồ sơ chỉ được chỉnh sửa khi đang chờ thanh toán hoặc chờ tiếp nhận.', 'APPLICATION_EDIT_INVALID', 409);
            }

            $stored = is_array($locked->dulieu) ? $locked->dulieu : [];
            $payload = is_array($stored['payload'] ?? null) ? $stored['payload'] : [];
            $payload = array_replace($payload, $data);
            $locked->dulieu = [...$stored, 'payload' => $payload];

            foreach ([
                'ho_ten' => 'tenChuHoSo',
                'full_name' => 'tenChuHoSo',
                'so_dien_thoai' => 'soDienThoai',
                'phone' => 'soDienThoai',
                'email' => 'email',
            ] as $input => $column) {
                if (array_key_exists($input, $data) && trim((string) $data[$input]) !== '') {
                    $locked->{$column} = trim((string) $data[$input]);
                }
            }

            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function submit(Nguoi $user, array $data, ?string $idempotencyKey = null): HoSoXuLy
    {
        $files = array_values($data['documents'] ?? []);
        $documentTypes = array_values($data['document_types'] ?? []);
        $fingerprint = trim((string) $idempotencyKey) === ''
            ? ''
            : $this->submissionFingerprint($data, $files, $documentTypes);
        $storedFiles = [];

        try {
            $application = $this->idempotency->rememberModel(
                'application',
                $user->getKey(),
                $idempotencyKey,
                $fingerprint,
                fn (string|int $id): ?HoSoXuLy => HoSoXuLy::query()->find($id),
                fn (): HoSoXuLy => DB::transaction(function () use ($user, $data, $files, $documentTypes, &$storedFiles): HoSoXuLy {
                    $procedure = TTHC::query()->findOrFail($data['procedure_id']);
                    if ($procedure->trangThai !== null && $procedure->trangThai !== 'Công khai') {
                        throw new ApiException('Thủ tục hiện không công khai.', 'PROCEDURE_UNAVAILABLE', 409);
                    }

                    $this->validateSubmissionDocuments((int) $procedure->getKey(), $files, $documentTypes);
                    $applicationData = $data['data'];
                    $applicationData['attached_files'] = $this->attachedFileMetadata($files, $documentTypes);

                    $citizen = $this->citizen($user);
                    $fees = $this->calculateFees((int) $procedure->getKey(), $data['fee_items'] ?? []);
                    $formId = DB::table('formtructuyen')->where('maTTHC', $procedure->getKey())->value('maForm');
                    $paymentMethod = $data['payment_method'] ?? 'online';
                    $initialStatus = (float) $fees['total'] > 0
                        ? HoSoStatus::PendingPayment
                        : HoSoStatus::PendingReception;

                    $application = HoSoXuLy::create([
                        'maTTHC' => $procedure->getKey(),
                        'IDCD' => $citizen->getKey(),
                        'maForm' => $formId,
                        'tenChuHoSo' => $user->hoTen,
                        'doiTuongThucHien' => null,
                        'email' => $user->email,
                        'soDienThoai' => substr((string) $user->soDienThoai, 0, 10),
                        'dulieu' => [
                            'payload' => $applicationData,
                            'fee_items' => $fees['items'],
                            'payment_method' => $paymentMethod,
                        ],
                        'ngayNop' => now(),
                        'maTrangThai' => $initialStatus->value,
                        'lePhi' => $fees['total'],
                        'hinhThuc' => $data['delivery_method'] === 'direct' ? 'Nhận trực tiếp' : 'Nhận trực tuyến',
                        'donViXuLy' => $procedure->coQuanThucHien ?: 'Bộ phận Một cửa',
                        'ngayTiepNhan' => null,
                        'ngayHenTra' => null,
                        'ngayTra' => null,
                        'hanBoSung' => null,
                        'thongTinTra' => null,
                        'ngayKetThucXuLy' => null,
                        'ghiChu' => null,
                    ]);

                    foreach ($files as $index => $file) {
                        $storedFiles[] = $this->documents->storeForDocumentType(
                            $file,
                            $application,
                            (int) $documentTypes[$index],
                            $user,
                            $this->storage,
                        );
                    }

                    return $application;
                }),
            );
        } catch (Throwable $exception) {
            $paths = [];
            foreach ($storedFiles as $file) {
                if (! TaiLieuNop::query()->whereKey($file->getKey())->exists() && $file->duongDan) {
                    $paths[] = $file->duongDan;
                }
            }

            if ($paths !== []) {
                Storage::disk(config('filesystems.private_disk', 'local'))->delete($paths);
            }

            throw $exception;
        }

        return $application->load(['trangThai', 'tthc', 'files']);
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, int|string>  $documentTypes
     */
    private function validateSubmissionDocuments(int $procedureId, array $files, array $documentTypes): void
    {
        if (count($files) !== count($documentTypes)) {
            throw new ApiException('Số loại giấy tờ phải khớp với số tệp tải lên.', 'APPLICATION_DOCUMENTS_MISMATCH', 422);
        }

        $documents = DB::table('thanhphanhoso')
            ->join('thanhphangiayto', 'thanhphangiayto.maThanhPhan', '=', 'thanhphanhoso.maThanhPhan')
            ->join('giayto', 'giayto.maGiayTo', '=', 'thanhphangiayto.maGiayTo')
            ->where('thanhphanhoso.maTTHC', $procedureId)
            ->select('giayto.maGiayTo', 'giayto.yeuCau')
            ->distinct()
            ->get();

        $mappedIds = $documents->pluck('maGiayTo')->map(static fn ($id): int => (int) $id)->all();
        $submittedIds = array_map('intval', $documentTypes);
        $invalidIds = array_values(array_diff($submittedIds, $mappedIds));
        if ($invalidIds !== []) {
            throw new ApiException('Có giấy tờ không thuộc thành phần thủ tục.', 'APPLICATION_DOCUMENT_TYPE_INVALID', 422, [
                'invalid_document_ids' => $invalidIds,
            ]);
        }

        $requiredIds = $documents
            ->where('yeuCau', 'Bắt buộc')
            ->pluck('maGiayTo')
            ->map(static fn ($id): int => (int) $id)
            ->all();
        $missingIds = array_values(array_diff($requiredIds, $submittedIds));
        if ($missingIds !== []) {
            throw new ApiException('Vui lòng tải lên đầy đủ giấy tờ bắt buộc của thủ tục.', 'APPLICATION_DOCUMENTS_INCOMPLETE', 422, [
                'missing_document_ids' => $missingIds,
            ]);
        }
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, int|string>  $documentTypes
     */
    private function submissionFingerprint(array $data, array $files, array $documentTypes): string
    {
        unset($data['documents'], $data['document_types']);
        $fileFingerprints = [];

        foreach ($files as $index => $file) {
            $hash = hash_file('sha256', $file->getPathname());
            if ($hash === false) {
                throw new ApiException('Không thể đọc tệp tải lên.', 'APPLICATION_FILE_READ_FAILED', 422);
            }

            $fileFingerprints[] = [
                'document_type' => (int) ($documentTypes[$index] ?? 0),
                'sha256' => $hash,
            ];
        }

        return hash('sha256', serialize([$data, $fileFingerprints]));
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, int|string>  $documentTypes
     * @return array<int, array<string, int|string>>
     */
    private function attachedFileMetadata(array $files, array $documentTypes): array
    {
        $names = DB::table('giayto')
            ->whereIn('maGiayTo', array_map('intval', $documentTypes))
            ->pluck('tenGiayTo', 'maGiayTo');
        $metadata = [];

        foreach ($files as $index => $file) {
            $documentType = (int) $documentTypes[$index];
            $fileName = basename($file->getClientOriginalName());
            $fileName = preg_replace('/[^A-Za-z0-9._ -]+/', '_', $fileName) ?: 'document';

            $metadata[] = [
                'document_id' => $documentType,
                'document_name' => (string) ($names[$documentType] ?? ''),
                'file_name' => $fileName,
                'file_size' => (int) round($file->getSize() / 1024).' KB',
                'file_type' => $file->getMimeType() ?: 'application/octet-stream',
            ];
        }

        return $metadata;
    }

    private function citizen(Nguoi $user): CongDan
    {
        return $user->congDan()->firstOrCreate([]);
    }

    private function calculateFees(int $procedureId, array $requestedItems): array
    {
        if ($requestedItems === []) {
            return ['total' => 0, 'items' => []];
        }

        $ids = collect($requestedItems)->pluck('id')->map(fn ($id): int => (int) $id)->unique()->values();
        $fees = DB::table('lephi')->where('maTTHC', $procedureId)->whereIn('maLePhi', $ids)->get()->keyBy('maLePhi');
        if ($fees->count() !== $ids->count()) {
            throw new ApiException('Khoản lệ phí không thuộc thủ tục.', 'FEE_ITEM_INVALID', 422);
        }

        $total = 0.0;
        $items = [];
        foreach ($requestedItems as $item) {
            $fee = $fees[(int) $item['id']];
            $quantity = (int) $item['quantity'];
            $amount = round((float) $fee->soTien * $quantity, 2);
            $total += $amount;
            $items[] = ['id' => (int) $fee->maLePhi, 'quantity' => $quantity, 'unit_amount' => (float) $fee->soTien, 'amount' => $amount];
        }

        return ['total' => $total, 'items' => $items];
    }
}
