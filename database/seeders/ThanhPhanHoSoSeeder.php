<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhPhanHoSoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thanhphanhoso')->insert([
            // Cấp bản sao trích lục hộ tịch, bản sao Giấy khai sinh

            [
                'maThanhPhan' => 1,
                'maTTHC' => 1,
                'tenThanhPhan' => 'Bao gồm',
            ],

            [
                'maThanhPhan' => 2,
                'maTTHC' => 1,
                'tenThanhPhan' => '* Giấy tờ phải nộp',
            ],
            [
                'maThanhPhan' => 3,
                'maTTHC' => 1,
                'tenThanhPhan' => '* Giấy tờ phải xuất trình',
            ],
            [
                'maThanhPhan' => 4,
                'maTTHC' => 1,
                'tenThanhPhan' => '* Lưu ý',
            ],
            // =================Thủ tục đăng ký kết hôn
            [
                'maThanhPhan' => 5,
                'maTTHC' => 2,
                'tenThanhPhan' => 'Bao gồm',
            ],

            [
                'maThanhPhan' => 6,
                'maTTHC' => 2,
                'tenThanhPhan' => '* Giấy tờ phải xuất trình',
            ],

            [
                'maThanhPhan' => 7,
                'maTTHC' => 2,
                'tenThanhPhan' => '* Lưu ý',
            ],

            // =================Thủ tục đăng ký khai sinh
            [
                'maThanhPhan' => 8,
                'maTTHC' => 3,
                'tenThanhPhan' => 'Bao gồm',
            ],
            [
                'maThanhPhan' => 9,
                'maTTHC' => 3,
                'tenThanhPhan' => '* Giấy tờ phải nộp',
            ],
            [
                'maThanhPhan' => 10,
                'maTTHC' => 3,
                'tenThanhPhan' => '* Giấy tờ phải xuất trình',
            ],
            [
                'maThanhPhan' => 11,
                'maTTHC' => 3,
                'tenThanhPhan' => '* Lưu ý',
            ],
            // ============Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)
            [
                'maThanhPhan' => 12,
                'maTTHC' => 4,
                'tenThanhPhan' => 'Người yêu cầu chứng thực phải xuất trình giấy tờ sau',
            ],

            // ==============Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận
            [
                'maThanhPhan' => 13,
                'maTTHC' => 5,
                'tenThanhPhan' => 'Bao gồm',
            ],
            // ===============Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi
            [
                'maThanhPhan' => 14,
                'maTTHC' => 6,
                'tenThanhPhan' => 'Bao gồm',
            ],

            // ==============Gia hạn giấy phép xây dựng đối với công trình cấp III, cấp IV (công trình Không theo tuyến/Theo tuyến trong đô thị/Tín ngưỡng, tôn giáo/Tượng đài, tranh hoành tráng/Sửa chữa, cải tạo/Theo giai đoạn cho công trình không theo tuyến/Theo giai đoạn cho công trình theo tuyến trong đô thị/Dự án) và nhà ở riêng lẻ
            [
                'maThanhPhan' => 15,
                'maTTHC' => 7,
                'tenThanhPhan' => 'Bao gồm',
            ],

            // ===============Thủ tục cấp Giấy xác nhận tình trạng hôn nhân
            [
                'maThanhPhan' => 16,
                'maTTHC' => 8,
                'tenThanhPhan' => 'Bao gồm',
            ],
            [
                'maThanhPhan' => 17,
                'maTTHC' => 8,
                'tenThanhPhan' => '* Giấy tờ phải nộp',
            ],
            [
                'maThanhPhan' => 18,
                'maTTHC' => 8,
                'tenThanhPhan' => '* Giấy tờ phải xuất trình',
            ],
            [
                'maThanhPhan' => 19,
                'maTTHC' => 8,
                'tenThanhPhan' => '* Lưu ý',
            ],

            // //============= Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi
            // //maThanhPhan = 15
            //         [
            //             'maThanhPhan'=>17,
            //             'maTTHC' => 9,
            //             'tenThanhPhan' =>'Bao gồm'
            //         ]
        ]);
    }
}
