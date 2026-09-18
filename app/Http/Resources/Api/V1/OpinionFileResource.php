<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpinionFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => (int) $this->size,
            'uploaded_by' => $this->uploaded_by,
            'uploaded_at' => optional($this->created_at)->toIso8601String(),
            'download_url' => route('api.v1.admin.application.opinion-files.show', [
                'application' => $this->maHSXL,
                'file' => $this->getKey(),
            ]),
        ];
    }
}
