<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'full_name' => $this->hoTen,
            'email' => $this->email,
            'phone' => $this->soDienThoai,
            'citizen_id' => $this->maCCCD,
            'role' => trim((string) $this->vaiTro),
        ];
    }
}
