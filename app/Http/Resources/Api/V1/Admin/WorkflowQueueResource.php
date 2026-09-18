<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Models\Nguoi;
use App\Services\Workflow\HoSoWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowQueueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Nguoi|null $actor */
        $actor = $request->user('api');

        return [
            'id' => (string) $this->maHSXL,
            'procedure_id' => (int) $this->maTTHC,
            'procedure_name' => $this->whenLoaded('tthc', fn (): ?string => $this->tthc?->tenTTHC),
            'applicant_name' => $this->tenChuHoSo,
            'status' => [
                'id' => (int) $this->maTrangThai,
                'label' => $this->whenLoaded('trangThai', fn (): ?string => $this->trangThai?->tenTrangThai),
            ],
            'received_at' => optional($this->ngayTiepNhan)->toIso8601String(),
            'due_at' => optional($this->ngayHenTra)->toIso8601String(),
            'fee' => (float) $this->lePhi,
            'payment_status' => [
                'fee' => (float) $this->lePhi,
                'is_free' => (float) $this->lePhi <= 0,
                'is_paid' => $this->hasSuccessfulPayment(),
                'method' => data_get($this->dulieu, 'payment_method'),
                'label' => (float) $this->lePhi <= 0 ? 'Miễn phí' : ($this->hasSuccessfulPayment() ? 'Đã thanh toán' : 'Chưa thanh toán'),
            ],
            'allowed_actions' => $this->allowedActions($actor),
        ];
    }

    /** @return list<string> */
    private function allowedActions(?Nguoi $actor): array
    {
        if (! $actor) {
            return [];
        }

        return HoSoWorkflowService::availableActions($actor, $this->resource);
    }
}
