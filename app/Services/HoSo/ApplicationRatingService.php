<?php

namespace App\Services\HoSo;

use App\Enums\HoSoStatus;
use App\Exceptions\ApiException;
use App\Models\DanhGia;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ApplicationRatingService
{
    public function rate(Nguoi $actor, HoSoXuLy $application, int $score, ?string $comment = null): DanhGia
    {
        return DB::transaction(function () use ($actor, $application, $score, $comment): DanhGia {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if ((int) $locked->maTrangThai !== HoSoStatus::Delivered->value) {
                throw new ApiException('Hồ sơ chưa được trả kết quả.', 'RATING_APPLICATION_NOT_DELIVERED', 409);
            }
            if (! $locked->ngayTra) {
                throw new ApiException('Không tìm thấy ngày trả kết quả.', 'RATING_DELIVERY_DATE_MISSING', 409);
            }

            $deliveryDate = CarbonImmutable::parse($locked->ngayTra)->startOfDay();
            $today = CarbonImmutable::now()->startOfDay();
            if ($deliveryDate->isFuture()) {
                throw new ApiException('Ngày trả kết quả không hợp lệ.', 'RATING_DELIVERY_DATE_INVALID', 409);
            }

            $daysSinceDelivery = $deliveryDate->diffInDays($today);
            if ($daysSinceDelivery > 10) {
                throw new ApiException('Đã quá thời hạn đánh giá (10 ngày).', 'RATING_WINDOW_EXPIRED', 409);
            }

            if (DanhGia::query()->where('maHSXL', $locked->getKey())->exists()) {
                throw new ApiException('Bạn đã đánh giá hồ sơ này rồi.', 'RATING_ALREADY_EXISTS', 409);
            }

            $citizenId = $actor->congDan()->where('IDCD', $locked->IDCD)->value('IDCD');
            if (! $citizenId) {
                throw new ApiException('Không tìm thấy thông tin công dân.', 'CITIZEN_PROFILE_NOT_FOUND', 404);
            }

            return DanhGia::create([
                'maHSXL' => $locked->getKey(),
                'soDiem' => $score,
                'nhanXet' => $comment,
                'IDCD' => $citizenId,
                'ngayDanhGia' => now(),
            ]);
        });
    }
}
