<?php

namespace Database\Factories;

use App\Models\Nguoi;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Nguoi> */
class NguoiFactory extends Factory
{
    protected $model = Nguoi::class;

    public function definition(): array
    {
        return [
            'maCCCD' => (string) fake()->unique()->numerify('################'),
            'hoTen' => fake()->name(),
            'gioiTinh' => fake()->randomElement(['Nam', 'Nữ']),
            'ngaySinh' => fake()->date('Y-m-d', '-18 years'),
            'queQuan' => fake()->address(),
            'noiThuongTru' => fake()->address(),
            'noiTamTru' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password123'),
            'soDienThoai' => fake()->numerify('0#########'),
            'vaiTro' => 'Công dân/ Tổ chức',
        ];
    }
}
