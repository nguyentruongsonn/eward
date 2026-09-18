<?php

namespace App\Http\Resources\Api\V1\Citizen;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'full_name' => $this->hoTen,
            'email' => $this->email,
            'phone' => $this->soDienThoai,
            'citizen_id' => $this->maCCCD,
            'gender' => $this->gioiTinh,
            'birth_date' => $this->ngaySinh,
            'hometown' => $this->queQuan,
            'permanent_address' => $this->noiThuongTru,
            'temporary_address' => $this->noiTamTru,
            'role' => trim((string) $this->vaiTro),
        ];
    }
}
