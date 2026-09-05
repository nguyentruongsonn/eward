<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinhVucSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('linhvuc')->insert([
            [
                'maLinhVuc' => 1,
                'tenLinhVuc' => 'Hộ tịch',
            ],
            [
                'maLinhVuc' => 2,
                'tenLinhVuc' => 'Chứng thực',
            ],
            [
                'maLinhVuc' => 3,
                'tenLinhVuc' => 'Đăng ký, quản lý cư trú',
            ],
            [
                'maLinhVuc' => 4,
                'tenLinhVuc' => 'Đất đai',
            ],
            [
                'maLinhVuc' => 5,
                'tenLinhVuc' => 'Người có công',
            ],
            [
                'maLinhVuc' => 6,
                'tenLinhVuc' => 'Bảo trợ xã hội',
            ],
            [
                'maLinhVuc' => 7,
                'tenLinhVuc' => 'Nuôi con nuôi',
            ],
            [
                'maLinhVuc' => 8,
                'tenLinhVuc' => 'Phòng cháy chữa cháy',
            ],
            [
                'maLinhVuc' => 9,
                'tenLinhVuc' => 'An toàn thực phẩm',
            ],
            [
                'maLinhVuc' => 10,
                'tenLinhVuc' => 'Trẻ em',
            ],
            [
                'maLinhVuc' => 11,
                'tenLinhVuc' => 'Thành lập và hoạt động của doanh nghiệp',
            ],
            [
                'maLinhVuc' => 12,
                'tenLinhVuc' => 'Xây dựng',
            ],
            [
                'maLinhVuc' => 13,
                'tenLinhVuc' => 'Kinh doanh khí',
            ],
            [
                'maLinhVuc' => 14,
                'tenLinhVuc' => 'Kinh tế hợp tác và Phát triển nông thôn',
            ],
            [
                'maLinhVuc' => 15,
                'tenLinhVuc' => 'Công nghiệp địa phương',
            ],
            [
                'maLinhVuc' => 16,
                'tenLinhVuc' => 'Lưu thông hàng hóa trong nước',
            ],
            [
                'maLinhVuc' => 17,
                'tenLinhVuc' => 'Bảo vệ quyền lợi người tiêu dùng',
            ],
            [
                'maLinhVuc' => 18,
                'tenLinhVuc' => 'Tài chính đất đai',
            ],
            [
                'maLinhVuc' => 19,
                'tenLinhVuc' => 'Quản lý công sản',

            ],
            [
                'maLinhVuc' => 20,
                'tenLinhVuc' => 'Cụm công nghiệp',
            ],

        ]);
    }
}
