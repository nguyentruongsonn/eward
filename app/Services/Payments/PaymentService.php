<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Enums\HoSoStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\ApiException;
use App\Models\HoSoWorkflowEvent;
use App\Models\HoSoXuLy;
use App\Models\LichSuThanhToan;
use App\Models\Nguoi;
use App\Models\PaymentIntent;
use App\Support\Idempotency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;

class PaymentService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly Idempotency $idempotency,
    ) {}

    public function findForUser(Nguoi $user, string $intentId): PaymentIntent
    {
        $intent = PaymentIntent::query()->findOrFail($intentId);
        abort_unless($intent->IDCD === $user->congDan()->value('IDCD'), 403);

        return $intent;
    }

    public function createIntent(Nguoi $user, string $applicationId, string $provider, ?string $idempotencyKey = null): PaymentIntent
    {
        $application = HoSoXuLy::query()->findOrFail($applicationId);
        abort_unless($user->congDan()->where('IDCD', $application->IDCD)->exists(), 403);
        if ((float) $application->lePhi <= 0) {
            throw new ApiException('Hồ sơ không phát sinh lệ phí.', 'PAYMENT_NOT_REQUIRED', 409);
        }

        return $this->idempotency->rememberModel(
            'payment',
            $user->getKey(),
            $idempotencyKey,
            hash('sha256', serialize([
                'application_id' => $application->getKey(),
                'provider' => $provider,
            ])),
            fn (string|int $id): ?PaymentIntent => PaymentIntent::query()->find($id),
            fn (): PaymentIntent => PaymentIntent::create([
                'IDCD' => $application->IDCD,
                'maHSXL' => $application->getKey(),
                'provider' => $provider,
                'provider_order_code' => $provider === 'payos' ? $this->nextPayOSOrderCode() : null,
                'amount' => (float) $application->lePhi,
                'status' => PaymentStatus::Pending,
                'expires_at' => now()->addMinutes(30),
            ]),
        );
    }

    public function checkout(PaymentIntent $intent): array
    {
        if ($intent->status !== PaymentStatus::Pending || ($intent->expires_at && $intent->expires_at->isPast())) {
            throw new ApiException('Payment intent đã hết hạn.', 'PAYMENT_INTENT_EXPIRED', 409);
        }

        return $this->gateway->createCheckout($intent);
    }

    public function syncPayOSPayment(Nguoi $user, int $orderCode): PaymentIntent
    {
        if ($orderCode <= 0) {
            throw new ApiException('Mã đơn PayOS không hợp lệ.', 'PAYMENT_ORDER_CODE_INVALID', 422);
        }

        $payment = $this->gateway->getPaymentStatus($orderCode);
        $intent = PaymentIntent::query()
            ->where('provider', 'payos')
            ->where('provider_order_code', $orderCode)
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->first();

        if (! $intent) {
            throw new ApiException('Không tìm thấy payment intent.', 'PAYMENT_INTENT_NOT_FOUND', 404);
        }

        if (strtoupper((string) ($payment['status'] ?? '')) !== 'PAID') {
            return $intent->fresh();
        }

        $amount = (int) ($payment['amountPaid'] ?? 0);
        if ($amount <= 0) {
            $amount = (int) ($payment['amount'] ?? 0);
        }

        return DB::transaction(function () use ($intent, $payment, $amount): PaymentIntent {
            $locked = PaymentIntent::query()->whereKey($intent->getKey())->lockForUpdate()->firstOrFail();
            $receivedCents = (int) round($amount * 100);
            $expectedCents = (int) round((float) $locked->amount * 100);
            if ($receivedCents !== $expectedCents) {
                throw new ApiException('Số tiền thanh toán không khớp.', 'PAYMENT_AMOUNT_MISMATCH', 409);
            }

            if ($locked->status === PaymentStatus::Paid) {
                return $locked->fresh();
            }

            if ($locked->status !== PaymentStatus::Pending || ($locked->expires_at && $locked->expires_at->isPast())) {
                throw new ApiException('Payment intent không còn ở trạng thái có thể thanh toán.', 'PAYMENT_INTENT_NOT_PAYABLE', 409);
            }

            return $this->settlePaidIntent($locked, (string) ($payment['providerId'] ?? $locked->provider_order_code), ['sync' => $payment]);
        });
    }

    public function confirmCounterPayment(Nguoi $actor, string $applicationId, float $amount, string $receiptNumber, ?string $note = null): PaymentIntent
    {
        return DB::transaction(function () use ($actor, $applicationId, $amount, $receiptNumber, $note): PaymentIntent {
            $application = HoSoXuLy::query()->whereKey($applicationId)->lockForUpdate()->firstOrFail();
            if ((float) $application->lePhi <= 0) {
                throw new ApiException('Hồ sơ không phát sinh lệ phí.', 'PAYMENT_NOT_REQUIRED', 409);
            }
            if ((int) round($amount * 100) !== (int) round((float) $application->lePhi * 100)) {
                throw new ApiException('Số tiền thu không khớp lệ phí hồ sơ.', 'PAYMENT_AMOUNT_MISMATCH', 409);
            }
            if ($application->hasSuccessfulPayment()) {
                throw new ApiException('Hồ sơ đã được ghi nhận thanh toán.', 'PAYMENT_ALREADY_SETTLED', 409);
            }
            if (LichSuThanhToan::query()->where('maGD', $receiptNumber)->exists()) {
                throw new ApiException('Số biên lai đã được sử dụng.', 'PAYMENT_RECEIPT_DUPLICATE', 409);
            }

            $intent = PaymentIntent::query()->where('maHSXL', $application->getKey())->where('status', PaymentStatus::Pending->value)->latest('created_at')->first();
            if (! $intent) {
                $intent = PaymentIntent::create([
                    'IDCD' => $application->IDCD,
                    'maHSXL' => $application->getKey(),
                    'provider' => 'counter',
                    'amount' => $application->lePhi,
                    'status' => PaymentStatus::Paid,
                ]);
            } else {
                $intent->provider = 'counter';
                $intent->status = PaymentStatus::Paid;
                $intent->provider_transaction_id = $receiptNumber;
                $intent->metadata = ['method' => 'counter', 'note' => $note, 'confirmed_by' => $actor->getKey()];
                $intent->save();
            }

            if ($intent->provider_transaction_id !== $receiptNumber) {
                $intent->provider_transaction_id = $receiptNumber;
                $intent->metadata = ['method' => 'counter', 'note' => $note, 'confirmed_by' => $actor->getKey()];
                $intent->save();
            }

            LichSuThanhToan::query()->create([
                'maGD' => $receiptNumber,
                'soGD' => $receiptNumber,
                'loaiGD' => 'Trực tiếp tại quầy',
                'ngayGD' => now(),
                'soTien' => $application->lePhi,
                'trangThai' => 'Thành công',
                'IDCD' => $application->IDCD,
                'maHSXL' => $application->getKey(),
                'moTa' => $note ?: 'Cán bộ xác nhận thu lệ phí trực tiếp tại quầy.',
            ]);

            $fromStatus = (int) $application->maTrangThai;
            $toStatus = $fromStatus === HoSoStatus::PendingPayment->value
                ? HoSoStatus::PendingReception->value
                : $fromStatus;
            if ($fromStatus === HoSoStatus::PendingPayment->value) {
                $application->maTrangThai = $toStatus;
                $application->save();
            }

            HoSoWorkflowEvent::create([
                'maHSXL' => $application->getKey(),
                'event_type' => 'counter_payment_confirmed',
                'actor_id' => $actor->getKey(),
                'actor_name' => $actor->hoTen,
                'actor_role' => $actor->vaiTro,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'note' => $note ?: 'Xác nhận thu lệ phí trực tiếp tại quầy.',
                'metadata' => ['receipt_number' => $receiptNumber, 'amount' => $application->lePhi],
                'created_at' => now(),
            ]);

            return $intent->fresh();
        });
    }

    public function listForUser(Nguoi $user, int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = PaymentIntent::query()
            ->whereIn('IDCD', $user->congDan()->select('IDCD'));

        $statuses = array_map(static fn (PaymentStatus $item): string => $item->value, PaymentStatus::cases());
        if ($status !== null && in_array($status, $statuses, true)) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
    }

    /**
     * Paginate the legacy transaction ledger for the authenticated citizen.
     * Payment intents remain the source of truth for new checkout flows; this
     * read model keeps the existing profile history available through REST.
     *
     * @param  array<string, mixed>  $filters
     */
    public function historyForUser(Nguoi $user, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = LichSuThanhToan::query()
            ->whereIn('IDCD', $user->congDan()->select('IDCD'));

        if (($type = trim((string) ($filters['loai_gd'] ?? ''))) !== '' && $type !== 'all') {
            $query->where('loaiGD', $type);
        }
        if (($fromDate = trim((string) ($filters['from_date'] ?? ''))) !== '') {
            $query->whereDate('ngayGD', '>=', $fromDate);
        }
        if (($toDate = trim((string) ($filters['to_date'] ?? ''))) !== '') {
            $query->whereDate('ngayGD', '<=', $toDate);
        }

        return $query
            ->orderByDesc('ngayGD')
            ->orderByDesc('id')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();
    }

    public function findLegacyTransactionForUser(Nguoi $user, int $transactionId): LichSuThanhToan
    {
        return LichSuThanhToan::query()
            ->whereKey($transactionId)
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->firstOrFail();
    }

    public function paymentStatusForUser(Nguoi $user, string $reference): ?LichSuThanhToan
    {
        $citizenId = $user->congDan()->value('IDCD');
        if ($citizenId === null) {
            return null;
        }

        return LichSuThanhToan::query()
            ->where('IDCD', $citizenId)
            ->where('maGD', $reference)
            ->latest('ngayGD')
            ->first();
    }

    public function invoice(PaymentIntent $intent): Fluent
    {
        if ($intent->status !== PaymentStatus::Paid) {
            throw new ApiException('Hóa đơn chỉ có sau khi thanh toán thành công.', 'PAYMENT_INVOICE_UNAVAILABLE', 409);
        }

        $transaction = LichSuThanhToan::query()
            ->where('maHSXL', $intent->maHSXL)
            ->where('maGD', $intent->provider_transaction_id)
            ->first();

        return new Fluent([
            'payment_intent_id' => $intent->getKey(),
            'application_id' => $intent->maHSXL,
            'transaction_id' => $intent->provider_transaction_id ?: $transaction?->maGD,
            'provider' => $intent->provider,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
            'status' => $intent->status->value,
            'paid_at' => $transaction?->ngayGD ?: $intent->updated_at,
        ]);
    }

    public function handlePayOSWebhook(array $payload, ?string $signature = null): PaymentIntent
    {
        $transaction = $this->gateway->verifyWebhook($payload, $signature);

        return DB::transaction(function () use ($transaction): PaymentIntent {
            $intent = PaymentIntent::query()
                ->where('provider', 'payos')
                ->where('provider_order_code', (int) $transaction['orderCode'])
                ->lockForUpdate()
                ->first();

            if (! $intent) {
                throw new ApiException('Không tìm thấy payment intent.', 'PAYMENT_INTENT_NOT_FOUND', 404);
            }
            $receivedCents = (int) round((float) $transaction['amount'] * 100);
            $expectedCents = (int) round((float) $intent->amount * 100);
            if ($receivedCents !== $expectedCents) {
                throw new ApiException('Số tiền thanh toán không khớp.', 'PAYMENT_AMOUNT_MISMATCH', 409);
            }

            if ($intent->status === PaymentStatus::Paid && $intent->provider_transaction_id === $transaction['providerId']) {
                return $intent->fresh();
            }
            if ($intent->status === PaymentStatus::Paid) {
                throw new ApiException('Payment intent đã được tất toán bằng giao dịch khác.', 'PAYMENT_ALREADY_SETTLED', 409);
            }
            if ($intent->status !== PaymentStatus::Pending || ($intent->expires_at && $intent->expires_at->isPast())) {
                throw new ApiException('Payment intent không còn ở trạng thái có thể thanh toán.', 'PAYMENT_INTENT_NOT_PAYABLE', 409);
            }

            return $this->settlePaidIntent($intent, (string) $transaction['providerId'], ['webhook' => $transaction['webhook'] ?? $transaction]);
        });
    }

    /** @param array<string, mixed> $metadata */
    private function settlePaidIntent(PaymentIntent $intent, string $providerTransactionId, array $metadata): PaymentIntent
    {
        $intent->status = PaymentStatus::Paid;
        $intent->provider_transaction_id = $providerTransactionId ?: $intent->provider_transaction_id;
        $intent->metadata = $metadata;
        $intent->save();

        $application = HoSoXuLy::query()->whereKey($intent->maHSXL)->lockForUpdate()->first();
        if ($application && (int) $application->maTrangThai === HoSoStatus::PendingPayment->value) {
            $application->maTrangThai = HoSoStatus::PendingReception->value;
            $application->save();

            HoSoWorkflowEvent::create([
                'maHSXL' => $application->getKey(),
                'event_type' => 'payment_confirmed',
                'actor_id' => null,
                'actor_name' => 'PayOS',
                'actor_role' => 'Cổng thanh toán',
                'from_status' => HoSoStatus::PendingPayment->value,
                'to_status' => HoSoStatus::PendingReception->value,
                'note' => 'Thanh toán đã được PayOS xác thực.',
                'metadata' => $metadata,
                'created_at' => now(),
            ]);
        }

        LichSuThanhToan::query()->updateOrCreate(
            ['maGD' => $intent->provider_transaction_id ?: $intent->getKey()],
            [
                'soGD' => $intent->provider_transaction_id,
                'loaiGD' => 'PayOS',
                'ngayGD' => now(),
                'soTien' => $intent->amount,
                'trangThai' => 'Thành công',
                'IDCD' => $intent->IDCD,
                'maHSXL' => $intent->maHSXL,
                'moTa' => 'Thanh toán PayOS đã được xác thực',
            ],
        );

        return $intent->fresh();
    }

    private function nextPayOSOrderCode(): int
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $code = random_int(100000000, 2000000000);
            if (! PaymentIntent::query()->where('provider_order_code', $code)->exists()) {
                return $code;
            }
        }

        throw new ApiException('Không thể tạo mã đơn PayOS duy nhất.', 'PAYMENT_ORDER_CODE_UNAVAILABLE', 503);
    }
}
