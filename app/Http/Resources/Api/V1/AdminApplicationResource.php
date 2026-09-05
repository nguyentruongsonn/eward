<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Nguoi;
use App\Services\Workflow\HoSoWorkflowService;
use Illuminate\Http\Request;

class AdminApplicationResource extends ApplicationResource
{
    public function toArray(Request $request): array
    {
        $signatures = $this->file_signatures;
        if (is_string($signatures)) {
            $signatures = json_decode($signatures, true);
        }

        /** @var Nguoi|null $actor */
        $actor = $request->user('api');
        $components = $this->tthc?->thanhPhanHoSos ?? collect();

        return [
            ...parent::toArray($request),
            'applicant_email' => $this->email ?: $this->congdan?->nguoi?->email,
            'applicant_phone' => $this->soDienThoai ?: $this->congdan?->nguoi?->soDienThoai,
            'applicant_id_card' => $this->congdan?->nguoi?->maCCCD ?? null,
            'applicant_address' => $this->congdan?->nguoi?->noiThuongTru ?? null,
            'procedure_field' => $this->tthc?->linhVuc?->tenLinhVuc ?? null,
            'form_config' => $this->tthc?->formConfig?->cauHinhForm ?? null,
            'department' => $this->donViXuLy,
            'receiver_id' => $this->nguoiTiepNhan,
            'receiver_name' => $this->receiver?->hoTen ?? null,
            'approver_id' => $this->nguoiDuyet,
            'approver_name' => $this->approver?->hoTen ?? null,
            'notes' => $this->ghiChu,
            'approval_opinion' => $this->yKienDuyet,
            'processing_opinion' => $this->yKienXuLy,
            'approval_date' => optional($this->ngayDuyet)->toIso8601String(),
            'result_file_signatures' => $signatures ?: null,
            'allowed_actions' => $actor ? HoSoWorkflowService::availableActions($actor, $this->resource) : [],
            'timeline' => $this->buildTimeline(),
            'files' => $this->when($this->relationLoaded('files'), fn (): array => $this->files->map(fn ($file): array => [
                'id' => $file->taiLieuID,
                'document_type_id' => $file->maGiayTo,
                'name' => $file->tenTep,
                'mime_type' => $file->dinhDang,
                'size' => (int) $file->kichThuoc,
                'uploaded_at' => optional($file->ngayTai)->toIso8601String(),
                'download_url' => route('api.v1.admin.application.component-files.show', ['application' => $this->maHSXL, 'file' => $file->taiLieuID]),
            ])->values()->all()),
            'procedure_components' => $this->when($this->tthc?->relationLoaded('thanhPhanHoSos'), fn (): array => $components->flatMap(fn ($component) => $component->giayTos->map(fn ($document): array => [
                'component_id' => $component->getKey(),
                'component_name' => $component->tenThanhPhan,
                'id' => $document->getKey(),
                'name' => $document->tenGiayTo,
                'required' => $document->yeuCau,
            ]))->values()->all()),
        ];
    }

    private function buildTimeline(): array
    {
        $timeline = [
            [
                'label' => $this->trangThai?->tenTrangThai ?? 'Đang cập nhật',
                'timestamp' => optional($this->ngayTra ?? $this->ngayDuyet ?? $this->updated_at ?? $this->ngayTiepNhan)->toIso8601String(),
                'current' => true,
                'actor' => $this->approver?->hoTen ?? $this->receiver?->hoTen,
            ],
        ];

        if ($this->ngayTiepNhan) {
            $timeline[] = [
                'label' => 'Tiếp nhận hồ sơ',
                'timestamp' => optional($this->ngayTiepNhan)->toIso8601String(),
                'current' => false,
                'actor' => $this->receiver?->hoTen,
            ];
        }

        if (! empty($this->ghiChu)) {
            $lines = array_filter(array_map('trim', explode("\n", (string) $this->ghiChu)));
            foreach ($lines as $line) {
                if (preg_match('/^\[(.*?)\]\s*(.*)$/u', $line, $m)) {
                    $ts = $m[1];
                    $rest = $m[2];
                    $actor = null;
                    $label = $rest;
                    if (str_contains($rest, ':')) {
                        [$partActor, $partContent] = explode(':', $rest, 2);
                        $actor = trim($partActor);
                        $label = trim($partContent);
                    }
                    $timeline[] = [
                        'label' => $label,
                        'timestamp' => $ts,
                        'current' => false,
                        'actor' => $actor,
                    ];
                }
            }
        }

        if ($this->ngayDuyet) {
            $timeline[] = [
                'label' => 'Phê duyệt hồ sơ',
                'timestamp' => optional($this->ngayDuyet)->toIso8601String(),
                'current' => false,
                'actor' => $this->approver?->hoTen,
            ];
        }

        if ($this->ngayTra) {
            $timeline[] = [
                'label' => 'Trả kết quả cho công dân',
                'timestamp' => optional($this->ngayTra)->toIso8601String(),
                'current' => false,
                'actor' => null,
            ];
        }

        return $timeline;
    }
}
