<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Exceptions\ApiException;
use App\Models\PaymentIntent;

class CassoPaymentGateway implements PaymentGateway
{
    public function createCheckout(PaymentIntent $intent): array
    {
        $account = config('services.vietqr.account_no');
        if (! $account) {
            throw new ApiException('Cổng thanh toán chưa được cấu hình.', 'PAYMENT_NOT_CONFIGURED', 503);
        }

        return [
            'provider' => 'casso',
            'amount' => (float) $intent->amount,
            'reference' => $intent->id,
            'qr_url' => 'https://img.vietqr.io/image/'.rawurlencode((string) config('services.vietqr.bank_id')).'-'.rawurlencode((string) $account).'-compact2.png?amount='.(int) $intent->amount.'&addInfo='.rawurlencode($intent->id),
        ];
    }

    public function verifyWebhook(array $payload, ?string $signature): array
    {
        $expected = (string) config('services.casso.webhook_secret');
        if ($expected === '' || $signature === null || ! hash_equals($expected, $signature)) {
            throw new ApiException('Webhook không hợp lệ.', 'PAYMENT_WEBHOOK_UNAUTHORIZED', 401);
        }

        $transaction = $payload['data'] ?? $payload;
        $amount = (float) ($transaction['amount'] ?? 0);
        $reference = (string) ($transaction['description'] ?? $transaction['reference'] ?? '');
        $providerId = (string) ($transaction['id'] ?? $transaction['tid'] ?? '');
        if ($reference === '' || $providerId === '') {
            throw new ApiException('Webhook thiếu mã tham chiếu hoặc mã giao dịch.', 'PAYMENT_WEBHOOK_INVALID', 422);
        }

        return compact('amount', 'reference', 'providerId');
    }
}
