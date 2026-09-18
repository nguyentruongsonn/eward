<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->maLichHen,
            'procedure_id' => $this->maTTHC,
            'procedure_name' => $this->whenLoaded('tthc', fn () => $this->tthc?->tenTTHC),
            'scheduled_at' => optional($this->thoiGianHen)->toIso8601String(),
            'status' => $this->trangThai,
            'counter_id' => $this->maQuayLamViec,
            'queue_number' => $this->soThuTu,
            'checked_in_at' => optional($this->checkin_time)->toIso8601String(),
            'checkin_token' => $this->checkin_token,
        ];
    }
}
