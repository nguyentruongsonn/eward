<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProcedureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->maTTHC,
            'name' => $this->tenTTHC,
            'field_id' => $this->maLinhVuc,
            'field_name' => $this->tenLinhVuc ?? $this->linhVuc?->tenLinhVuc,
            'counter_id' => $this->maQuayLamViec,
            'status' => $this->trangThai,
            'instructions' => $this->trinhTuThucHien,
            'target' => $this->doiTuongThucHien,
            'agency' => $this->coQuanThucHien,
            'requirements' => $this->yeuCauDieuKien,
            'legal_basis' => $this->canCuPhapLy,
            'result' => $this->ketQuaThucHien,
        ];

        if ($this->resource instanceof Model) {
            if ($this->resource->relationLoaded('cachThucHiens')) {
                $data['methods'] = $this->cachThucHiens->map(fn ($method): array => [
                    'id' => $method->getKey(),
                    'channel' => $method->kenh,
                    'resolution_time' => $method->thoiHanGiaiQuyet,
                    'duration' => $method->thoiHan,
                    'fee_description' => $method->moTaPhiLePhi,
                    'description' => $method->moTa,
                ])->values()->all();
            }
            if ($this->resource->relationLoaded('doiTuongs')) {
                $data['audiences'] = $this->doiTuongs->map(fn ($audience): array => [
                    'id' => $audience->getKey(),
                    'name' => $audience->tenDoiTuong,
                ])->values()->all();
            }
            if ($this->resource->relationLoaded('thanhPhanHoSos')) {
                $data['components'] = $this->thanhPhanHoSos->map(fn ($component): array => [
                    'id' => $component->getKey(),
                    'name' => $component->tenThanhPhan,
                    'documents' => $component->relationLoaded('giayTos') ? $component->giayTos->map(fn ($document): array => [
                        'id' => $document->getKey(),
                        'name' => $document->tenGiayTo,
                        'original_copies' => (int) $document->pivot->soLuongBanChinh,
                        'duplicate_copies' => (int) $document->pivot->soLuongBanSao,
                    ])->values()->all() : [],
                ])->values()->all();
            }
            if ($this->resource->relationLoaded('lephis')) {
                $data['fees'] = $this->lephis->map(fn ($fee): array => [
                    'id' => $fee->getKey(),
                    'type' => $fee->loaiLePhi,
                    'amount' => (float) $fee->soTien,
                    'required' => $fee->batBuoc,
                    'description' => $fee->moTa,
                ])->values()->all();
            }
            if ($this->resource->relationLoaded('formConfig')) {
                $data['form_config'] = $this->formConfig?->cauHinhForm;
            }
        }

        return $data;
    }
}
