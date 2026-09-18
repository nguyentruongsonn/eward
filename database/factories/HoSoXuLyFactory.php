<?php

namespace Database\Factories;

use App\Models\HoSoXuLy;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HoSoXuLy> */
class HoSoXuLyFactory extends Factory
{
    protected $model = HoSoXuLy::class;

    public function definition(): array
    {
        return [
            'maHSXL' => 'HS_'.strtoupper(fake()->unique()->bothify('??####??')),
            'maTTHC' => 1,
            'IDCD' => 1,
            'tenChuHoSo' => fake()->name(),
            'doiTuongThucHien' => 'Công dân',
            'email' => fake()->safeEmail(),
            'soDienThoai' => fake()->numerify('0#########'),
            'dulieu' => [],
            'ngayTiepNhan' => now()->toDateString(),
            'ngayHenTra' => now()->addDays(3)->toDateString(),
            'maTrangThai' => 1, // Mới tiếp nhận
            'lePhi' => 0,
            'hinhThuc' => 'Nhận trực tuyến',
            'donViXuLy' => 'Bộ phận Một cửa',
        ];
    }

    public function status(int $status): static
    {
        return $this->state(fn (array $attributes) => [
            'maTrangThai' => $status,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'ngayHenTra' => now()->subDays(2)->toDateString(),
        ]);
    }
}
