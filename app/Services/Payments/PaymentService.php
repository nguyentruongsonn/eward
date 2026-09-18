<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Exceptions\ApiException;
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

    public function handleCassoWebhook(array $payload, ?string $signature): PaymentIntent
    {
        $transaction = $this->gateway->verifyWebhook($payload, $signature);

        return DB::transaction(function () use ($transaction): PaymentIntent {
            $intent = PaymentIntent::query()
                ->where('provider', 'casso')
                ->where(function ($query) use ($transaction): void {
                    $query->whereKey($transaction['reference'])
                        ->orWhere('provider_transaction_id', $transaction['providerId']);
                })
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

            if ($intent->status === PaymentStatus::Paid) {
                if ($intent->provider_transaction_id === $transaction['providerId']) {
                    return $intent->fresh();
                }

                throw new ApiException('Payment intent đã được tất toán bằng giao dịch khác.', 'PAYMENT_ALREADY_SETTLED', 409);
            }
            if ($intent->status !== PaymentStatus::Pending || ($intent->expires_at && $intent->expires_at->isPast())) {
                throw new ApiException('Payment intent không còn ở trạng thái có thể thanh toán.', 'PAYMENT_INTENT_NOT_PAYABLE', 409);
            }

            $intent->status = PaymentStatus::Paid;
            $intent->provider_transaction_id = $transaction['providerId'] ?: $intent->provider_transaction_id;
            $intent->metadata = ['webhook' => $transaction];
            $intent->save();

            LichSuThanhToan::query()->updateOrCreate(
                ['maGD' => $intent->provider_transaction_id ?: $intent->getKey()],
                [
                    'soGD' => $intent->provider_transaction_id,
                    'loaiGD' => 'Casso',
                    'ngayGD' => now(),
                    'soTien' => $intent->amount,
                    'trangThai' => 'Thành công',
                    'IDCD' => $intent->IDCD,
                    'maHSXL' => $intent->maHSXL,
                    'moTa' => 'Webhook thanh toán đã xác thực',
                ],
            );

            return $intent->fresh();
        });
    }
}
