<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->taiLieuID,
            'document_type_id' => $this->maGiayTo,
            'name' => $this->tenTep,
            'mime_type' => $this->dinhDang,
            'size' => (int) $this->kichThuoc,
            'uploaded_at' => optional($this->ngayTai)->toIso8601String(),
            'download_url' => route('api.v1.citizen.application.files.show', [
                'application' => $this->maHSXL,
                'file' => $this->taiLieuID,
            ]),
        ];
    }
}
