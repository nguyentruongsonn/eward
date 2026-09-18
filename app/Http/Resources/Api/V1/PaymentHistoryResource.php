<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_code' => $this->maGD,
            'order_number' => $this->soGD,
            'type' => $this->loaiGD,
            'occurred_at' => optional($this->ngayGD)->toIso8601String(),
            'amount' => $this->soTien === null ? null : (float) $this->soTien,
            'status' => $this->trangThai,
            'application_id' => $this->maHSXL,
            'description' => $this->moTa,
        ];
    }
}
