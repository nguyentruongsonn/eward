<?php

namespace Database\Factories;

use App\Models\LichHen;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LichHen> */
class LichHenFactory extends Factory
{
    protected $model = LichHen::class;

    public function definition(): array
    {
        return [
            'IDCD' => 1,
            'maTTHC' => 1,
            'maQuayLamViec' => 1,
            'thoiGianHen' => now()->addDays(2)->setHour(9)->setMinute(0),
            'trangThai' => 'Đã đặt',
        ];
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'trangThai' => 'Đã đến',
            'checkin_time' => now(),
            'soThuTu' => 1,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'trangThai' => 'Hoàn thành',
            'checkin_time' => now()->subHours(1),
            'soThuTu' => 1,
        ]);
    }
}
