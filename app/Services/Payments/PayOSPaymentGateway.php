<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGateway;
use App\Exceptions\ApiException;
use App\Models\PaymentIntent;
use PayOS\Exceptions\WebhookException;
use PayOS\PayOS;
use Throwable;

class PayOSPaymentGateway implements PaymentGateway
{
    public function createCheckout(PaymentIntent $intent): array
    {
        $payos = $this->client();
        $orderCode = (int) $intent->provider_order_code;
        if ($orderCode <= 0) {
            throw new ApiException('Payment intent chưa có mã đơn PayOS.', 'PAYMENT_ORDER_CODE_MISSING', 409);
        }

        $amount = (int) round((float) $intent->amount);
        $payload = [
            'orderCode' => $orderCode,
            'amount' => $amount,
            'description' => 'EWARD '.$orderCode,
            'cancelUrl' => (string) config('services.payos.cancel_url'),
            'returnUrl' => (string) config('services.payos.return_url'),
            'items' => [[
                'name' => 'Lệ phí hồ sơ '.$intent->maHSXL,
                'quantity' => 1,
                'price' => $amount,
            ]],
            'expiredAt' => $intent->expires_at?->timestamp,
        ];

        try {
            $response = $payos->paymentRequests->create($payload, ['asArray' => true]);
        } catch (Throwable $error) {
            report($error);
            throw new ApiException('Không thể tạo liên kết thanh toán PayOS.', 'PAYMENT_PROVIDER_UNAVAILABLE', 502);
        }

        $response = is_array($response) ? $response : get_object_vars($response);
        $checkoutUrl = (string) ($response['checkoutUrl'] ?? '');
        $qrCode = (string) ($response['qrCode'] ?? '');
        $paymentLinkId = (string) ($response['paymentLinkId'] ?? '');
        if ($checkoutUrl === '' || $qrCode === '' || $paymentLinkId === '') {
            throw new ApiException('PayOS trả về dữ liệu thanh toán không đầy đủ.', 'PAYMENT_PROVIDER_INVALID_RESPONSE', 502);
        }

        return [
            'provider' => 'payos',
            'amount' => $amount,
            'reference' => (string) $orderCode,
            'order_code' => $orderCode,
            'checkout_url' => $checkoutUrl,
            'qr_code' => $qrCode,
            'payment_link_id' => $paymentLinkId,
            'expires_at' => $intent->expires_at?->toIso8601String(),
        ];
    }

    public function getPaymentStatus(int $orderCode): array
    {
        if ($orderCode <= 0) {
            throw new ApiException('Mã đơn PayOS không hợp lệ.', 'PAYMENT_ORDER_CODE_INVALID', 422);
        }

        try {
            $response = $this->client()->paymentRequests->get($orderCode, ['asArray' => true]);
        } catch (ApiException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);
            throw new ApiException('Không thể kiểm tra trạng thái thanh toán PayOS.', 'PAYMENT_PROVIDER_UNAVAILABLE', 502);
        }

        $response = is_array($response) ? $response : get_object_vars($response);
        $transactions = $response['transactions'] ?? [];
        $latestTransaction = [];
        if (is_array($transactions) && $transactions !== []) {
            $latestTransaction = (array) end($transactions);
        }

        return [
            'orderCode' => (int) ($response['orderCode'] ?? $orderCode),
            'status' => strtoupper((string) ($response['status'] ?? '')),
            'amount' => (int) ($response['amount'] ?? 0),
            'amountPaid' => (int) ($response['amountPaid'] ?? 0),
            'providerId' => (string) ($latestTransaction['reference'] ?? $latestTransaction['transactionId'] ?? $response['paymentLinkId'] ?? $response['id'] ?? ''),
            'payment' => $response,
        ];
    }

    public function verifyWebhook(array $payload, ?string $signature = null): array
    {
        if (($payload['signature'] ?? null) === null && $signature !== null) {
            $payload['signature'] = $signature;
        }

        try {
            $verified = $this->client()->webhooks->verify($payload, ['asArray' => true]);
        } catch (ApiException $error) {
            throw $error;
        } catch (WebhookException $error) {
            throw new ApiException('Webhook PayOS không hợp lệ.', 'PAYMENT_WEBHOOK_UNAUTHORIZED', 401);
        } catch (Throwable $error) {
            report($error);
            throw new ApiException('Không thể xác thực webhook PayOS.', 'PAYMENT_WEBHOOK_INVALID', 422);
        }

        $verified = is_array($verified) ? $verified : get_object_vars($verified);
        $orderCode = (int) ($verified['orderCode'] ?? 0);
        $amount = (int) ($verified['amount'] ?? 0);
        $providerId = (string) ($verified['reference'] ?? $verified['paymentLinkId'] ?? '');
        if ($orderCode <= 0 || $amount <= 0 || $providerId === '') {
            throw new ApiException('Webhook PayOS thiếu mã đơn, số tiền hoặc mã giao dịch.', 'PAYMENT_WEBHOOK_INVALID', 422);
        }

        return [
            'amount' => $amount,
            'reference' => (string) $orderCode,
            'providerId' => $providerId,
            'orderCode' => $orderCode,
            'webhook' => $verified,
        ];
    }

    private function client(): PayOS
    {
        $clientId = (string) config('services.payos.client_id');
        $apiKey = (string) config('services.payos.api_key');
        $checksumKey = (string) config('services.payos.checksum_key');
        if ($clientId === '' || $apiKey === '' || $checksumKey === '') {
            throw new ApiException('Cổng PayOS chưa được cấu hình.', 'PAYMENT_NOT_CONFIGURED', 503);
        }

        return new PayOS(
            $clientId,
            $apiKey,
            $checksumKey,
            null,
            (string) config('services.payos.api_url', 'https://api-merchant.payos.vn'),
        );
    }
}
