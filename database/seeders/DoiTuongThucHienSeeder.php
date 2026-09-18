<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoiTuongThucHienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('doituongthuchien')->insert([
            [
                'maDoiTuong' => 1,
                'tenDoiTuong' => 'Công dân Việt Nam',
            ],
            [
                'maDoiTuong' => 2,
                'tenDoiTuong' => 'Tổ chức',
            ],
            [
                'maDoiTuong' => 3,
                'tenDoiTuong' => 'Người nước ngoài',
            ],
            [
                'maDoiTuong' => 4,
                'tenDoiTuong' => 'Doanh nghiệp',
            ],

        ]);
    }
}
