<?php

namespace Database\Factories;

use App\Models\TTHC;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TTHC> */
class TTHCFactory extends Factory
{
    protected $model = TTHC::class;

    public function definition(): array
    {
        return [
            'tenTTHC' => 'Thủ tục '.fake()->words(3, true),
            'maLinhVuc' => 1,
            'maQuayLamViec' => 1,
            'trinhTuThucHien' => 'Nộp hồ sơ trực tuyến hoặc nộp trực tiếp tại Bộ phận Một cửa.',
            'doiTuongThucHien' => 'Công dân, tổ chức',
            'coQuanThucHien' => 'Ủy ban nhân dân cấp xã/phường',
            'trangThai' => 'Công khai',
            'yeuCauDieuKien' => 'Không có yêu cầu đặc thù.',
            'canCuPhapLy' => 'Quy định pháp luật hiện hành liên quan.',
            'ketQuaThucHien' => 'Văn bản xác nhận giải quyết TTHC',
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'trangThai' => 'Chờ công khai',
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'trangThai' => 'Bãi bỏ',
        ]);
    }
}
