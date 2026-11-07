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

        $application = $this->relationLoaded('application') ? $this->application : $this->application()->first();
        $signatures = is_array($application?->file_signatures)
            ? $application->file_signatures
            : (json_decode((string) ($application?->file_signatures ?? ''), true) ?: []);
        $signatureKey = 'result:'.$this->getKey();
        $sig = $signatures[$signatureKey] ?? null;

        return [
            'id' => $this->getKey(),
            'name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => (int) $this->size,
            'uploaded_by' => $this->uploaded_by,
            'uploaded_at' => optional($this->created_at)->toIso8601String(),
            'signed' => $sig !== null,
            'signed_at' => $sig['signed_at'] ?? null,
            'signed_by' => $sig['signed_by'] ?? null,
            'signature_method' => $sig['signature_method'] ?? null,
            'download_url' => route($route, [
                'application' => $this->maHSXL,
                'file' => $this->getKey(),
            ]),
        ];
    }
}
