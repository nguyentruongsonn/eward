<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminStaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->IDCB ?? ($this->resource->IDnguoiDung ?? null),
            'IDCB' => $this->resource->IDCB ?? null,
            'user_id' => $this->resource->IDnguoiDung ?? null,
            'hoTen' => $this->hoTen,
            'email' => $this->email,
            'soDienThoai' => $this->soDienThoai,
            'maCCCD' => $this->maCCCD,
            'vaiTro' => trim((string) $this->vaiTro),
            'chucVu' => $this->resource->chucVu ?? null,
            'maQuayLamViec' => $this->maQuayLamViec,
            'tenQuayLamViec' => $this->tenQuayLamViec ?? null,
            'gioiTinh' => $this->gioiTinh ?? null,
            'ngaySinh' => $this->ngaySinh ?? null,
            'queQuan' => $this->queQuan ?? null,
            'noiThuongTru' => $this->noiThuongTru ?? null,
            'noiTamTru' => $this->noiTamTru ?? null,
        ];
    }
}
