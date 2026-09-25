<?php

namespace App\Contracts\Payments;

use App\Models\PaymentIntent;

interface PaymentGateway
{
    public function createCheckout(PaymentIntent $intent): array;

    /** @return array<string, mixed> */
    public function getPaymentStatus(int $orderCode): array;

    public function verifyWebhook(array $payload, ?string $signature = null): array;
}
