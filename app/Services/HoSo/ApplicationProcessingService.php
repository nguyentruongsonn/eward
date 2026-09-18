<?php

namespace App\Services\HoSo;

use App\Enums\Role;
use App\Exceptions\ApiException;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;

class ApplicationProcessingService
{
    public function saveOpinion(HoSoXuLy $application, ?string $opinion): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $opinion): HoSoXuLy {
            $locked = HoSoXuLy::query()
                ->whereKey($application->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $locked->yKienXuLy = $opinion;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function confirmReception(HoSoXuLy $application, ?string $note = null, ?Nguoi $actor = null): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $note, $actor): HoSoXuLy {
            $locked = HoSoXuLy::query()
                ->whereKey($application->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $locked->maTrangThai !== 2) {
                throw new ApiException('Chỉ có thể chuyển hồ sơ đã được tiếp nhận sang cán bộ thụ lý.', 'WORKFLOW_CONFIRM_RECEPTION_INVALID', 409, [
                    'from' => (int) $locked->maTrangThai,
                ]);
            }
            if ($actor && ! in_array(Role::normalize($actor->vaiTro), [Role::OneStopOfficer, Role::Administrator], true)) {
                throw new ApiException('Bạn không có quyền chuyển hồ sơ sang cán bộ thụ lý.', 'FORBIDDEN', 403);
            }

            $message = trim((string) $note);
            $message = $message !== ''
                ? 'Đã chuyển sang cán bộ thụ lý: '.$message
                : 'Đã chuyển sang cán bộ thụ lý.';
            $locked->ghiChu = ($locked->ghiChu ?? '')."\n[".now()->format('d/m/Y H:i').'] '.$message;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }
}
