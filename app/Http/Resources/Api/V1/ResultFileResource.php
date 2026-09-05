<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $route = $request->is('api/v1/admin/*')
            ? 'api.v1.admin.application.result-files.show'
            : 'api.v1.citizen.application.result-files.show';

        return [
            'id' => $this->getKey(),
            'name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => (int) $this->size,
            'uploaded_by' => $this->uploaded_by,
            'uploaded_at' => optional($this->created_at)->toIso8601String(),
            'download_url' => route($route, [
                'application' => $this->maHSXL,
                'file' => $this->getKey(),
            ]),
        ];
    }
}
