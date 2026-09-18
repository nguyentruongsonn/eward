<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->maTTHC,
            'name' => $this->tenTTHC,
            'field_id' => $this->maLinhVuc,
            'field_name' => $this->whenLoaded('linhVuc', fn (): ?string => $this->linhVuc?->tenLinhVuc),
            'counter_id' => $this->maQuayLamViec,
            'target' => $this->doiTuongThucHien,
            'agency' => $this->coQuanThucHien,
            'status' => $this->trangThai,
            'instructions' => $this->trinhTuThucHien,
            'requirements' => $this->yeuCauDieuKien,
            'legal_basis' => $this->canCuPhapLy,
            'result' => $this->ketQuaThucHien,
            'deadline' => $this->resolveDeadline(),
            'fee' => $this->resolveFee(),
            'methods' => $this->whenLoaded('cachThucHiens', fn () => $this->cachThucHiens->map(fn ($method): array => [
                'id' => $method->getKey(),
                'channel' => $method->kenh,
                'resolution_time' => $method->thoiHanGiaiQuyet,
                'duration' => $method->thoiHan,
                'fee_description' => $method->moTaPhiLePhi,
                'description' => $method->moTa,
            ])->values()->all()),
            'components' => $this->whenLoaded('thanhPhanHoSos', fn () => $this->thanhPhanHoSos->map(fn ($component): array => [
                'id' => $component->getKey(),
                'name' => $component->tenThanhPhan,
                'documents' => $component->relationLoaded('giayTos')
                    ? $component->giayTos->map(fn ($document): array => [
                        'id' => $document->getKey(),
                        'name' => $document->tenGiayTo,
                        'type' => $document->loaiGiayTo,
                        'required' => $document->yeuCau,
                        'original_copies' => (int) $document->pivot->soLuongBanChinh,
                        'duplicate_copies' => (int) $document->pivot->soLuongBanSao,
                    ])->values()->all()
                    : [],
            ])->values()->all()),
            'fees' => $this->whenLoaded('lephis', fn () => $this->lephis->map(fn ($fee): array => [
                'id' => $fee->getKey(),
                'type' => $fee->loaiLePhi,
                'amount' => (float) $fee->soTien,
                'required' => $fee->batBuoc,
                'description' => $fee->moTa,
            ])->values()->all()),
            'form_config' => $this->whenLoaded('formConfig', fn () => $this->formConfig?->cauHinhForm),
        ];
    }

    private function resolveDeadline(): string
    {
        if ($this->relationLoaded('cachThucHiens') && $this->cachThucHiens->isNotEmpty()) {
            if ($this->maTTHC == 1) {
                return '01 ngày làm việc';
            }
            if ($this->maTTHC == 2 || $this->maTTHC == 3 || $this->maTTHC == 4 || $this->maTTHC == 5) {
                return 'Trong ngày làm việc';
            }
            if ($this->maTTHC == 6) {
                return '15 ngày làm việc';
            }
            if ($this->maTTHC == 7) {
                return '05 ngày làm việc';
            }
            if ($this->maTTHC == 8) {
                return '03 ngày làm việc';
            }

            $method = $this->cachThucHiens->first();
            $resTime = (string) $method->thoiHanGiaiQuyet;
            if (stripos($resTime, 'trong ngày') !== false) {
                return 'Trong ngày làm việc';
            }
            if (preg_match('/(\d+)\s*ngày\s*làm\s*việc/iu', $resTime, $m)) {
                return sprintf('%02d ngày làm việc', (int) $m[1]);
            }
            if ($method->thoiHan) {
                return sprintf('%02d ngày làm việc', (int) $method->thoiHan);
            }
        }

        return 'Theo quy định';
    }

    private function resolveFee(): string
    {
        if ($this->relationLoaded('lephis') && $this->lephis->isNotEmpty()) {
            if ($this->maTTHC == 2 || $this->maTTHC == 3 || $this->maTTHC == 6) {
                return 'Miễn phí';
            }
            if ($this->maTTHC == 1) {
                return '8.000 VNĐ / bản sao';
            }
            if ($this->maTTHC == 4) {
                return '10.000 VNĐ / lượt';
            }
            if ($this->maTTHC == 5) {
                return '8.000 VNĐ / bản';
            }

            $fee = $this->lephis->first();
            $amount = (float) ($fee->soTien ?? 0);
            if ($amount <= 0) {
                return 'Miễn phí';
            }

            return number_format($amount, 0, ',', '.').' VNĐ';
        }

        return 'Miễn phí';
    }
}
