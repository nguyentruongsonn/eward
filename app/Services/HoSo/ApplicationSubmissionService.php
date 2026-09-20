<?php

namespace App\Services\HoSo;

use App\Enums\HoSoStatus;
use App\Exceptions\ApiException;
use App\Models\CongDan;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Models\TrangThaiHoSo;
use App\Models\TTHC;
use App\Support\Idempotency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ApplicationSubmissionService
{
    public function __construct(private readonly Idempotency $idempotency) {}

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

        return $query
            ->orderByDesc('ngayTiepNhan')
            ->orderByDesc('maHSXL')
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
        $fingerprint = hash('sha256', serialize($data));
        $application = $this->idempotency->rememberModel(
            'application',
            $user->getKey(),
            $idempotencyKey,
            $fingerprint,
            fn (string|int $id): ?HoSoXuLy => HoSoXuLy::query()->find($id),
            fn (): HoSoXuLy => DB::transaction(function () use ($user, $data): HoSoXuLy {
                $procedure = TTHC::query()->findOrFail($data['procedure_id']);
                if ($procedure->trangThai !== null && $procedure->trangThai !== 'Công khai') {
                    throw new ApiException('Thủ tục hiện không công khai.', 'PROCEDURE_UNAVAILABLE', 409);
                }

                $citizen = $this->citizen($user);
                $fees = $this->calculateFees((int) $procedure->getKey(), $data['fee_items'] ?? []);
                $formId = DB::table('formtructuyen')->where('maTTHC', $procedure->getKey())->value('maForm');
                $paymentMethod = $data['payment_method'] ?? 'online';
                $initialStatus = (float) $fees['total'] > 0
                    ? HoSoStatus::PendingPayment
                    : HoSoStatus::PendingReception;

                return HoSoXuLy::create([
                    'maTTHC' => $procedure->getKey(),
                    'IDCD' => $citizen->getKey(),
                    'maForm' => $formId,
                    'tenChuHoSo' => $user->hoTen,
                    'doiTuongThucHien' => null,
                    'email' => $user->email,
                    'soDienThoai' => substr((string) $user->soDienThoai, 0, 10),
                    'dulieu' => [
                        'payload' => $data['data'],
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
            }),
        );

        return $application->load(['trangThai', 'tthc']);
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
