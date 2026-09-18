<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTHCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tthc')->insert([
            [
                'maTTHC' => 1,
                'tenTTHC' => 'Cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh',
                'maLinhVuc' => '1',
                'maQuayLamViec' => '1',
                'trinhTuThucHien' => '
            - Nếu lựa chọn hình thức nộp hồ sơ trực tiếp, người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh nộp hồ sơ đề nghị cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh tại Trung tâm Phục vụ hành chính công có thẩm quyền; nộp phí theo quy định pháp luật.
            Trường hợp cơ quan, tổ chức có thẩm quyền đề nghị cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh của cá nhân thì gửi văn bản yêu cầu nêu rõ lý do cho Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử.
            - Nếu lựa chọn hình thức nộp hồ sơ trực tuyến, người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh truy cập Cổng dịch vụ công quốc gia, xác thực người dùng theo hướng dẫn, đăng nhập vào hệ thống, lựa chọn Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử có thẩm quyền.
            Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tuyến cung cấp thông tin theo mẫu điện tử tương tác yêu cầu bản sao Trích lục hộ tịch, cấp bản sao Giấy khai sinh (cung cấp trên Cổng dịch vụ công), đính kèm bản chụp hoặc bản sao điện tử các giấy tờ, tài liệu liên quan đến nội dung đề nghị cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh; nộp phí thông qua chức năng thanh toán trực tuyến hoặc bằng cách thức khác theo quy định pháp luật, hoàn tất việc nộp hồ sơ.
            - Cán bộ tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công có trách nhiệm kiểm tra tính chính xác, đầy đủ, thống nhất, hợp lệ của hồ sơ.
            (i) Trường hợp hồ sơ đầy đủ, hợp lệ thì tiếp nhận hồ sơ; nếu tiếp nhận hồ sơ sau 15 giờ thì có Phiếu hẹn, trả kết quả cho người yêu cầu trong ngày làm việc tiếp theo (nếu người yêu cầu nộp hồ sơ trực tiếp) hoặc gửi ngay Phiếu hẹn, trả kết quả qua thư điện tử hoặc gửi tin nhắn hẹn trả kết quả qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến); chuyển hồ sơ để công chức tư pháp - hộ tịch xử lý; trường hợp tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công cấp tỉnh, cán bộ tiếp nhận hồ sơ chuyển hồ sơ đến Sở Tư pháp/Ủy ban nhân dân cấp xã có thẩm quyền xử lý.
            (ii) Trường hợp hồ sơ chưa đầy đủ, hợp lệ thì cán bộ tiếp nhận hồ sơ thông báo cho người yêu cầu bổ sung, hoàn thiện hồ sơ, nêu rõ loại giấy tờ, nội dung cần bổ sung để người yêu cầu bổ sung, hoàn thiện. Sau khi hồ sơ được bổ sung, thực hiện lại bước (i);
            (iii) Trường hợp người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh không bổ sung, hoàn thiện được hồ sơ thì cán bộ tiếp nhận hồ sơ báo cáo Lãnh đạo Trung tâm Phục vụ hành chính công có thông báo từ chối giải quyết yêu cầu cấp bản sao Trích lục hộ tịch, bản sao giấy khai sinh.
            - Công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch thẩm tra hồ sơ (thẩm tra tính thống nhất, hợp lệ của các thông tin trong hồ sơ, giấy tờ, tài liệu do người yêu cầu nộp, xuất trình hoặc đính kèm).
            + Trường hợp hồ sơ cần bổ sung, hoàn thiện hoặc không đủ điều kiện giải quyết, phải từ chối thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch gửi thông báo về tình trạng hồ sơ tới Trung tâm Phục vụ hành chính công để thông báo cho người nộp hồ sơ – thực hiện lại bước (ii) hoặc (iii);
            + Trường hợp cần phải kiểm tra, xác minh làm rõ hoặc do nguyên nhân khác mà không thể trả kết quả đúng thời gian đã hẹn thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch lập Phiếu xin lỗi và hẹn lại ngày trả kết quả, trong đó nêu rõ lý do chậm trả kết quả và thời gian hẹn trả kết quả, chuyển Trung tâm Phục vụ hành chính công để trả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp), hoặc gửi Phiếu xin lỗi và hẹn lại ngày trả kết quả qua thư điện tử hoặc gửi tin nhắn qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến).
            + Nếu thấy hồ sơ đầy đủ, hợp lệ, đúng quy định, trường hợp tiếp nhận hồ sơ cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh theo hình thức trực tiếp, thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch in bản sao Trích lục hộ tịch trình Thủ trưởng Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.
            Trường hợp tiếp nhận hồ sơ xin cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh theo hình thức trực tuyến, công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch gửi lại nội dung biểu mẫu Trích lục hộ tịch điện tử tương ứng với thông tin đầy đủ cho người yêu cầu qua thư điện tử hoặc thiết bị số.
            Người yêu cầu có trách nhiệm kiểm tra tính chính xác, đầy đủ của các thông tin trên biểu mẫu Trích lục hộ tịch điện tử và xác nhận (tối đa một ngày).
            Nếu người có yêu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh đã thống nhất, đầy đủ hoặc không có phản hồi sau thời hạn một ngày thì công chức tư pháp - hộ tịch/công chức làm công tác hộ tịch in bản sao Trích lục hộ tịch, trình Thủ trưởng Cơ quan quản lý Cơ sở dữ liệu hộ tịch điện tử ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.

            ',
                'doiTuongThucHien' => 'Công dân Việt Nam',
                'coQuanThucHien' => 'Ủy ban nhân dân xã ABC',
                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => 'Không',
                'canCuPhapLy' => 'Luật 60/2014/QH13',
                'ketQuaThucHien' => 'Trích lục ghi vào Sổ hộ tịch các việc hộ tịch khác, Bản sao giấy khai sinh',
            ],

            [
                'maTTHC' => 2,
                'tenTTHC' => 'Thủ tục đăng ký kết hôn',
                'maLinhVuc' => '1',
                'maQuayLamViec' => '3',
                'trinhTuThucHien' => '
                - Nếu lựa chọn hình thức nộp hồ sơ trực tiếp, người yêu cầu đăng ký kết hôn nộp hồ sơ đăng ký kết hôn tại Trung tâm Phục vụ hành chính công có thẩm quyền; nộp lệ phí nếu thuộc trường hợp phải nộp lệ phí đăng ký kết hôn; nộp phí cấp bản sao Trích lục kết hôn nếu có yêu cầu cấp bản sao Trích lục kết hôn.
                - Nếu lựa chọn hình thức nộp hồ sơ trực tuyến, người có yêu cầu đăng ký kết hôn truy cập Cổng dịch vụ công quốc gia hoặc Cổng dịch vụ công cấp tỉnh, đăng ký tài khoản (nếu chưa có tài khoản), xác thực người dùng theo hướng dẫn, đăng nhập vào hệ thống, xác định đúng Ủy ban nhân dân cấp xã có thẩm quyền.
                Người có yêu cầu đăng ký kết hôn trực tuyến cung cấp thông tin theo biểu mẫu điện tử tương tác đăng ký kết hôn (cung cấp trên Cổng dịch vụ công), đính kèm bản chụp hoặc bản sao điện tử các giấy tờ, tài liệu theo quy định; nộp phí, lệ phí thông qua chức năng thanh toán trực tuyến hoặc bằng cách thức khác theo quy định pháp luật, hoàn tất việc nộp hồ sơ.
                - Cán bộ tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công có trách nhiệm kiểm tra tính chính xác, đầy đủ, thống nhất, hợp lệ của hồ sơ.
                (i) Trường hợp hồ sơ đầy đủ, hợp lệ thì tiếp nhận hồ sơ, có Phiếu hẹn, trả kết quả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp) hoặc gửi ngay Phiếu hẹn, trả kết quả qua thư điện tử hoặc gửi tin nhắn hẹn trả kết quả qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến), chuyển hồ sơ để công chức tư pháp - hộ tịch xử lý; trường hợp tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công cấp tỉnh, cán bộ tiếp nhận hồ sơ chuyển hồ sơ đến Ủy ban nhân dân cấp xã có thẩm quyền xử lý.
                - Sau khi tiếp nhận hồ sơ theo hình thức nộp trực tiếp, cán bộ tiếp nhận hồ sơ tại Bộ phận một cửa thực hiện số hóa (sao chụp, chuyển thành tài liệu điện tử trên hệ thống thông tin, cơ sở dữ liệu) và ký số vào tài liệu, hồ sơ giải quyết thủ tục hành chính đã được số hóa theo quy định.
                (ii) Trường hợp hồ sơ chưa đầy đủ, hợp lệ thì có thông báo cho người yêu cầu bổ sung, hoàn thiện hồ sơ, nêu rõ loại giấy tờ, nội dung cần bổ sung để người có yêu cầu bổ sung, hoàn thiện. Sau khi hồ sơ được bổ sung, thực hiện lại bước (i);
                (iii) Trường hợp người yêu cầu đăng ký kết hôn không bổ sung, hoàn thiện được hồ sơ thì cán bộ tiếp nhận hồ sơ báo cáo Lãnh đạo Trung tâm Phục vụ hành chính công có thông báo từ chối giải quyết yêu cầu đăng ký kết hôn.
                - Công chức tư pháp - hộ tịch thẩm tra hồ sơ (thẩm tra tính thống nhất, hợp lệ của các thông tin trong hồ sơ, giấy tờ, tài liệu đính kèm).
                + Đối với yêu cầu đăng ký kết hôn, cơ quan đăng ký hộ tịch tra cứu thông tin về tình trạng hôn nhân của người yêu cầu đăng ký kết hôn trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư. Kết quả tra cứu được lưu trữ dưới dạng điện tử hoặc bản giấy, phản ánh đầy đủ, chính xác thông tin tại thời điểm tra cứu và đính kèm hồ sơ của người đăng ký. Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó.
                + Nếu bên kết hôn là công dân Việt Nam đã ly hôn hoặc hủy việc kết hôn tại cơ quan có thẩm quyền nước ngoài nhưng qua tra cứu thông tin trong Cơ sở dữ liệu hộ tịch điện tử; thông qua kết nối giữa Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư không thể hiện thông tin về việc đã ghi chú ly hôn, hủy việc kết hôn thì cơ quan đăng ký hộ tịch hướng dẫn công dân thực hiện thủ tục ghi vào sổ hộ tịch việc ly hôn/hủy việc kết hôn tại cơ quan nhà nước có thẩm quyền trước khi giải quyết việc đăng ký kết hôn.
                + Trường hợp hồ sơ cần bổ sung, hoàn thiện hoặc không đủ điều kiện giải quyết, phải từ chối thì công chức tư pháp - hộ tịch gửi thông báo về tình trạng hồ sơ tới Trung tâm Phục vụ hành chính công để thông báo cho người nộp hồ sơ – thực hiện lại bước (ii) hoặc (iii);
                + Trường hợp cần phải kiểm tra, xác minh làm rõ hoặc do nguyên nhân khác mà không thể trả kết quả đúng thời gian đã hẹn thì công chức tư pháp - hộ tịch lập Phiếu xin lỗi và hẹn lại ngày trả kết quả, trong đó nêu rõ lý do chậm trả kết quả và thời gian hẹn trả kết quả, chuyển Trung tâm Phục vụ hành chính công để trả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp), hoặc gửi Phiếu xin lỗi và hẹn lại ngày trả kết quả qua thư điện tử hoặc gửi tin nhắn qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến).
                + Nếu thấy hồ sơ đầy đủ, hợp lệ, các bên có đủ điều kiện kết hôn theo quy định của Luật Hôn nhân và gia đình, không thuộc trường hợp từ chối đăng ký kết hôn theo quy định, trường hợp tiếp nhận hồ sơ đăng ký kết hôn theo hình thức trực tiếp, thì công chức tư pháp - hộ tịch thực hiện việc ghi vào Sổ đăng ký kết hôn, cập nhật thông tin đăng ký kết hôn và lưu chính thức trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung.
                Trường hợp tiếp nhận hồ sơ đăng ký kết hôn theo hình thức trực tuyến, công chức tư pháp - hộ tịch gửi lại biểu mẫu Giấy chứng nhận kết hôn điện tử với thông tin đầy đủ cho người yêu cầu qua thư điện tử hoặc thiết bị số.
                Người yêu cầu có trách nhiệm kiểm tra tính chính xác, đầy đủ của các thông tin trên biểu mẫu Giấy chứng nhận kết hôn điện tử và xác nhận (tối đa một ngày).
                Nếu người có yêu cầu xác nhận thông tin đã thống nhất, đầy đủ hoặc không có phản hồi sau thời hạn yêu cầu thì công chức tư pháp - hộ tịch thực hiện việc ghi nội dung vào Sổ đăng ký kết hôn, cập nhật thông tin đăng ký kết hôn và lưu chính thức trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung.
                - Công chức tư pháp - hộ tịch in Giấy chứng nhận kết hôn, trình Lãnh đạo Ủy ban nhân dân cấp xã ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.
                - Người có yêu cầu đăng ký kết hôn (hai bên nam, nữ phải có mặt, xuất trình giấy tờ tuỳ thân để đối chiếu) kiểm tra thông tin trên Giấy chứng nhận kết hôn, trong Sổ đăng ký kết hôn, khẳng định sự tự nguyện kết hôn và ký tên vào Sổ đăng ký kết hôn, ký tên vào Giấy chứng nhận kết hôn, mỗi bên nam, nữ nhận 01 bản chính Giấy chứng nhận kết hôn.
            ',
                'doiTuongThucHien' => 'Công dân Việt Nam',
                'coQuanThucHien' => 'Ủy ban Nhân dân xã, phường, thị trấn.',
                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => '
                - Nam từ đủ 20 tuổi trở lên, nữ từ đủ 18 tuổi trở lên;
                - Việc kết hôn do nam và nữ tự nguyện quyết định;
                - Các bên không bị mất năng lực hành vi dân sự;
                - Việc kết hôn không thuộc một trong các trường hợp cấm kết hôn, gồm:
                + Kết hôn giả tạo;
                + Tảo hôn, cưỡng ép kết hôn, lừa dối kết hôn, cản trở kết hôn;
                + Người đang có vợ, có chồng mà kết hôn với người khác hoặc chưa có vợ, chưa có chồng mà kết hôn với người đang có chồng, có vợ;
                + Kết hôn giữa những người cùng dòng máu về trực hệ; giữa những người có họ trong phạm vi ba đời; giữa cha, mẹ nuôi với con nuôi; giữa người đã từng là cha, mẹ nuôi với con nuôi, cha chồng với con dâu, mẹ vợ với con rể, cha dượng với con riêng của vợ, mẹ kế với con riêng của chồng.
                * Nhà nước không thừa nhận hôn nhân giữa những người cùng giới tính.
            ',
                'canCuPhapLy' => 'Luật 60/2014/QH13',
                'ketQuaThucHien' => 'Giấy chứng nhận kết hôn',
            ],

            [
                'maTTHC' => 3,
                'tenTTHC' => 'Thủ tục đăng ký khai sinh',
                'maLinhVuc' => '1',
                'maQuayLamViec' => '3',
                'trinhTuThucHien' => '
                - Nếu lựa chọn hình thức nộp hồ sơ trực tiếp, người yêu cầu đăng ký khai sinh nộp hồ sơ đăng ký khai sinh tại Trung tâm Phục vụ hành chính công có thẩm quyền; nộp lệ phí nếu thuộc trường hợp phải nộp lệ phí đăng ký khai sinh; nộp phí cấp bản sao Giấy khai sinh nếu có yêu cầu cấp bản sao Giấy khai sinh.  bản sao Giấy khai sinh.
                - Nếu lựa chọn hình thức nộp hồ sơ trực tuyến, người có yêu cầu đăng ký khai sinh truy cập Cổng dịch vụ công quốc gia hoặc Cổng dịch vụ công cấp tỉnh, đăng ký tài khoản (nếu chưa có tài khoản), xác thực người dùng theo hướng dẫn, đăng nhập vào hệ thống, xác định đúng Ủy ban nhân dân cấp xã có thẩm quyền.
                Người có yêu cầu đăng ký khai sinh trực tuyến cung cấp thông tin theo biểu mẫu điện tử tương tác đăng ký khai sinh (cung cấp trên Cổng dịch vụ công), đính kèm bản chụp hoặc bản sao điện tử các giấy tờ, tài liệu theo quy định; nộp phí, lệ phí thông qua chức năng thanh toán trực tuyến hoặc bằng cách thức khác theo quy định pháp luật, hoàn tất việc nộp hồ sơ.
                - Cán bộ tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công có trách nhiệm kiểm tra tính chính xác, đầy đủ, thống nhất, hợp lệ của hồ sơ.
                (i) Trường hợp hồ sơ đầy đủ, hợp lệ thì tiếp nhận hồ sơ; nếu tiếp nhận hồ sơ sau 15 giờ thì có Phiếu hẹn, trả kết quả cho người yêu cầu trong ngày làm việc tiếp theo (nếu người yêu cầu nộp hồ sơ trực tiếp) hoặc gửi ngay Phiếu hẹn, trả kết quả qua thư điện tử hoặc gửi tin nhắn hẹn trả kết quả qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến); chuyển hồ sơ để công chức tư pháp - hộ tịch xử lý; trường hợp tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công cấp tỉnh, cán bộ tiếp nhận hồ sơ chuyển hồ sơ đến Ủy ban nhân dân cấp xã có thẩm quyền xử lý.
                - Sau khi tiếp nhận hồ sơ theo hình thức nộp trực tiếp, cán bộ tiếp nhận hồ sơ tại Bộ phận một cửa thực hiện số hóa (sao chụp, chuyển thành tài liệu điện tử trên hệ thống thông tin, cơ sở dữ liệu) và ký số vào tài liệu, hồ sơ giải quyết thủ tục hành chính đã được số hóa theo quy định.
                (ii) Trường hợp hồ sơ chưa đầy đủ, hợp lệ thì có thông báo cho người yêu cầu bổ sung, hoàn thiện hồ sơ, nêu rõ loại giấy tờ, nội dung cần bổ sung để người có yêu cầu bổ sung, hoàn thiện. Sau khi hồ sơ được bổ sung, thực hiện lại bước (i);
                (iii) Trường hợp người yêu cầu đăng ký khai sinh không bổ sung, hoàn thiện được hồ sơ thì cán bộ tiếp nhận hồ sơ báo cáo Lãnh đạo Trung tâm Phục vụ hành chính công có thông báo từ chối giải quyết yêu cầu đăng ký khai sinh.
                - Công chức tư pháp - hộ tịch thẩm tra hồ sơ (thẩm tra tính thống nhất, hợp lệ của các thông tin trong hồ sơ, giấy tờ, tài liệu do người yêu cầu nộp, xuất trình hoặc đính kèm).
                + Đối với yêu cầu đăng ký khai sinh mà cha, mẹ trẻ đã đăng ký kết hôn, trên cơ sở thông tin về Giấy chứng nhận kết hôn cung cấp trong Tờ khai đăng ký khai sinh, cơ quan đăng ký hộ tịch có trách nhiệm tra cứu thông tin về tình trạng hôn nhân của cha, mẹ trẻ trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư. Kết quả tra cứu được lưu trữ dưới dạng điện tử hoặc bản giấy, phản ánh đầy đủ, chính xác thông tin tại thời điểm tra cứu và đính kèm hồ sơ của người đăng ký. Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó.
                + Trường hợp hồ sơ cần bổ sung, hoàn thiện hoặc không đủ điều kiện giải quyết, phải từ chối thì công chức tư pháp - hộ tịch gửi thông báo về tình trạng hồ sơ tới Trung tâm Phục vụ hành chính công để thông báo cho người nộp hồ sơ – thực hiện lại bước (ii) hoặc (iii);
                + Trường hợp cần phải kiểm tra, xác minh làm rõ hoặc do nguyên nhân khác mà không thể trả kết quả đúng thời gian đã hẹn thì công chức tư pháp - hộ tịch lập Phiếu xin lỗi và hẹn lại ngày trả kết quả, trong đó nêu rõ lý do chậm trả kết quả và thời gian hẹn trả kết quả, chuyển Trung tâm Phục vụ hành chính công để trả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp), hoặc gửi Phiếu xin lỗi và hẹn lại ngày trả kết quả qua thư điện tử hoặc gửi tin nhắn qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến).
                + Nếu thấy hồ sơ đầy đủ, hợp lệ, đúng quy định, trường hợp tiếp nhận hồ sơ đăng ký khai sinh theo hình thức trực tiếp, thì công chức tư pháp - hộ tịch thực hiện việc ghi vào Sổ đăng ký khai sinh, cập nhật thông tin đăng ký khai sinh trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung, lưu chính thức và chuyển thông tin đến CSDLQGVDC để lấy Số định danh cá nhân.
                Trường hợp tiếp nhận hồ sơ đăng ký khai sinh theo hình thức trực tuyến, công chức tư pháp - hộ tịch gửi lại biểu mẫu Giấy khai sinh điện tử với thông tin đầy đủ cho người yêu cầu qua thư điện tử hoặc thiết bị số.
                Người yêu cầu có trách nhiệm kiểm tra tính chính xác, đầy đủ của các thông tin trên biểu mẫu Giấy khai sinh điện tử và xác nhận (tối đa một ngày).
                Nếu người có yêu cầu xác nhận thông tin đã thống nhất, đầy đủ hoặc không có phản hồi sau thời hạn yêu cầu thì công chức tư pháp - hộ tịch thực hiện việc ghi nội dung vào Sổ đăng ký khai sinh, cập nhật thông tin đăng ký khai sinh trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung, lưu chính thức và chuyển thông tin đến CSDLQGVDC để lấy Số định danh cá nhân.
                - Sau khi CSDLQGVDC trả về Số định danh cá nhân, công chức tư pháp - hộ tịch in Giấy khai sinh, bản sao Giấy khai sinh trình Lãnh đạo Ủy ban nhân dân cấp xã ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.

                * Lưu ý:
                - Trường hợp cha, mẹ trẻ đã đăng ký kết hôn, trên cơ sở thông tin về Giấy chứng nhận kết hôn cung cấp trong hồ sơ đăng ký khai sinh, cơ quan đăng ký hộ tịch có trách nhiệm tra cứu, xác thực thông tin trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, CSDLQGVDC. Kết quả tra cứu được lưu trữ dưới dạng điện tử hoặc bản giấy, phản ánh đầy đủ, chính xác thông tin tại thời điểm tra cứu và đính kèm trong hồ sơ của người đăng ký.
                Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, CSDLQGVDC, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó.
                - Trong thời hạn 60 ngày kể từ ngày sinh con, cha hoặc mẹ có trách nhiệm đăng ký khai sinh cho con; trường hợp cha, mẹ không thể đăng ký khai sinh cho con thì ông hoặc bà hoặc người thân thích khác hoặc cá nhân, tổ chức đang nuôi dưỡng trẻ em có trách nhiệm đăng ký khai sinh cho trẻ em.
                - Trường hợp đăng ký khai sinh cho trẻ bị bỏ rơi thì sau khi nhận được thông báo, Lãnh đạo Ủy ban nhân dân hoặc Trưởng công an cấp xã có trách nhiệm tổ chức lập biên bản về việc trẻ bị bỏ rơi.
                Sau khi lập biên bản, Ủy ban nhân dân cấp xã tiến hành niêm yết tại trụ sở Ủy ban nhân dân trong 07 ngày liên tục về việc trẻ bị bỏ rơi. Hết thời hạn niêm yết, nếu không có thông tin về cha, mẹ đẻ của trẻ, Ủy ban nhân dân cấp xã thông báo cho cá nhân hoặc tổ chức đang tạm thời nuôi dưỡng trẻ để tiến hành đăng ký khai sinh cho trẻ.
                - Trường hợp trẻ chưa xác định được cha thì họ, dân tộc, quê quán, quốc tịch của con được xác định theo họ, dân tộc, quê quán, quốc tịch của mẹ; phần ghi về cha của trẻ để trống. Nếu vào thời điểm đăng ký khai sinh người cha yêu cầu làm thủ tục nhận con thì kết hợp giải quyết việc nhận con và đăng ký khai sinh.
                - Trường hợp trẻ chưa xác định được mẹ mà khi đăng ký khai sinh cha yêu cầu làm thủ tục nhận con thì kết hợp giải quyết việc nhận con và đăng ký khai sinh; phần khai về mẹ của trẻ để trống.
                - Trường hợp trẻ chưa xác định được cả cha và mẹ nhưng không thuộc diện bị bỏ rơi, thì thực hiện tương tự như đăng ký khai sinh cho trẻ bị bỏ rơi, trong Sổ hộ tịch ghi rõ “Trẻ chưa xác định được cha, mẹ”.            ',

                'doiTuongThucHien' => 'Công dân Việt Nam, Người Việt Nam định cư ở nước ngoài',
                'coQuanThucHien' => 'Ủy ban nhân dân cấp xã',
                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => 'Không',
                'canCuPhapLy' => 'Luật 60/2014/QH13',
                'ketQuaThucHien' => 'Giấy khai sinh',
            ],

            [
                'maTTHC' => 4,
                'tenTTHC' => 'Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)',
                'maLinhVuc' => '2',
                'maQuayLamViec' => '1',
                'trinhTuThucHien' => '
                + Người yêu cầu chứng thực chữ ký/điểm chỉ/không thể ký, không thể điểm chỉ được phải xuất trình các giấy tờ phục vụ việc chứng thực chữ ký.
                + Trong trường hợp người yêu cầu chứng thực không thông thạo tiếng Việt thì phải có người phiên dịch. Người phiên dịch phải là người có năng lực hành vi dân sự đầy đủ theo quy định của pháp luật, thông thạo tiếng Việt và ngôn ngữ mà người yêu cầu chứng thực sử dụng. Người phiên dịch do người yêu cầu chứng thực mời hoặc do cơ quan thực hiện chứng thực chỉ định. Thù lao phiên dịch do người yêu cầu chứng thực trả.
                + Người thực hiện chứng thực (hoặc người tiếp nhận hồ sơ trong trường hợp tiếp nhận tại Bộ phận Một cửa) kiểm tra giấy tờ yêu cầu chứng thực, nếu thấy đủ giấy tờ theo quy định, tại thời điểm chứng thực, người yêu cầu chứng thực minh mẫn, nhận thức và làm chủ được hành vi của mình và việc chứng thực không thuộc các trường hợp không được chứng thực chữ ký thì yêu cầu người yêu cầu chứng thực ký/điểm chỉ trước mặt và thực hiện chứng thực như sau:
                * Ghi đầy đủ lời chứng chứng thực chữ ký theo mẫu quy định phía dưới chữ ký được chứng thực hoặc trang liền sau của trang giấy tờ, văn bản có chữ ký được chứng thực; nếu hồ sơ tiếp nhận tại Bộ phận Một cửa thì người tiếp nhận hồ sơ ký vào dưới lời chứng theo mẫu quy định và chuyển hồ sơ cho người thực hiện chứng thực.
                * Người thực hiện chứng thực ký, ghi rõ họ tên, đóng dấu của cơ quan, tổ chức thực hiện chứng thực và ghi vào sổ chứng thực.
                Đối với giấy tờ, văn bản có từ 02 (hai) tờ trở lên thì phải đóng dấu giáp lai. Trường hợp lời chứng được ghi tại tờ liền sau của trang có chữ ký thì phải đóng dấu giáp lai giữa giấy tờ, văn bản chứng thực chữ ký và trang ghi lời chứng.                ',

                'doiTuongThucHien' => 'Công dân Việt Nam, Người Việt Nam định cư ở nước ngoài, Cán bộ, công chức, viên chức, Doanh nghiệp, Doanh nghiệp có vốn đầu tư nước ngoài, Tổ chức (không bao gồm doanh nghiệp, HTX), Hợp tác xã',
                'coQuanThucHien' => 'Tổ chức hành nghề công chứng, Cơ quan đại diện có thẩm quyền, Ủy ban nhân dân cấp xã',

                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => 'Trường hợp không được chứng thực chữ ký:
                + Tại thời điểm chứng thực, người yêu cầu chứng thực chữ ký không nhận thức và làm chủ được hành vi của mình.
                + Người yêu cầu chứng thực chữ ký xuất trình Giấy chứng minh nhân dân hoặc Hộ chiếu không còn giá trị sử dụng hoặc giả mạo.
                + Giấy tờ, văn bản mà người yêu cầu chứng thực ký vào có nội dung quy định tại Khoản 4 Điều 22 của Nghị định số 23/2015/NĐ-CP.
                + Giấy tờ, văn bản có nội dung là hợp đồng, giao dịch; trừ Giấy ủy quyền trong các trường hợp Giấy ủy quyền: (1) ủy quyền về việc nộp hộ, nhận hộ hồ sơ, giấy tờ, trừ trường hợp pháp luật quy định không được ủy quyền; (2) ủy quyền nhận hộ lương hưu, bưu phẩm, trợ cấp, phụ cấp; (3) ủy quyền nhờ trông nom nhà cửa; (4) ủy quyền của thành viên hộ gia đình để vay vốn tại Ngân hàng chính sách xã hội;  hoặc trừ trường hợp pháp luật có quy định khác.',
                'canCuPhapLy' => 'Luật 60/2014/QH13',
                'ketQuaThucHien' => 'Giấy tờ, văn bản được chứng thực chữ ký/điểm chỉ',
            ],

            [
                'maTTHC' => 5,
                'tenTTHC' => 'Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận',
                'maLinhVuc' => '11',
                'maQuayLamViec' => '2',
                'trinhTuThucHien' => '
                + Người yêu cầu chứng thực phải xuất trình bản chính giấy tờ, văn bản làm cơ sở để chứng thực bản sao và bản sao cần chứng thực.
                + Trường hợp người yêu cầu chứng thực chỉ xuất trình bản chính thì cơ quan, tổ chức tiến hành chụp từ bản chính để thực hiện chứng thực, trừ trường hợp cơ quan, tổ chức không có phương tiện để chụp.
                + Người thực hiện chứng thực kiểm tra bản chính, đối chiếu với bản sao, nếu nội dung bản sao đúng với bản chính. Bản chính giấy tờ, văn bản không thuộc các trường hợp bản chính giấy tờ, văn bản không được dùng làm cơ sở để chứng thực bản sao thì thực hiện chứng thực như sau:
                * Ghi đầy đủ lời chứng chứng thực bản sao từ bản chính theo mẫu quy định;
                * Ký, ghi rõ họ tên, đóng dấu của cơ quan, tổ chức thực hiện chứng thực và ghi vào sổ chứng thực.
                Đối với bản sao có từ 02 (hai) trang trở lên thì ghi lời chứng vào trang cuối, nếu bản sao có từ 02 (hai) tờ trở lên thì phải đóng dấu giáp lai.
                Mỗi bản sao được chứng thực từ một bản chính giấy tờ, văn bản hoặc nhiều bản sao được chứng thực từ một bản chính giấy tờ, văn bản trong cùng một thời điểm được ghi một số chứng thực.
                + Người yêu cầu chứng thực nhận kết quả tại nơi nộp hồ sơ.                 ',

                'doiTuongThucHien' => 'Công dân Việt Nam, Cán bộ, công chức, viên chức, Doanh nghiệp, Doanh nghiệp có vốn đầu tư nước ngoài, Tổ chức (không bao gồm doanh nghiệp, HTX), Hợp tác xã',
                'coQuanThucHien' => 'Ủy ban Nhân dân xã, phường, thị trấn., Tổ chức hành nghề công chứng, Cơ quan đại diện có thẩm quyền',

                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => 'Bản chính giấy tờ, văn bản cần chứng thực.
                Bản chính giấy tờ, văn bản không được dùng làm cơ sở để chứng thực bản sao:
                + Bản chính bị tẩy xóa, sửa chữa, thêm, bớt nội dung không hợp lệ.
                + Bản chính bị hư hỏng, cũ nát, không xác định được nội dung.
                + Bản chính đóng dấu mật của cơ quan, tổ chức có thẩm quyền hoặc không đóng dấu mật nhưng ghi rõ không được sao chụp.
                + Bản chính có nội dung trái pháp luật, đạo đức xã hội; tuyên truyền, kích động chiến tranh, chống chế độ xã hội chủ nghĩa Việt Nam; xuyên tạc lịch sử của dân tộc Việt Nam; xúc phạm danh dự, nhân phẩm, uy tín của cá nhân, tổ chức; vi phạm quyền công dân.
                + Giấy tờ, văn bản do cá nhân tự lập nhưng không có xác nhận và đóng dấu của cơ quan, tổ chức có thẩm quyền.',
                'canCuPhapLy' => 'Luật 60/2014/QH13',
                'ketQuaThucHien' => 'Bản sao được chứng thực từ bản chính',
            ],

            [
                'maTTHC' => 6,
                'tenTTHC' => 'Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi',
                'maLinhVuc' => '1',
                'maQuayLamViec' => '5',
                'trinhTuThucHien' => '
                Người có yêu cầu nộp hồ sơ tại Bộ phận tiếp nhận và trả kết quả theo cơ chế “Một cửa” của Ủy ban nhân dân cấp xã.
                Cán bộ, công chức tiếp nhận hồ sơ có trách nhiệm kiểm tra giấy tờ trong hồ sơ, nếu hồ sơ đầy đủ, hợp lệ thì tiếp nhận, viết giấy nhận hồ sơ, hẹn trả kết quả từng loại việc cho người đi đăng ký; nếu hồ sơ thiếu hoặc không hợp lệ thì hướng dẫn người có yêu cầu bổ sung hoàn thiện hồ sơ; văn bản hướng dẫn phải ghi đầy đủ, rõ ràng loại giấy tờ cần bổ sung, hoàn thiện; cán bộ tiếp nhận hồ sơ ký, ghi rõ họ tên và giao cho người nộp hồ sơ.
                Công chức Tư pháp - hộ tịch cấp xã đăng ký khai sinh cho trẻ em ngay trong ngày tiếp nhận hồ sơ
                Sau khi công chức Tư pháp – hộ tịch cấp xã thực hiện việc đăng ký khai sinh và cấp Giấy khai sinh cho trẻ em, cán bộ, công chức được giao nhiệm vụ lập lập hồ sơ cấp Thẻ bảo hiểm y tế chuyển cho Bảo hiểm xã hội cấp huyện để cấp Thẻ bảo hiểm y tế cho trẻ em.
                Cơ quan Bảo hiểm xã hội kiểm tra hồ sơ, nếu thấy đầy đủ, hợp lệ thì thực hiện cấp Thẻ bảo hiểm y tế cho trẻ em (trong thời hạn 10 ngày).
                Trường hợp hồ sơ chưa đầy đủ thì thông báo cho Ủy ban nhân dân cấp xã biết, hoàn thiện.
                Cơ quan Bảo hiểm xã hội chuyển trả Thẻ bảo hiểm y tế cho trẻ em về Ủy ban nhân dân cấp xã.
                Bộ phận tiếp nhận và trả kết quả theo cơ chế “Một cửa” tại Ủy ban nhân dân cấp xã trả kết quả cho người có yêu cầu.
                Khi trả Giấy khai sinh cho người có yêu cầu, công chức Tư pháp – hộ tịch ghi vào Sổ đăng ký khai sinh, yêu cầu người có yêu cầu ký tên vào Sổ đăng ký khai sinh và Giấy khai sinh.
            ',
                'doiTuongThucHien' => 'Công dân Việt Nam, Cán bộ, công chức, viên chức',
                'coQuanThucHien' => 'Ủy ban Nhân dân xã, phường, thị trấn., Công đoàn cơ quan BHXH Việt Nam',
                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => '- Việc đăng ký khai sinh cho trẻ em dưới 6 tuổi thuộc thẩm quyền giải quyết của Ủy ban nhân dân cấp xã.
                - Các cơ quan có thẩm quyền thực hiện liên thông các thủ tục hành chính phải cùng thuộc địa bàn một huyện, thị xã, thành phố thuộc tỉnh hoặc cùng thuộc địa bàn một quận, huyện, thị xã thuộc thành phố trực thuộc trung ương.',
                'canCuPhapLy' => 'Luật 60/2014/QH13',
                'ketQuaThucHien' => 'Giấy khai sinh',
            ],

            [
                'maTTHC' => 7,
                'tenTTHC' => 'Gia hạn giấy phép xây dựng đối với công trình cấp III, cấp IV (công trình Không theo tuyến/Theo tuyến trong đô thị/Tín ngưỡng, tôn giáo/Tượng đài, tranh hoành tráng/Sửa chữa, cải tạo/Theo giai đoạn cho công trình không theo tuyến/Theo giai đoạn cho công trình theo tuyến trong đô thị/Dự án) và nhà ở riêng lẻ',
                'maLinhVuc' => '2',
                'maQuayLamViec' => '2',
                'trinhTuThucHien' => '- Chủ đầu tư nộp hồ sơ đề nghị cấp gia hạn giấy phép xây dựng cho Trung tâm phục vụ hành chính công hoặc Bộ phận tiếp nhận và trả kết quả giải quyết thủ tục hành chính của Ủy ban nhân dân cấp xã.
            - Trong thời hạn 05 ngày làm việc kể từ ngày nhận đủ hồ sơ hợp lệ, cơ quan có thẩm quyền có trách nhiệm xem xét gia hạn giấy phép xây dựng.',
                'doiTuongThucHien' => 'Công dân Việt Nam, Cán bộ, công chức, viên chức, Doanh nghiệp, Tổ chức (không bao gồm doanh nghiệp, HTX), Hợp tác xãCông dân Việt Nam, Cán bộ, công chức, viên chức',
                'coQuanThucHien' => 'Ủy ban nhân dân cấp xã',
                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => 'Không có.',
                'canCuPhapLy' => 'Luật Xây dựng năm 2014, Luật sửa đổi, bổ sung một số điều của Luật Xây dựng năm 2020, Nghị định số 175/2024/NĐ-CP của Chính phủ quy định chi tiết một số điều và biện pháp thi hành Luật Xây dựng về quản lý hoạt động xây dựng, quy định về phân định thẩm quyền của chính quyền địa phương 02 cấp trong lĩnh vực quản lý nhà nước của Bộ Xây dựng, quy định về phân quyền, phân cấp trong lĩnh vực quản lý nhà nước của Bộ Xây dựng, quy định chi tiết một số điều và biện pháp thi hành luật phòng cháy, chữa cháy và cứu nạn, cứu hộ',
                'ketQuaThucHien' => 'Giấy phép xây dựng được gia hạn.',
            ],
            [
                'maTTHC' => 8,
                'tenTTHC' => 'Thủ tục cấp Giấy xác nhận tình trạng hôn nhân',
                'maLinhVuc' => '2',
                'maQuayLamViec' => '1',
                'trinhTuThucHien' => '- Nếu lựa chọn hình thức nộp hồ sơ trực tiếp, người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân nộp hồ sơ yêu cầu cấp Giấy xác nhận tình trạng hôn nhân tại Trung tâm phục vụ hành chính công có thẩm quyền nộp lệ phí nếu thuộc trường hợp phải nộp lệ phí cấp Giấy xác nhận tình trạng hôn nhân.
                - Nếu lựa chọn hình thức nộp hồ sơ trực tuyến, người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân truy cập Cổng dịch vụ công quốc gia , xác thực người dùng theo hướng dẫn, đăng nhập vào hệ thống, lựa chọn Ủy ban nhân dân cấp xã có thẩm quyền.
                Người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân trực tuyến cung cấp thông tin theo mẫu điện tử tương tác cấp Giấy xác nhận tình trạng hôn nhân (cung cấp trên Cổng dịch vụ công), đính kèm bản chụp hoặc bản sao điện tử các giấy tờ, tài liệu theo quy định; nộp phí, lệ phí thông qua chức năng thanh toán trực tuyến hoặc bằng cách thức khác theo quy định pháp luật, hoàn tất việc nộp hồ sơ.
                - Cán bộ tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công có trách nhiệm kiểm tra tính chính xác, đầy đủ, thống nhất, hợp lệ của hồ sơ.
                (i) Trường hợp hồ sơ đầy đủ, hợp lệ thì tiếp nhận hồ sơ; có Phiếu hẹn, trả kết quả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp) hoặc gửi ngay Phiếu hẹn, trả kết quả qua thư điện tử hoặc gửi tin nhắn hẹn trả kết quả qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến); chuyển hồ sơ để công chức tư pháp - hộ tịch xử lý; trường hợp tiếp nhận hồ sơ tại Trung tâm Phục vụ hành chính công cấp tỉnh, cán bộ tiếp nhận hồ sơ chuyển hồ sơ đến Ủy ban nhân dân cấp xã có thẩm quyền xử lý.
                (ii) Trường hợp hồ sơ chưa đầy đủ, hợp lệ thì cán bộ tiếp nhận hồ sơ thông báo cho người yêu cầu bổ sung, hoàn thiện hồ sơ, nêu rõ loại giấy tờ, nội dung cần bổ sung để người yêu cầu bổ sung, hoàn thiện. Sau khi hồ sơ được bổ sung, thực hiện lại bước (i);
                (iii) Nếu người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân không bổ sung, hoàn thiện được hồ sơ thì báo cáo Lãnh đạo Trung tâm Phục vụ hành chính công có văn bản từ chối giải quyết yêu cầu cấp Giấy xác nhận tình trạng hôn nhân.
                - Công chức tư pháp - hộ tịch thẩm tra hồ sơ (thẩm tra tính thống nhất, hợp lệ của các thông tin trong hồ sơ, giấy tờ, tài liệu đính kèm).
                + Trường hợp hồ sơ cần bổ sung, hoàn thiện hoặc không đủ điều kiện giải quyết, phải từ chối thì công chức tư pháp - hộ tịch gửi thông báo về tình trạng hồ sơ tới Trung tâm Phục vụ hành chính công để thông báo cho người nộp hồ sơ – thực hiện lại bước (ii) hoặc (iii);
                + Trường hợp cần phải kiểm tra, xác minh làm rõ hoặc do nguyên nhân khác mà không thể trả kết quả đúng thời gian đã hẹn thì công chức tư pháp - hộ tịch lập Phiếu xin lỗi và hẹn lại ngày trả kết quả, trong đó nêu rõ lý do chậm trả kết quả và thời gian hẹn trả kết quả, chuyển Trung tâm Phục vụ hành chính công để trả cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tiếp), hoặc gửi Phiếu xin lỗi và hẹn lại ngày trả kết quả qua thư điện tử hoặc gửi tin nhắn qua điện thoại di động cho người yêu cầu (nếu người yêu cầu nộp hồ sơ trực tuyến).
                - Trường hợp người yêu cầu cấp lại Giấy xác nhận tình trạng hôn nhân để kết hôn mà không nộp lại được Giấy xác nhận tình trạng hôn nhân đã được cấp trước đây, thì người yêu cầu phải trình bày rõ lý do không nộp lại được Giấy xác nhận tình trạng hôn nhân. Trong thời hạn 03 ngày làm việc kể từ ngày tiếp nhận hồ sơ, cơ quan đăng ký hộ tịch có văn bản trao đổi với nơi dự định đăng ký kết hôn trước đây để xác minh. Trường hợp không xác minh được hoặc không nhận được kết quả xác minh thì cơ quan đăng ký hộ tịch cho phép người yêu cầu lập văn bản cam đoan về tình trạng hôn nhân.
                - Trường hợp tiếp nhận hồ sơ cấp Giấy xác nhận tình trạng hôn nhân theo hình thức trực tiếp, sau khi hoàn tất thủ tục thì công chức tư pháp - hộ tịch cập nhật thông tin xác nhận tình trạng hôn nhân trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung.
                Trường hợp tiếp nhận hồ sơ xác nhận tình trạng hôn nhân theo hình thức trực tuyến, công chức tư pháp - hộ tịch gửi lại biểu mẫu Giấy xác nhận tình trạng hôn nhân điện tử với thông tin đầy đủ cho người yêu cầu qua thư điện tử hoặc thiết bị số.
                Người yêu cầu có trách nhiệm kiểm tra tính chính xác, đầy đủ của các thông tin trên biểu mẫu Giấy xác nhận tình trạng hôn nhân điện tử và xác nhận (tối đa một ngày).
                Nếu người yêu cầu xác nhận thông tin đã thống nhất, đầy đủ hoặc không có phản hồi sau thời hạn một ngày thì công chức tư pháp - hộ tịch cập nhật thông tin xác nhận tình trạng hôn nhân trên Phần mềm đăng ký, quản lý hộ tịch điện tử dùng chung.
                - Công chức tư pháp - hộ tịch in Giấy xác nhận tình trạng hôn nhân, trình Lãnh đạo Ủy ban nhân dân cấp xã ký, chuyển tới Trung tâm Phục vụ hành chính công để trả kết quả cho người yêu cầu.
                - Trong thời hạn 05 ngày làm việc kể từ ngày nhận đủ hồ sơ hợp lệ, cơ quan có thẩm quyền có trách nhiệm xem xét gia hạn giấy phép xây dựng.',
                'doiTuongThucHien' => 'Công dân Việt Nam, Người Việt Nam định cư ở nước ngoài',
                'coQuanThucHien' => 'Ủy ban nhân dân cấp xã',
                'trangThai' => 'Công khai',
                'yeuCauDieuKien' => 'Không có.',
                'canCuPhapLy' => 'Luật Xây dựng năm 2014, Luật sửa đổi, bổ sung một số điều của Luật Xây dựng năm 2020, Nghị định số 175/2024/NĐ-CP của Chính phủ quy định chi tiết một số điều và biện pháp thi hành Luật Xây dựng về quản lý hoạt động xây dựng, quy định về phân định thẩm quyền của chính quyền địa phương 02 cấp trong lĩnh vực quản lý nhà nước của Bộ Xây dựng, quy định về phân quyền, phân cấp trong lĩnh vực quản lý nhà nước của Bộ Xây dựng, quy định chi tiết một số điều và biện pháp thi hành luật phòng cháy, chữa cháy và cứu nạn, cứu hộ',
                'ketQuaThucHien' => 'Giấy xác nhận tình trạng hôn nhân',
            ],
        ]);
    }
}
