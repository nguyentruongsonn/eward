<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'application_id' => $this->maHSXL,
            'score' => (int) $this->soDiem,
            'comment' => $this->nhanXet,
            'rated_at' => optional($this->ngayDanhGia)->toIso8601String(),
        ];
    }
}
