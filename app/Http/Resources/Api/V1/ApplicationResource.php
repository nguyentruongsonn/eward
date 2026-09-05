<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $supplementRequest = $this->yeu_cau_bo_sung;
        if (is_string($supplementRequest)) {
            $supplementRequest = json_decode($supplementRequest, true);
        }

        return [
            'id' => $this->maHSXL,
            'procedure_id' => $this->maTTHC,
            'procedure_name' => $this->whenLoaded('tthc', fn () => $this->tthc?->tenTTHC),
            'status' => [
                'id' => (int) $this->maTrangThai,
                'name' => $this->whenLoaded('trangThai', fn () => $this->trangThai?->tenTrangThai),
            ],
            'applicant_name' => $this->tenChuHoSo,
            'received_at' => optional($this->ngayTiepNhan)->toDateString(),
            'due_at' => optional($this->ngayHenTra)->toDateString(),
            'delivered_at' => optional($this->ngayTra)->toDateString(),
            'fee' => (float) $this->lePhi,
            'payment_status' => [
                'fee' => (float) $this->lePhi,
                'is_free' => (float) $this->lePhi <= 0,
                'is_paid' => (float) $this->lePhi <= 0 || ($this->relationLoaded('paymentHistories') && $this->paymentHistories->contains(fn ($p) => $p->trangThai === 'Thành công')),
                'label' => ((float) $this->lePhi <= 0) ? 'Miễn phí' : (($this->relationLoaded('paymentHistories') && $this->paymentHistories->contains(fn ($p) => $p->trangThai === 'Thành công')) ? 'Đã thanh toán' : 'Chưa nộp phí'),
            ],
            'delivery_method' => $this->hinhThuc,
            'data' => $this->dulieu,
            'supplement_request' => $this->when(
                (int) $this->maTrangThai === 5 && is_array($supplementRequest),
                fn () => [
                    'document_ids' => array_values(array_map('intval', (array) ($supplementRequest['document_ids'] ?? $supplementRequest['giayto'] ?? []))),
                    'documents' => array_values((array) ($supplementRequest['documents'] ?? $supplementRequest['giayto_names'] ?? [])),
                    'note' => $supplementRequest['note'] ?? $supplementRequest['ghi_chu'] ?? null,
                    'requested_by' => isset($supplementRequest['requested_by']) ? (int) $supplementRequest['requested_by'] : null,
                    'requested_at' => $supplementRequest['requested_at'] ?? null,
                ],
            ),
            'result_files' => $this->when(
                $this->relationLoaded('resultFiles')
                    && ($request->is('api/v1/admin/*') || (int) $this->maTrangThai === 10),
                fn () => ResultFileResource::collection($this->resultFiles),
            ),
        ];
    }
}
