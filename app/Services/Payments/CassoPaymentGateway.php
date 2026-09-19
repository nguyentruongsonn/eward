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

    public function verifyWebhook(array $payload, ?string $signature, ?string $secureToken = null): array
    {
        $expected = (string) config('services.casso.webhook_secret');
        if (! $this->isValidWebhookSignature($payload, $expected, $signature, $secureToken)) {
            throw new ApiException('Webhook không hợp lệ.', 'PAYMENT_WEBHOOK_UNAUTHORIZED', 401);
        }

        $transaction = $payload['data'] ?? $payload;
        if (! is_array($transaction)) {
            throw new ApiException('Webhook thiếu dữ liệu giao dịch.', 'PAYMENT_WEBHOOK_INVALID', 422);
        }
        if (array_is_list($transaction)) {
            $transaction = $transaction[0] ?? [];
        }
        $amount = (float) ($transaction['amount'] ?? 0);
        $reference = (string) ($transaction['description'] ?? $transaction['reference'] ?? '');
        $providerId = (string) ($transaction['tid'] ?? $transaction['reference'] ?? $transaction['id'] ?? '');
        if ($reference === '' || $providerId === '') {
            throw new ApiException('Webhook thiếu mã tham chiếu hoặc mã giao dịch.', 'PAYMENT_WEBHOOK_INVALID', 422);
        }

        return compact('amount', 'reference', 'providerId');
    }

    private function isValidWebhookSignature(array $payload, string $expected, ?string $signature, ?string $secureToken): bool
    {
        if ($expected === '') {
            return false;
        }
        if ($secureToken !== null) {
            return hash_equals($expected, $secureToken);
        }
        if ($signature === null) {
            return false;
        }
        if (preg_match('/^t=(\d+),v1=([a-f0-9]+)$/i', $signature, $matches) === 1) {
            $timestamp = $matches[1];
            $received = $matches[2];
            $sortedPayload = $this->sortWebhookData($payload);
            $encodedPayload = json_encode($sortedPayload, JSON_UNESCAPED_SLASHES);
            if ($encodedPayload === false) {
                return false;
            }
            $generated = hash_hmac('sha512', $timestamp.'.'.$encodedPayload, $expected);

            return hash_equals($generated, $received);
        }

        return hash_equals($expected, $signature);
    }

    private function sortWebhookData(array $data): array
    {
        if (array_is_list($data)) {
            return array_map(fn (mixed $value): mixed => is_array($value) ? $this->sortWebhookData($value) : $value, $data);
        }

        ksort($data);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sortWebhookData($value);
            }
        }

        return $data;
    }
}
