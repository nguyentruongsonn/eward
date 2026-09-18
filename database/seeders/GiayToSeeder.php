<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiayToSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('giayto')->insert([
            // Cấp bản sao trích lục hộ tịch, bản sao giấy khai sinh
            [
                'maGiayTo' => 1,
                'tenGiayTo' => 'Văn bản ủy quyền theo quy định của pháp luật trong trường hợp ủy quyền thực hiện yêu cầu cấp bản sao Trích lục hộ tịch. Trường hợp người được ủy quyền là ông, bà, cha, mẹ, con, vợ, chồng, anh, chị, em ruột của người ủy quyền thì văn bản ủy quyền không phải chứng thực.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 2,
                'tenGiayTo' => 'Trường hợp gửi hồ sơ qua hệ thống bưu chính thì phải gửi kèm theo bản sao có chứng thực các giấy tờ phải xuất trình nêu trên.',
                'loaiGiayTo' => 'Mẫu đơn',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 3,
                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu cấp bản sao Trích lục hộ tịch. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' => 'Mẫu đơn',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 4,
                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu đăng ký kết hôn. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến);',
                'loaiGiayTo' => 'Giấy tờ cá nhân',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 5,
                'tenGiayTo' => '-- Cá nhân có quyền lựa chọn thực hiện thủ tục hành chính về hộ tịch tại cơ quan đăng ký hộ tịch nơi cư trú; nơi cư trú của cá nhân được xác định theo quy định của pháp luật về cư trú. Trường hợp cá nhân lựa chọn thực hiện thủ tục hành chính về hộ tịch không phải tại Ủy ban nhân dân cấp xã nơi thường trú hoặc nơi tạm trú thì Ủy ban nhân dân cấp xã nơi tiếp nhận yêu cầu có trách nhiệm hỗ trợ người dân nộp hồ sơ đăng ký hộ tịch trực tuyến đến đúng cơ quan có thẩm quyền theo quy định.',
                'loaiGiayTo' => 'Giấy tờ cá nhân',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 6,
                'tenGiayTo' => '- Đối với giấy tờ nộp, xuất trình nếu người yêu cầu nộp hồ sơ theo hình thức trực tiếp:',
                'loaiGiayTo' => 'Giấy tờ cá nhân',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 7,
                'tenGiayTo' => '+ Người yêu cầu đăng ký hộ tịch có thể nộp bản sao được chứng thực từ bản chính hoặc bản sao được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản chụp kèm theo bản chính, bản điện tử các giấy tờ này, bao gồm cả giấy tờ được tích hợp, hiển thị trên Ứng dụng định danh điện tử (VneID).',
                'loaiGiayTo' => 'Giấy tờ cá nhân',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 8,
                'tenGiayTo' => 'Trường hợp người yêu cầu nộp bản chụp kèm theo bản chính giấy tờ thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó.',
                'loaiGiayTo' => 'Giấy tờ cá nhân',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 9,
                'tenGiayTo' => '+ Đối với giấy tờ xuất trình khi đăng ký hộ tịch, người tiếp nhận có trách nhiệm kiểm tra, đối chiếu với thông tin trong tờ khai, chụp lại hoặc ghi lại thông tin để lưu trong hồ sơ và trả lại cho người xuất trình, không được yêu cầu nộp bản sao hoặc bản chụp giấy tờ đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 10,
                'tenGiayTo' => '+ Người tiếp nhận có trách nhiệm tiếp nhận đúng, đủ hồ sơ đăng ký hộ tịch theo quy định của pháp luật hộ tịch, không được yêu cầu người đăng ký hộ tịch nộp thêm giấy tờ mà pháp luật hộ tịch không quy định phải nộp .	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 11,
                'tenGiayTo' => '+ Người tiếp nhận hồ sơ thực hiện khai thác thông tin trong CSDLQGVDC theo quy định pháp luật nếu người yêu cầu đăng ký hộ tịch đã cung cấp họ, chữ đệm, tên; ngày, tháng, năm sinh; số định danh cá nhân/thẻ căn cước công dân/thẻ căn cước/căn cước điện tử. Trường hợp các thông tin cần khai thác không có trong CSDLQGVDC thì đề nghị người yêu cầu kê khai đầy đủ.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 12,
                'tenGiayTo' => '- Đối với giấy tờ gửi kèm theo nếu người yêu cầu nộp hồ sơ theo hình thức trực tuyến:	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 13,
                'tenGiayTo' => '+ Bản chụp các giấy tờ gửi kèm theo hồ sơ cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tuyến phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung, là bản chụp bằng máy ảnh, điện thoại hoặc được chụp, được quét bằng thiết bị điện tử, từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 14,
                'tenGiayTo' => '+ Trường hợp giấy tờ, tài liệu phải gửi kèm trong hồ sơ cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tuyến đã có bản sao điện tử hoặc bản điện tử giấy tờ hộ tịch thì người yêu cầu được sử dụng bản điện tử này.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 15,
                'tenGiayTo' => '- Trường hợp người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh không cung cấp đầy đủ hoặc cung cấp các thông tin không chính xác, không thể tra cứu được thông tin thì cơ quan đăng ký hộ tịch từ chối giải quyết.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 16,
                'tenGiayTo' => '- Giấy tờ do cơ quan có thẩm quyền của nước ngoài cấp, công chứng hoặc xác nhận để sử dụng cho việc đăng ký hộ tịch tại Việt Nam phải được hợp pháp hóa lãnh sự theo quy định của pháp luật, trừ trường hợp được miễn theo điều ước quốc tế mà Việt Nam là thành viên.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 17,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng k‎ý hộ tịch lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch phải xuất trình giấy tờ tuỳ thân, nộp các giấy tờ là thành phần hồ sơ theo quy định.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 18,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng ký hộ tịch không lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch nộp các giấy tờ là thành phần hồ sơ theo quy định trước khi nhận kết quả.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 19,
                'tenGiayTo' => '- Tờ khai đề nghị bản sao Trích lục hộ tịch theo mẫu trong trường hợp người yêu cầu là cá nhân hoặc Văn bản yêu cầu cấp bản sao Trích lục hộ tịch nêu rõ lý do trong trường hợp người yêu cầu là cơ quan, tổ chức (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',

            ],
            [
                'maGiayTo' => 20,
                'tenGiayTo' => '- Mẫu điện tử tương tác yêu cầu cấp bản sao Giấy khai sinh, bản sao Trích lục hộ tịch (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 21,
                'tenGiayTo' => '- Người có yêu cầu cấp bản sao Trích lục hộ tịch thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // ================Thủ tục đăng ký kết hôn
            [
                'maGiayTo' => 22,
                'tenGiayTo' => '- Tờ khai đăng ký kết hôn theo mẫu, có đủ thông tin của hai bên nam, nữ. Hai bên nam, nữ có thể khai chung vào một Tờ khai đăng ký kết hôn (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',
            ],

            [
                'maGiayTo' => 23,
                'tenGiayTo' => '- Mẫu hộ tịch điện tử tương tác đăng ký kết hôn (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 24,
                'tenGiayTo' => '- Người có yêu cầu đăng ký kết hôn thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 25,
                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu đăng ký kết hôn. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến);',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 26,
                'tenGiayTo' => '- Giấy tờ có giá trị chứng minh thông tin về cư trú trong trường hợp cơ quan đăng ký hộ tịch không thể khai thác được thông tin về nơi cư trú của công dân theo các phương thức quy định tại khoản 2 Điều 14 Nghị định số 104/2022/NĐ-CP ngày 21/12/2022 của Chính phủ. Trường hợp các thông tin về giấy tờ chứng minh nơi cư trú đã được khai thác từ Cơ sở dữ liệu quốc gia về dân cư bằng các phương thức này thì người có yêu cầu không phải xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 27,
                'tenGiayTo' => '- Cá nhân có quyền lựa chọn thực hiện thủ tục hành chính về hộ tịch tại cơ quan đăng ký hộ tịch nơi cư trú; nơi cư trú của cá nhân được xác định theo quy định của pháp luật về cư trú. Trường hợp cá nhân lựa chọn thực hiện thủ tục hành chính về hộ tịch không phải tại Ủy ban nhân dân cấp xã nơi thường trú hoặc nơi tạm trú thì Ủy ban nhân dân cấp xã nơi tiếp nhận yêu cầu có trách nhiệm hỗ trợ người dân nộp hồ sơ đăng ký hộ tịch trực tuyến đến đúng cơ quan có thẩm quyền theo quy định..',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 28,
                'tenGiayTo' => '- Đối với yêu cầu đăng ký kết hôn, cơ quan đăng ký hộ tịch tra cứu thông tin về tình trạng hôn nhân của người yêu cầu đăng ký kết hôn trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư. Kết quả tra cứu được lưu trữ dưới dạng điện tử hoặc bản giấy, phản ánh đầy đủ, chính xác thông tin tại thời điểm tra cứu và đính kèm hồ sơ của người đăng ký. Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 29,
                'tenGiayTo' => '- Nếu bên kết hôn là công dân Việt Nam đã ly hôn hoặc hủy việc kết hôn tại cơ quan có thẩm quyền nước ngoài nhưng qua tra cứu thông tin trong Cơ sở dữ liệu hộ tịch điện tử; thông qua kết nối giữa Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh với Cơ sở dữ liệu hộ tịch điện tử, Cơ sở dữ liệu quốc gia về dân cư không thể hiện thông tin về việc đã ghi chú ly hôn, hủy việc kết hôn thì cơ quan đăng ký hộ tịch hướng dẫn công dân thực hiện thủ tục ghi vào sổ hộ tịch việc ly hôn/hủy việc kết hôn tại cơ quan nhà nước có thẩm quyền trước khi giải quyết việc đăng ký kết hôn.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 30,
                'tenGiayTo' => '- Đối với giấy tờ nộp, xuất trình nếu người yêu cầu nộp hồ sơ theo hình thức trực tiếp:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 31,
                'tenGiayTo' => '+ Người yêu cầu đăng ký hộ tịch có thể nộp bản sao được chứng thực từ bản chính hoặc bản sao được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản chụp kèm theo bản chính, bản điện tử các giấy tờ này, bao gồm cả giấy tờ được tích hợp, hiển thị trên Ứng dụng định danh điện tử (VneID).',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 32,
                'tenGiayTo' => 'Trường hợp người yêu cầu nộp bản chụp kèm theo bản chính giấy tờ thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 33,
                'tenGiayTo' => '+ Đối với giấy tờ xuất trình khi đăng ký hộ tịch, người tiếp nhận có trách nhiệm kiểm tra, đối chiếu với thông tin trong tờ khai, chụp lại hoặc ghi lại thông tin để lưu trong hồ sơ và trả lại cho người xuất trình, không được yêu cầu nộp bản sao hoặc bản chụp giấy tờ đó.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 34,
                'tenGiayTo' => '+ Người tiếp nhận có trách nhiệm tiếp nhận đúng, đủ hồ sơ đăng ký hộ tịch theo quy định của pháp luật hộ tịch, không được yêu cầu người đăng ký hộ tịch nộp thêm giấy tờ mà pháp luật hộ tịch không quy định phải nộp .	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 35,
                'tenGiayTo' => '+ Người tiếp nhận hồ sơ thực hiện khai thác thông tin trong CSDLQGVDC theo quy định pháp luật nếu người yêu cầu đăng ký hộ tịch đã cung cấp họ, chữ đệm, tên; ngày, tháng, năm sinh; số định danh cá nhân/thẻ căn cước công dân/thẻ căn cước/căn cước điện tử. Trường hợp các thông tin cần khai thác không có trong CSDLQGVDC thì đề nghị người yêu cầu kê khai đầy đủ.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 36,
                'tenGiayTo' => '- Đối với giấy tờ gửi kèm theo nếu người yêu cầu nộp hồ sơ theo hình thức trực tuyến:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 37,
                'tenGiayTo' => '+ Bản chụp các giấy tờ gửi kèm theo hồ sơ đăng ký kết hôn trực tuyến phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung, là bản chụp bằng máy ảnh, điện thoại hoặc được chụp, được quét bằng thiết bị điện tử, từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 38,
                'tenGiayTo' => '+ Trường hợp giấy tờ, tài liệu phải gửi kèm trong hồ sơ đăng ký kết hôn trực tuyến đã có bản sao điện tử hoặc đã có bản điện tử giấy tờ hộ tịch thì người yêu cầu được sử dụng bản điện tử này.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 39,
                'tenGiayTo' => '- Giấy tờ do cơ quan có thẩm quyền của nước ngoài cấp, công chứng hoặc xác nhận để sử dụng cho việc đăng ký hộ tịch tại Việt Nam phải được hợp pháp hóa lãnh sự theo quy định của pháp luật, trừ trường hợp được miễn theo điều ước quốc tế mà Việt Nam là thành viên.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 40,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng ký kết hôn không cung cấp được giấy tờ nêu trên theo quy định hoặc giấy tờ nộp, xuất trình bị tẩy xóa, sửa chữa, làm giả thì cơ quan đăng ký hộ tịch có thẩm quyền hủy bỏ kết quả đăng ký kết hôn.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // ===========Thủ tục đăng ký khai sinh
            // Bao gồm
            [
                'maGiayTo' => 41,
                'tenGiayTo' => '- Tờ khai đăng ký khai sinh theo mẫu (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp hoặc gửi hồ sơ qua hệ thống bưu chính);',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 42,
                'tenGiayTo' => '- Mẫu hộ tịch điện tử tương tác đăng ký khai sinh (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công, nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 43,
                'tenGiayTo' => '- Người có yêu cầu đăng ký khai sinh thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // Giấy tờ phải nộp
            [
                'maGiayTo' => 44,
                'tenGiayTo' => '- Giấy chứng sinh; trường hợp không có Giấy chứng sinh thì nộp văn bản của người làm chứng xác nhận về việc sinh; nếu không có người làm chứng thì phải có giấy cam đoan về việc sinh.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',
            ],

            [
                'maGiayTo' => 45,
                'tenGiayTo' => 'Trường hợp người yêu cầu đã nộp bản điện tử Giấy chứng sinh hoặc cơ quan đăng ký hộ tịch đã khai thác được dữ liệu điện tử có ký số của Giấy chứng sinh thì không phải nộp bản giấy.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 46,
                'tenGiayTo' => '- Trường hợp trẻ em bị bỏ rơi thì phải có biên bản về việc trẻ bị bỏ rơi do cơ quan có thẩm quyền lập.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 47,
                'tenGiayTo' => '- Trường hợp khai sinh cho trẻ em sinh ra do mang thai hộ phải có văn bản xác nhận của cơ sở y tế đã thực hiện kỹ thuật hỗ trợ sinh sản cho việc mang thai hộ.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 48,
                'tenGiayTo' => '- Văn bản ủy quyền (được chứng thực) theo quy định của pháp luật trong trường hợp ủy quyền thực hiện việc đăng ký khai sinh. Trường hợp người được ủy quyền là ông, bà, cha, mẹ, con, vợ, chồng, anh, chị, em ruột của người ủy quyền thì văn bản ủy quyền không phải chứng thực.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // Giấy tờ phải xuất trình
            [
                'maGiayTo' => 49,
                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu đăng ký khai sinh. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, Cơ sở dữ liệu hộ tịch điện tử (CSDLHTĐT), được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến)	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 50,

                'tenGiayTo' => '- Giấy tờ có giá trị chứng minh thông tin về cư trú trong trường hợp cơ quan đăng ký hộ tịch không thể khai thác được thông tin về nơi cư trú của công dân theo các phương thức quy định tại khoản 2 Điều 14 Nghị định số 104/2022/NĐ-CP ngày 21/12/2022 của Chính phủ. Trường hợp các thông tin về giấy tờ chứng minh nơi cư trú đã được khai thác từ Cơ sở dữ liệu quốc gia về dân cư bằng các phương thức này thì người có yêu cầu không phải xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 51,
                'tenGiayTo' => 'Trường hợp gửi hồ sơ qua hệ thống bưu chính thì phải gửi kèm theo bản sao có chứng thực các giấy tờ phải xuất trình nêu trên.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // Lưu ý
            [
                'maGiayTo' => 52,
                'tenGiayTo' => '- Cá nhân có quyền lựa chọn thực hiện thủ tục hành chính về hộ tịch tại cơ quan đăng ký hộ tịch nơi cư trú; nơi cư trú của cá nhân được xác định theo quy định của pháp luật về cư trú. Trường hợp cá nhân lựa chọn thực hiện thủ tục hành chính về hộ tịch không phải tại Ủy ban nhân dân cấp xã nơi thường trú hoặc nơi tạm trú thì Ủy ban nhân dân cấp xã nơi tiếp nhận yêu cầu có trách nhiệm hỗ trợ người dân nộp hồ sơ đăng ký hộ tịch trực tuyến đến đúng cơ quan có thẩm quyền theo quy định.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 53,
                'tenGiayTo' => '- Đối với giấy tờ nộp, xuất trình nếu người yêu cầu nộp hồ sơ theo hình thức trực tiếp:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 54,
                'tenGiayTo' => '+ Người yêu cầu đăng ký hộ tịch có thể nộp bản sao được chứng thực từ bản chính hoặc bản sao được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản chụp kèm theo bản chính, bản điện tử các giấy tờ này, bao gồm cả giấy tờ được tích hợp, hiển thị trên Ứng dụng định danh điện tử (VneID).',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 55,

                'tenGiayTo' => 'Trường hợp người yêu cầu nộp bản chụp kèm theo bản chính giấy tờ thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 56,
                'tenGiayTo' => 'Đối với giấy tờ xuất trình khi đăng ký hộ tịch, người tiếp nhận có trách nhiệm kiểm tra, đối chiếu, ghi lại thông tin hoặc chụp lại, ký xác nhận để lưu trong hồ sơ và trả lại cho người xuất trình, không được yêu cầu nộp bản sao hoặc bản chụp giấy tờ đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 57,
                'tenGiayTo' => '+ Người tiếp nhận có trách nhiệm tiếp nhận đúng, đủ hồ sơ đăng ký hộ tịch theo quy định của pháp luật hộ tịch, không được yêu cầu người đăng ký hộ tịch nộp thêm giấy tờ mà pháp luật hộ tịch không quy định phải nộp .',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 58,
                'tenGiayTo' => 'Người tiếp nhận hồ sơ thực hiện khai thác thông tin trong Cơ sở dữ liệu quốc gia về dân cư theo quy định pháp luật nếu người yêu cầu đăng ký hộ tịch đã cung cấp họ, chữ đệm, tên; ngày, tháng, năm sinh; số định danh cá nhân/căn cước công dân/thẻ căn cước/chứng minh nhân dân. Trường hợp các thông tin cần khai thác không có trong Cơ sở dữ liệu quốc gia về dân cư thì đề nghị người yêu cầu kê khai đầy đủ .',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 59,
                'tenGiayTo' => '- Đối với giấy tờ gửi kèm theo nếu người yêu cầu nộp hồ sơ theo hình thức trực tuyến:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 60,
                'tenGiayTo' => '+ Bản chụp các giấy tờ gửi kèm theo hồ sơ đăng ký khai sinh trực tuyến phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung, là bản chụp bằng máy ảnh, điện thoại hoặc được chụp, được quét bằng thiết bị điện tử, từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 61,
                'tenGiayTo' => '+ Trường hợp giấy tờ, tài liệu phải gửi kèm trong hồ sơ đăng ký khai sinh trực tuyến đã có bản sao điện tử hoặc đã có bản điện tử giấy tờ hộ tịch thì người yêu cầu được sử dụng bản điện tử này.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 62,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng k‎ý hộ tịch lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch phải xuất trình giấy tờ tuỳ thân, nộp các giấy tờ là thành phần hồ sơ theo quy định; Trường hợp người yêu cầu đăng ký hộ tịch không lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch nộp các giấy tờ là thành phần hồ sơ theo quy định trước khi nhận kết quả.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 63,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng ký hộ tịch không cung cấp được giấy tờ theo quy định hoặc giấy tờ nộp, xuất trình bị tẩy xóa, sửa chữa, làm giả thì cơ quan đăng ký hộ tịch có thẩm quyền hủy bỏ kết quả đăng ký hộ tịch.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 64,
                'tenGiayTo' => '- Giấy tờ do cơ quan có thẩm quyền của nước ngoài cấp, công chứng hoặc xác nhận để sử dụng cho việc đăng ký hộ tịch tại Việt Nam phải được hợp pháp hóa lãnh sự theo quy định của pháp luật, trừ trường hợp được miễn theo điều ước quốc tế mà Việt Nam là thành viên.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 65,
                'tenGiayTo' => '- Trường hợp người đi đăng ký khai sinh cho trẻ em là ông, bà, người thân thích khác thì không phải có văn bản ủy quyền của cha, mẹ trẻ em, nhưng phải thống nhất với cha, mẹ trẻ em về các nội dung khai sinh.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 66,
                'tenGiayTo' => '- Đối với việc xác định họ, dân tộc, quê quán, đặt tên cho trẻ:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 67,
                'tenGiayTo' => '+ Việc xác định họ, dân tộc, đặt tên cho trẻ em phải phù hợp với pháp luật và yêu cầu giữ gìn bản sắc dân tộc, tập quán, truyền thống văn hóa tốt đẹp của Việt Nam; không đặt tên quá dài, khó sử dụng.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 68,
                'tenGiayTo' => '+ Trường hợp cha, mẹ không thỏa thuận được về họ, dân tộc, quê quán của con khi đăng ký khai sinh thì họ, dân tộc, quê quán của con được xác định theo tập quán nhưng phải bảo đảm theo họ, dân tộc, quê quán của cha hoặc mẹ.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 69,
                'tenGiayTo' => '- Trường hợp cho phép người yêu cầu đăng ký hộ tịch lập văn bản cam đoan về nội dung yêu cầu đăng ký hộ tịch thì cơ quan đăng ký hộ tịch phải giải thích rõ cho người lập văn bản cam đoan về trách nhiệm, hệ quả pháp lý của việc cam đoan không đúng sự thật.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 70,
                'tenGiayTo' => 'Cơ quan đăng ký hộ tịch từ chối giải quyết hoặc đề nghị cơ quan có thẩm quyền hủy bỏ kết quả đăng ký hộ tịch, nếu có cơ sở xác định nội dung cam đoan không đúng sự thật.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // ==============Thủ tục chứng thực chữ ký trong các giấy tờ, văn bản (áp dụng cho cả trường hợp chứng thực điểm chỉ và trường hợp người yêu cầu chứng thực không thể ký, không thể điểm chỉ được)
            [
                'maGiayTo' => 71,
                'tenGiayTo' => '+ Bản chính hoặc bản sao có chứng thực Thẻ căn cước công dân/Thẻ căn cước/Giấy chứng nhận căn cước/Hộ chiếu/giấy tờ xuất nhập cảnh/giấy tờ có giá trị đi lại quốc tế còn giá trị sử dụng hoặc Căn cước điện tử.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',

            ],
            [
                'maGiayTo' => 72,
                'tenGiayTo' => '+ Giấy tờ, văn bản mà mình sẽ yêu cầu chứng thực chữ ký. Trường hợp chứng thực chữ ký trong giấy tờ, văn bản bằng tiếng nước ngoài, nếu người thực hiện chứng thực không hiểu rõ nội dung của giấy tờ, văn bản thì có quyền yêu cầu người yêu cầu chứng thực nộp kèm theo bản dịch ra tiếng Việt nội dung của giấy tờ, văn bản đó (bản dịch không cần công chứng hoặc chứng thực chữ ký người dịch, người yêu cầu chứng thực phải chịu trách nhiệm về nội dung của bản dịch). + Người yêu cầu chứng thực nhận kết quả tại nơi nộp hồ sơ.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // =============Chứng thực bản sao từ bản chính giấy tờ, văn bản do cơ quan, tổ chức có thẩm quyền của Việt Nam; cơ quan, tổ chức có thẩm quyền của nước ngoài; cơ quan, tổ chức có thẩm quyền của Việt Nam liên kết với cơ quan, tổ chức có thẩm quyền của nước ngoài cấp hoặc chứng nhận
            [
                'maGiayTo' => 73,
                'tenGiayTo' => 'Bản chính giấy tờ, văn bản làm cơ sở để chứng thực bản sao và bản sao cần chứng thực. Trường hợp người yêu cầu chứng thực chỉ xuất trình bản chính thì cơ quan, tổ chức tiến hành chụp từ bản chính để thực hiện chứng thực, trừ trường hợp cơ quan, tổ chức không có phương tiện để chụp. Bản sao từ bản chính để thực hiện chứng thực phải có đầy đủ các trang đã ghi thông tin của bản chính.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // ===========Liên thông các thủ tục hành chính về đăng ký khai sinh, cấp Thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi
            [
                'maGiayTo' => 74,
                'tenGiayTo' => '- Tờ khai đăng ký khai sinh theo mẫu quy định.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',
            ],
            [
                'maGiayTo' => 75,
                'tenGiayTo' => '- Giấy chứng sinh do cơ sở y tế nơi trẻ em sinh ra cấp; nếu trẻ em sinh ra ngoài cơ sở y tế thì giấy chứng sinh được thay bằng văn bản xác nhận của người làm chứng; trường hợp không có người làm chứng thì người đi khai sinh phải làm giấy cam đoan về việc sinh là có thực. Đối với trường hợp trẻ em bị bỏ rơi thì nộp biên bản về việc trẻ em bị bỏ rơi thay cho giấy chứng sinh.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',
            ],
            [
                'maGiayTo' => 76,
                'tenGiayTo' => '- Tờ khai tham gia bảo hiểm y tế, Danh sách đề nghị cấp thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi của Ủy ban nhân dân cấp xã (theo mẫu quy định)',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',
            ],
            // ===========Gia hạn giấy phép xây dựng đối với công trình cấp III, cấp IV (công trình Không theo tuyến/Theo tuyến trong đô thị/Tín ngưỡng, tôn giáo/Tượng đài, tranh hoành tráng/Sửa chữa, cải tạo/Theo giai đoạn cho công trình không theo tuyến/Theo giai đoạn cho công trình theo tuyến trong đô thị/Dự án) và nhà ở riêng lẻ
            // Bao gồm
            [
                'maGiayTo' => 77,
                'tenGiayTo' => 'Đơn đề nghị gia hạn giấy phép xây dựng theo Mẫu số 2 Phụ lục số II Nghị định số 175/2024/NĐ-CP ngày 30/12/2024 của Chính phủ',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 78,
                'tenGiayTo' => 'Bản chính giấy phép xây dựng đã được cấp theo quy định',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // ==============Thủ tục cấp Giấy xác nhận tình trạng hôn nhân
            // Bao gồm
            [
                'maGiayTo' => 79,
                'tenGiayTo' => '- Tờ khai cấp Giấy xác nhận tình trạng hôn nhân (nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tiếp);',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Bắt buộc',
            ],

            [
                'maGiayTo' => 80,
                'tenGiayTo' => '- Mẫu điện tử tương tác cấp Giấy xác nhận tình trạng hôn nhân (do người yêu cầu cung cấp thông tin theo hướng dẫn trên Cổng dịch vụ công nếu người có yêu cầu lựa chọn nộp hồ sơ theo hình thức trực tuyến)',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 81,
                'tenGiayTo' => '- Người có yêu cầu cấp Giấy xác nhận tình trạng hôn nhân thực hiện việc nộp/xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến) các giấy tờ sau:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // Giấy tờ phải nộp
            [
                'maGiayTo' => 82,
                'tenGiayTo' => '- Trường hợp người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân đã có vợ hoặc chồng nhưng đã ly hôn hoặc người vợ/chồng đã chết thì phải xuất trình (bản chính) hoặc nộp bản sao giấy tờ hợp lệ để chứng minh;',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 83,
                'tenGiayTo' => '- Công dân Việt Nam đã ly hôn, hủy việc kết hôn ở nước ngoài thì phải nộp bản sao Trích lục ghi chú ly hôn.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],

            [
                'maGiayTo' => 84,
                'tenGiayTo' => '- Trường hợp cá nhân yêu cầu cấp lại Giấy xác nhận tình trạng hôn nhân để sử dụng vào mục đích khác hoặc do Giấy xác nhận tình trạng hôn nhân đã hết thời hạn sử dụng theo quy định thì phải nộp lại Giấy xác nhận tình trạng hôn nhân đã được cấp trước đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 85,
                'tenGiayTo' => '- Văn bản ủy quyền theo quy định của pháp luật trong trường hợp ủy quyền thực hiện việc cấp Giấy xác nhận tình trạng hôn nhân. Trường hợp người được ủy quyền là ông, bà, cha, mẹ, con, vợ, chồng, anh, chị, em ruột của người ủy quyền thì văn bản ủy quyền không phải chứng thực.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // Giấy tờ phải xuất trình
            [
                'maGiayTo' => 86,
                'tenGiayTo' => '- Hộ chiếu/Chứng minh nhân dân/Thẻ căn cước công dân/Thẻ căn cước/Căn cước điện tử/Giấy chứng nhận căn cước hoặc các giấy tờ khác có dán ảnh và thông tin cá nhân do cơ quan có thẩm quyền cấp, còn giá trị sử dụng để chứng minh về nhân thân của người có yêu cầu cấp Giấy xác nhận tình trạng hôn nhân. Trường hợp các thông tin cá nhân trong các giấy tờ này đã có trong CSDLQGVDC, CSDLHTĐT, được hệ thống điền tự động thì không phải tải lên (theo hình thức trực tuyến);	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 87,
                'tenGiayTo' => '- Giấy tờ có giá trị chứng minh thông tin về cư trú trong trường hợp cơ quan đăng ký hộ tịch không thể khai thác được thông tin về nơi cư trú của công dân theo các phương thức quy định tại khoản 2 Điều 14 Nghị định số 104/2022/NĐ-CP ngày 21/12/2022 của Chính phủ. Trường hợp các thông tin về giấy tờ chứng minh nơi cư trú đã được khai thác từ Cơ sở dữ liệu quốc gia về dân cư bằng các phương thức này thì người có yêu cầu không phải xuất trình (theo hình thức trực tiếp) hoặc tải lên (theo hình thức trực tuyến).',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 88,
                'tenGiayTo' => 'Trường hợp gửi hồ sơ qua hệ thống bưu chính thì phải gửi kèm theo bản sao có chứng thực các giấy tờ phải xuất trình nêu trên.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // Lưu ý
            [
                'maGiayTo' => 89,
                'tenGiayTo' => '- Cá nhân có quyền lựa chọn thực hiện thủ tục hành chính về hộ tịch tại cơ quan đăng ký hộ tịch nơi cư trú; nơi cư trú của cá nhân được xác định theo quy định của pháp luật về cư trú. Trường hợp cá nhân lựa chọn thực hiện thủ tục hành chính về hộ tịch không phải tại Ủy ban nhân dân cấp xã nơi thường trú hoặc nơi tạm trú thì Ủy ban nhân dân cấp xã nơi tiếp nhận yêu cầu có trách nhiệm hỗ trợ người dân nộp hồ sơ đăng ký hộ tịch trực tuyến đến đúng cơ quan có thẩm quyền theo quy định.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 90,
                'tenGiayTo' => '- Trường hợp người yêu cầu xác nhận tình trạng hôn nhân đã từng đăng ký thường trú tại nhiều nơi khác nhau, người yêu cầu cung cấp các giấy tờ chứng minh tình trạng hôn nhân tại nơi thường trú trước đây (nếu có). Trên cơ sở các thông tin được cung cấp, cơ quan đăng ký hộ tịch tra cứu thông tin về tình trạng hôn nhân của người yêu cầu xác nhận tình trạng hôn nhân trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, CSDLQGVDC. Trường hợp không tra cứu được tình trạng hôn nhân do chưa có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, CSDLQGVDC, thì cơ quan đăng ký hộ tịch đề nghị Ủy ban nhân dân cấp xã nơi người yêu cầu thường trú/nơi đã đăng ký kết hôn xác minh, cung cấp thông tin. Trong thời hạn 03 ngày làm việc kể từ ngày nhận được yêu cầu xác minh, Ủy ban nhân dân cấp xã nơi nhận được đề nghị xác minh có trách nhiệm kiểm tra, xác minh và gửi kết quả về tình trạng hôn nhân của người đó.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 91,
                'tenGiayTo' => '- Trường hợp yêu cầu cấp Giấy xác nhận tình trạng hôn nhân để sử dụng vào mục đích kết hôn thì cơ quan đăng ký hộ tịch chỉ cấp 01 bản cho người yêu cầu.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 92,
                'tenGiayTo' => '- Trường hợp yêu cầu cấp Giấy xác nhận tình trạng hôn nhân để sử dụng vào mục đích khác, không phải để đăng ký kết hôn thì trong Giấy xác nhận tình trạng hôn nhân phải ghi rõ mục đích sử dụng, số lượng Giấy xác nhận tình trạng hôn nhân được cấp theo yêu cầu.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 93,
                'tenGiayTo' => '- Trường hợp yêu cầu cấp Giấy xác nhận tình trạng hôn nhân để kết hôn với người cùng giới tính hoặc kết hôn với người nước ngoài tại Cơ quan đại diện nước ngoài tại Việt Nam thì cơ quan đăng ký hộ tịch từ chối giải quyết.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 94,
                'tenGiayTo' => '- Đối với giấy tờ nộp, xuất trình nếu người yêu cầu nộp hồ sơ theo hình thức trực tiếp:	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 95,
                'tenGiayTo' => '+ Người yêu cầu đăng ký hộ tịch có thể nộp bản sao được chứng thực từ bản chính hoặc bản sao được cấp từ sổ gốc (sau đây gọi là bản sao) hoặc bản chụp kèm theo bản chính, bản điện tử các giấy tờ này, bao gồm cả giấy tờ được tích hợp, hiển thị trên Ứng dụng định danh điện tử (VneID).',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 96,
                'tenGiayTo' => 'Trường hợp người yêu cầu nộp bản chụp kèm theo bản chính giấy tờ thì người tiếp nhận có trách nhiệm kiểm tra, đối chiếu bản chụp với bản chính và ký xác nhận, không được yêu cầu nộp bản sao giấy tờ đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 97,
                'tenGiayTo' => '+ Đối với giấy tờ xuất trình khi đăng ký hộ tịch, người tiếp nhận có trách nhiệm kiểm tra, đối chiếu với thông tin trong tờ khai, chụp lại hoặc ghi lại thông tin để lưu trong hồ sơ và trả lại cho người xuất trình, không được yêu cầu nộp bản sao hoặc bản chụp giấy tờ đó.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 98,
                'tenGiayTo' => '+ Người tiếp nhận có trách nhiệm tiếp nhận đúng, đủ hồ sơ đăng ký hộ tịch theo quy định của pháp luật hộ tịch, không được yêu cầu người đăng ký hộ tịch nộp thêm giấy tờ mà pháp luật hộ tịch không quy định phải nộp .',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 99,
                'tenGiayTo' => '+ Người tiếp nhận hồ sơ thực hiện khai thác thông tin trong CSDLQGVDC theo quy định pháp luật nếu người yêu cầu đăng ký hộ tịch đã cung cấp họ, chữ đệm, tên; ngày, tháng, năm sinh; số định danh cá nhân/thẻ căn cước công dân/thẻ căn cước/căn cước điện tử. Trường hợp các thông tin cần khai thác không có trong CSDLQGVDC thì đề nghị người yêu cầu kê khai đầy đủ.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 100,
                'tenGiayTo' => '+ Trường hợp người yêu cầu đăng ký hộ tịch cung cấp thông tin về giấy tờ hộ tịch của cá nhân đã được đăng ký, cơ quan đăng ký hộ tịch có trách nhiệm tra cứu thông tin trên Hệ thống thông tin giải quyết thủ tục hành chính cấp tỉnh thông qua kết nối với Cơ sở dữ liệu hộ tịch điện tử, CSDLQGVDC. Trường hợp không tra cứu được do không có thông tin trong Cơ sở dữ liệu hộ tịch điện tử, CSDLQGVDC thì cơ quan đăng ký hộ tịch yêu cầu người đi đăng ký hộ tịch nộp/xuất trình giấy tờ liên quan để chứng minh.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 101,
                'tenGiayTo' => '- Đối với giấy tờ gửi kèm theo nếu người yêu cầu nộp hồ sơ theo hình thức trực tuyến:',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 102,
                'tenGiayTo' => '+ Bản chụp các giấy tờ gửi kèm theo hồ sơ cấp Giấy xác nhận tình trạng hôn nhân trực tuyến phải bảo đảm rõ nét, đầy đủ, toàn vẹn về nội dung, là bản chụp bằng máy ảnh, điện thoại hoặc được chụp, được quét bằng thiết bị điện tử, từ giấy tờ được cấp hợp lệ, còn giá trị sử dụng.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 103,
                'tenGiayTo' => '+ Trường hợp giấy tờ, tài liệu phải gửi kèm trong hồ sơ cấp Giấy xác nhận tình trạng hôn nhân trực tuyến đã có bản sao điện tử hoặc đã có bản điện tử giấy tờ hộ tịch thì người yêu cầu được sử dụng bản điện tử này.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 104,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng k‎ý hộ tịch lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch phải xuất trình giấy tờ tuỳ thân, nộp các giấy tờ là thành phần hồ sơ theo quy định; Trường hợp người yêu cầu đăng ký hộ tịch không lựa chọn nhận kết quả tại Trung tâm Phục vụ hành chính công thì người yêu cầu đăng ký hộ tịch nộp các giấy tờ là thành phần hồ sơ theo quy định trước khi nhận kết quả.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 105,
                'tenGiayTo' => '- Trường hợp người yêu cầu đăng ký hộ tịch không cung cấp được giấy tờ theo quy định hoặc giấy tờ nộp, xuất trình bị tẩy xóa, sửa chữa, làm giả thì cơ quan đăng ký hộ tịch có thẩm quyền hủy bỏ kết quả đăng ký hộ tịch.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 106,
                'tenGiayTo' => '- Giấy tờ do cơ quan có thẩm quyền của nước ngoài cấp, công chứng hoặc xác nhận để sử dụng cho việc đăng ký hộ tịch tại Việt Nam phải được hợp pháp hóa lãnh sự theo quy định của pháp luật, trừ trường hợp được miễn theo điều ước quốc tế mà Việt Nam là thành viên.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 107,
                'tenGiayTo' => '- Trường hợp cho phép người yêu cầu đăng ký hộ tịch lập văn bản cam đoan về nội dung yêu cầu đăng ký hộ tịch thì cơ quan đăng ký hộ tịch phải giải thích rõ cho người lập văn bản cam đoan về trách nhiệm, hệ quả pháp lý của việc cam đoan không đúng sự thật.',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            [
                'maGiayTo' => 108,
                'tenGiayTo' => 'Cơ quan đăng ký hộ tịch từ chối giải quyết hoặc đề nghị cơ quan có thẩm quyền hủy bỏ kết quả đăng ký hộ tịch, nếu có cơ sở xác định nội dung cam đoan không đúng sự thật.	',
                'loaiGiayTo' => 'Tờ khai',
                'yeuCau' => 'Không bắt buộc',
            ],
            // [
            //     'maGiayTo'=>109,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
            // [
            //     'maGiayTo'=>110,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
            // [
            //     'maGiayTo'=>111,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
            // [
            //     'maGiayTo'=>112,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
            // [
            //     'maGiayTo'=>113,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
            // [
            //     'maGiayTo'=>114,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
            // [
            //     'maGiayTo'=>115,
            //     'tenGiayTo'=>'',
            //     'loaiGiayTo'=>'Tờ khai'
            // ],
        ]);
    }
}
