<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old data to avoid duplicates
        DB::table('faqs')->truncate();

        $now = now();
        $faqs = [
            // Đăng ký tài khoản
            [
                'id_loaicauhoi' => 1,
                'name_loaicauhoi' => 'Đăng ký tài khoản',
                'cauhoi' => 'Làm thế nào để tạo tài khoản trên cổng dịch vụ công?',
                'dapan' => 'Bấm Đăng ký, nhập họ tên, số điện thoại/email, số CCCD và xác thực OTP để kích hoạt.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 1,
                'name_loaicauhoi' => 'Đăng ký tài khoản',
                'cauhoi' => 'Tôi không nhận được email kích hoạt?',
                'dapan' => 'Kiểm tra hộp thư spam. Nếu không thấy, chọn Gửi lại email xác nhận hoặc liên hệ tổng đài hỗ trợ.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 1,
                'name_loaicauhoi' => 'Đăng ký tài khoản',
                'cauhoi' => 'Có thể đăng ký bằng số điện thoại không?',
                'dapan' => 'Bạn có thể đăng ký bằng số điện thoại, hệ thống gửi OTP SMS để xác thực và kích hoạt.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 1,
                'name_loaicauhoi' => 'Đăng ký tài khoản',
                'cauhoi' => 'Tài khoản bị trùng số CCCD phải làm gì?',
                'dapan' => 'Nếu báo trùng CCCD, chọn Quên mật khẩu để khôi phục hoặc liên hệ hỗ trợ để gỡ trùng.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 1,
                'name_loaicauhoi' => 'Đăng ký tài khoản',
                'cauhoi' => 'Đăng ký doanh nghiệp cần thêm giấy tờ gì?',
                'dapan' => 'Cần mã số thuế, tên doanh nghiệp, người đại diện và giấy ủy quyền/hợp lệ khi nộp hồ sơ thay.',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Đăng nhập & OTP
            [
                'id_loaicauhoi' => 2,
                'name_loaicauhoi' => 'Đăng nhập & OTP',
                'cauhoi' => 'Không nhận được OTP qua SMS thì làm sao?',
                'dapan' => 'Chờ 1-2 phút, kiểm tra sóng, tắt chặn tin nhắn rác. Nếu vẫn không nhận, bấm Gửi lại OTP hoặc dùng OTP email.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 2,
                'name_loaicauhoi' => 'Đăng nhập & OTP',
                'cauhoi' => 'Đổi số điện thoại đăng nhập ở đâu?',
                'dapan' => 'Đăng nhập, vào Tài khoản > Thông tin cá nhân > Cập nhật số điện thoại. Hệ thống yêu cầu xác thực OTP mới.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 2,
                'name_loaicauhoi' => 'Đăng nhập & OTP',
                'cauhoi' => 'Quên mật khẩu thì khôi phục thế nào?',
                'dapan' => 'Tại màn hình đăng nhập, bấm Quên mật khẩu, nhập số điện thoại/email đã đăng ký, nhận OTP và đặt mật khẩu mới.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 2,
                'name_loaicauhoi' => 'Đăng nhập & OTP',
                'cauhoi' => 'Đăng nhập bằng tài khoản Cổng DVCQG có được không?',
                'dapan' => 'Nếu đã tích hợp, chọn Đăng nhập bằng Cổng DVCQG, xác thực một lần rồi dùng chung nhiều dịch vụ.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 2,
                'name_loaicauhoi' => 'Đăng nhập & OTP',
                'cauhoi' => 'Tài khoản bị khóa do nhập sai OTP nhiều lần?',
                'dapan' => 'Tài khoản tự mở sau 15 phút. Nếu cần mở ngay, liên hệ tổng đài và cung cấp thông tin xác thực.',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Nộp hồ sơ trực tuyến
            [
                'id_loaicauhoi' => 3,
                'name_loaicauhoi' => 'Nộp hồ sơ trực tuyến',
                'cauhoi' => 'Hồ sơ điện tử cần định dạng file nào?',
                'dapan' => 'Ưu tiên PDF/JPEG/PNG dung lượng < 5MB mỗi file. Đặt tên file không dấu, không ký tự đặc biệt để tránh lỗi.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 3,
                'name_loaicauhoi' => 'Nộp hồ sơ trực tuyến',
                'cauhoi' => 'Làm sao biết thủ tục có hỗ trợ nộp online?',
                'dapan' => 'Trang chi tiết thủ tục có nút Nộp hồ sơ trực tuyến hoặc mức độ 3/4 thì có thể nộp online.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 3,
                'name_loaicauhoi' => 'Nộp hồ sơ trực tuyến',
                'cauhoi' => 'Có cần bản giấy sau khi nộp trực tuyến không?',
                'dapan' => 'Một số thủ tục yêu cầu nộp bổ sung bản giấy. Xem mục Thành phần hồ sơ và Cách thức thực hiện để biết chi tiết.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 3,
                'name_loaicauhoi' => 'Nộp hồ sơ trực tuyến',
                'cauhoi' => 'Có thể lưu hồ sơ nháp không?',
                'dapan' => 'Trong khi kê khai, bấm Lưu nháp. Vào Hồ sơ của tôi để hoàn thiện và gửi sau.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 3,
                'name_loaicauhoi' => 'Nộp hồ sơ trực tuyến',
                'cauhoi' => 'Gửi hồ sơ xong có nhận được mã tra cứu?',
                'dapan' => 'Sau khi nộp thành công, hệ thống hiển thị mã hồ sơ và gửi email/SMS xác nhận kèm mã tra cứu.',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Thanh toán phí, lệ phí
            [
                'id_loaicauhoi' => 4,
                'name_loaicauhoi' => 'Thanh toán phí, lệ phí',
                'cauhoi' => 'Hệ thống hỗ trợ phương thức thanh toán nào?',
                'dapan' => 'Hỗ trợ thẻ nội địa/ATM, thẻ quốc tế, ví điện tử hoặc QR tùy cổng thanh toán tích hợp.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 4,
                'name_loaicauhoi' => 'Thanh toán phí, lệ phí',
                'cauhoi' => 'Thanh toán bị trừ tiền nhưng báo lỗi?',
                'dapan' => 'Giữ biên lai, chờ 5-10 phút kiểm tra lại. Nếu vẫn lỗi, liên hệ tổng đài và cung cấp mã giao dịch.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 4,
                'name_loaicauhoi' => 'Thanh toán phí, lệ phí',
                'cauhoi' => 'Có xuất hóa đơn điện tử sau thanh toán không?',
                'dapan' => 'Một số thủ tục có hóa đơn điện tử. Bạn sẽ nhận qua email hoặc tải trong Hồ sơ của tôi nếu cơ quan cung cấp.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 4,
                'name_loaicauhoi' => 'Thanh toán phí, lệ phí',
                'cauhoi' => 'Đang thanh toán mà thoát ra giữa chừng?',
                'dapan' => 'Hồ sơ vẫn lưu. Vào chi tiết hồ sơ và chọn thanh toán lại; giao dịch dở dang sẽ tự hủy.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 4,
                'name_loaicauhoi' => 'Thanh toán phí, lệ phí',
                'cauhoi' => 'Có thể thanh toán hộ người khác không?',
                'dapan' => 'Được, chỉ cần mã hồ sơ và thông tin thanh toán. Lưu biên lai để đối chiếu.',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Tra cứu hồ sơ
            [
                'id_loaicauhoi' => 5,
                'name_loaicauhoi' => 'Tra cứu hồ sơ',
                'cauhoi' => 'Tra cứu hồ sơ bằng mã hồ sơ ở đâu?',
                'dapan' => 'Vào menu Tra cứu hồ sơ, nhập mã hồ sơ và số điện thoại/email đã đăng ký để xem trạng thái.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 5,
                'name_loaicauhoi' => 'Tra cứu hồ sơ',
                'cauhoi' => 'Có thông báo khi hồ sơ được giải quyết không?',
                'dapan' => 'Khi trạng thái thay đổi, hệ thống gửi thông báo qua email/SMS và hiển thị trong mục Thông báo tài khoản.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 5,
                'name_loaicauhoi' => 'Tra cứu hồ sơ',
                'cauhoi' => 'Hồ sơ bị yêu cầu bổ sung thì làm thế nào?',
                'dapan' => 'Xem chi tiết yêu cầu, bổ sung file/thông tin theo hướng dẫn rồi bấm Gửi lại. Hạn bổ sung ghi trong thông báo.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 5,
                'name_loaicauhoi' => 'Tra cứu hồ sơ',
                'cauhoi' => 'Có thể hủy hồ sơ đã nộp không?',
                'dapan' => 'Nếu hồ sơ chưa được tiếp nhận, có thể gửi yêu cầu hủy. Khi đã tiếp nhận, cần liên hệ cơ quan xử lý.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 5,
                'name_loaicauhoi' => 'Tra cứu hồ sơ',
                'cauhoi' => 'Nhận kết quả qua bưu chính như thế nào?',
                'dapan' => 'Khi nộp hồ sơ, chọn trả kết quả qua bưu chính và cung cấp địa chỉ. Đơn vị bưu chính sẽ liên hệ khi phát.',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Hỗ trợ kỹ thuật
            [
                'id_loaicauhoi' => 6,
                'name_loaicauhoi' => 'Hỗ trợ kỹ thuật',
                'cauhoi' => 'Trình duyệt nào được khuyến nghị?',
                'dapan' => 'Khuyến nghị dùng Chrome hoặc Firefox mới nhất, bật JavaScript và cho phép pop-up khi thanh toán.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 6,
                'name_loaicauhoi' => 'Hỗ trợ kỹ thuật',
                'cauhoi' => 'Tải file bị lỗi dung lượng hoặc ký tự đặc biệt?',
                'dapan' => 'Giảm dung lượng <5MB, đổi tên file không dấu, không ký tự đặc biệt hoặc dấu chấm liên tiếp rồi tải lại.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 6,
                'name_loaicauhoi' => 'Hỗ trợ kỹ thuật',
                'cauhoi' => 'Trang bị treo khi gửi hồ sơ?',
                'dapan' => 'Làm mới trang, đăng nhập lại, xóa cache trình duyệt hoặc thử trình duyệt khác. Nếu còn lỗi, báo mã lỗi cho hỗ trợ.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 6,
                'name_loaicauhoi' => 'Hỗ trợ kỹ thuật',
                'cauhoi' => 'Có kênh hỗ trợ trực tiếp không?',
                'dapan' => 'Bạn có thể chat với trợ lý ảo, gọi tổng đài hỗ trợ hoặc đến Bộ phận một cửa UBND để được hướng dẫn.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id_loaicauhoi' => 6,
                'name_loaicauhoi' => 'Hỗ trợ kỹ thuật',
                'cauhoi' => 'Thay đổi thông tin cá nhân hiển thị ở hồ sơ?',
                'dapan' => 'Vào Tài khoản > Hồ sơ cá nhân để cập nhật. Hồ sơ đã nộp không tự cập nhật; cần chỉnh trực tiếp từng hồ sơ nếu cần.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('faqs')->insert($faqs);
    }
}
