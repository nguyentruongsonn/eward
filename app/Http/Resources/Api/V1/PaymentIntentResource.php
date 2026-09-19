<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentIntentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_id' => $this->maHSXL,
            'provider' => $this->provider,
            'order_code' => $this->provider_order_code,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'transaction_id' => $this->provider_transaction_id,
            'payment_method' => $this->provider === 'counter' ? 'direct' : 'online',
            'expires_at' => optional($this->expires_at)->toIso8601String(),
        ];
    }
}
