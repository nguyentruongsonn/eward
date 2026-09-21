<?php

namespace App\Contracts\Payments;

use App\Models\PaymentIntent;

interface PaymentGateway
{
    public function createCheckout(PaymentIntent $intent): array;

    public function verifyWebhook(array $payload, ?string $signature = null): array;
}
