<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'payment_intent_id' => $this->payment_intent_id,
            'application_id' => $this->application_id,
            'transaction_id' => $this->transaction_id,
            'provider' => $this->provider,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'paid_at' => optional($this->paid_at)->toIso8601String(),
        ];
    }
}
