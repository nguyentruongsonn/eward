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

        $isPaid = $this->hasSuccessfulPayment();
        $paymentHistory = $this->relationLoaded('paymentHistories')
            ? $this->paymentHistories->first(fn ($payment): bool => $payment->trangThai === 'Thành công')
            : $this->paymentHistories()->where('trangThai', 'Thành công')->latest('ngayGD')->first();
        $paymentIntent = $this->relationLoaded('paymentIntents')
            ? $this->paymentIntents->first(fn ($intent): bool => $intent->status?->value === 'paid')
            : $this->paymentIntents()->where('status', 'paid')->latest('created_at')->first();
        $paymentMethod = data_get($this->dulieu, 'payment_method');

        return [
            'id' => $this->maHSXL,
            'procedure_id' => $this->maTTHC,
            'procedure_name' => $this->whenLoaded('tthc', fn () => $this->tthc?->tenTTHC),
            'status' => [
                'id' => (int) $this->maTrangThai,
                'name' => $this->whenLoaded('trangThai', fn () => $this->trangThai?->tenTrangThai),
            ],
            'applicant_name' => $this->tenChuHoSo,
            'submitted_at' => optional($this->ngayNop)->toIso8601String(),
            'received_at' => optional($this->ngayTiepNhan)->toDateString(),
            'due_at' => optional($this->ngayHenTra)->toDateString(),
            'delivered_at' => optional($this->ngayTra)->toDateString(),
            'fee' => (float) $this->lePhi,
            'payment_status' => [
                'fee' => (float) $this->lePhi,
                'is_free' => (float) $this->lePhi <= 0,
                'is_paid' => $isPaid,
                'method' => $paymentMethod,
                'transaction_code' => $paymentHistory?->maGD ?: $paymentIntent?->provider_transaction_id,
                'label' => ((float) $this->lePhi <= 0) ? 'Miễn phí' : ($isPaid ? 'Đã thanh toán' : ($paymentMethod === 'direct' ? 'Chờ thanh toán tại quầy' : 'Chưa thanh toán')),
            ],
            'delivery_method' => $this->hinhThuc,
            'data' => $this->dulieu,
            'files' => $this->when($this->relationLoaded('files'), fn (): array => FileResource::collection($this->files)->resolve($request)),
            'procedure_components' => $this->when(
                $this->tthc?->relationLoaded('thanhPhanHoSos'),
                fn (): array => $this->tthc->thanhPhanHoSos->flatMap(fn ($component) => $component->giayTos->map(fn ($document): array => [
                    'component_id' => $component->getKey(),
                    'component_name' => $component->tenThanhPhan,
                    'id' => $document->getKey(),
                    'name' => $document->tenGiayTo,
                    'type' => $document->loaiGiayTo,
                    'required' => $document->yeuCau,
                ]))->values()->all(),
            ),
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
