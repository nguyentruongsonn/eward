<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrangThaiHoSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('trangthaihoso')->insert([
            [
                'maTrangThai' => 1,
                'tenTrangThai' => 'Chờ tiếp nhận',
            ],

            [
                'maTrangThai' => 2,
                'tenTrangThai' => 'Được tiếp nhận',
            ],
            [
                'maTrangThai' => 3,
                'tenTrangThai' => 'Không được tiếp nhân',
            ],
            [
                'maTrangThai' => 4,
                'tenTrangThai' => 'Đang xử lý',
            ],
            [
                'maTrangThai' => 5,
                'tenTrangThai' => 'Yêu cầu bổ sung giấy tờ',
            ],
            [
                'maTrangThai' => 6,
                'tenTrangThai' => 'Hồ sơ đã bổ sung giấy tờ',
            ],
            [
                'maTrangThai' => 7,
                'tenTrangThai' => 'Công dân yêu cầu rút hồ sơ',
            ],
            [
                'maTrangThai' => 8,
                'tenTrangThai' => 'Dửng xử lý',
            ],
            [
                'maTrangThai' => 9,
                'tenTrangThai' => 'Đã xử lý xong',
            ],
            [
                'maTrangThai' => 10,
                'tenTrangThai' => 'Đã trả kết quả',
            ],
            [
                'maTrangThai' => 11,
                'tenTrangThai' => 'Nhận trực tiếp',
            ],
            [
                'maTrangThai' => 12,
                'tenTrangThai' => 'Yêu cầu xử lý lại',
            ],
            [
                'maTrangThai' => 13,
                'tenTrangThai' => 'Chờ thanh toán',
            ],
        ]);
    }
}
