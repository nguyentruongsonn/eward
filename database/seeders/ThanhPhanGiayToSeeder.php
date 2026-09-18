<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhPhanGiayToSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thanhphangiayto')->insert([
            // Cấp bản sao trích lục hộ tịch, bản sao giấy khai sinh
            // Bao gồm
            [
                'maThanhPhan' => 1,
                'maGiayTo' => 19,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 1,
                'maGiayTo' => 20,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 1,
                'maGiayTo' => 21,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Giấy tờ phải nộp
            [
                'maThanhPhan' => 2,
                'maGiayTo' => 1,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 2,
                'maGiayTo' => 2,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Giấy tờ phải xuất trình
            [
                'maThanhPhan' => 3,
                'maGiayTo' => 3,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 3,
                'maGiayTo' => 4,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            // Lưu ý
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 5,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 6,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 7,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 8,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 9,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 10,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 11,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 12,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 13,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 14,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 15,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 16,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 17,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 4,
                'maGiayTo' => 18,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            // =================Thủ tục đăng ký kết hôn
            // Bao gồm
            [
                'maThanhPhan' => 5,
                'maGiayTo' => 22,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 5,
                'maGiayTo' => 23,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 5,
                'maGiayTo' => 24,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Giấy tờ phải xuất trình
            [
                'maThanhPhan' => 6,
                'maGiayTo' => 25,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 6,
                'maGiayTo' => 26,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Lưu ý
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 27,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 28,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 29,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 30,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 31,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 32,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 33,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 34,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 35,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 36,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 37,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 38,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 39,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 7,
                'maGiayTo' => 40,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // =================Thủ tục đăng ký khai sinh
            // bao gồm
            [
                'maThanhPhan' => 8,
                'maGiayTo' => 41,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 8,
                'maGiayTo' => 42,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 8,
                'maGiayTo' => 43,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Giấy tờ phải nộp
            [
                'maThanhPhan' => 9,
                'maGiayTo' => 44,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 9,
                'maGiayTo' => 45,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 9,
                'maGiayTo' => 46,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 9,
                'maGiayTo' => 47,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 9,
                'maGiayTo' => 48,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            // Giấy tờ phải xuất trình
            [
                'maThanhPhan' => 10,
                'maGiayTo' => 49,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 10,
                'maGiayTo' => 50,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 10,
                'maGiayTo' => 51,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Lưu ý
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 52,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 54,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 11,
                'maGiayTo' => 55,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 56,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 57,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 58,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 59,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 60,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 61,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 11,
                'maGiayTo' => 62,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 63,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 64,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 65,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 66,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 67,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 68,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 69,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 11,
                'maGiayTo' => 70,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            // Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)
            [
                'maThanhPhan' => 12,
                'maGiayTo' => 71,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 12,
                'maGiayTo' => 72,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 1,
            ],
            // ==================Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận
            [
                'maThanhPhan' => 13,
                'maGiayTo' => 73,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            // ============Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi
            [
                'maThanhPhan' => 14,
                'maGiayTo' => 74,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 14,
                'maGiayTo' => 75,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 14,
                'maGiayTo' => 76,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            // ================Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi
            [
                'maThanhPhan' => 15,
                'maGiayTo' => 77,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 15,
                'maGiayTo' => 78,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            // ====================Thủ tục cấp Giấy xác nhận tình trạng hôn nhân

            [
                'maThanhPhan' => 16,
                'maGiayTo' => 79,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 16,
                'maGiayTo' => 80,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 16,
                'maGiayTo' => 81,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 17,
                'maGiayTo' => 82,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 1,
            ],
            [
                'maThanhPhan' => 17,
                'maGiayTo' => 83,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 1,
            ],

            [
                'maThanhPhan' => 17,
                'maGiayTo' => 84,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 17,
                'maGiayTo' => 85,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 18,
                'maGiayTo' => 86,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 18,
                'maGiayTo' => 87,
                'soLuongBanChinh' => 1,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 18,
                'maGiayTo' => 88,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 89,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 90,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 91,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 92,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 93,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 94,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 95,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 96,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 97,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 98,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 99,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 100,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 101,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 102,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 103,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 104,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 105,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 106,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],
            [
                'maThanhPhan' => 19,
                'maGiayTo' => 107,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

            [
                'maThanhPhan' => 19,
                'maGiayTo' => 108,
                'soLuongBanChinh' => 0,
                'soLuongBanSao' => 0,
            ],

        ]);
    }
}
