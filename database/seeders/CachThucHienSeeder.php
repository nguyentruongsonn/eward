<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CachThucHienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cachthuchien')->insert([
            [

                'maTTHC' => '1',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi' => 'Lệ phí : 8.000 đồng/bản sao Trích lục/sự kiện hộ tịch đã đăng ký Miễn lệ phí cho người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'thoiHan' => 1,
                'moTa' => 'Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia ',
            ],
            [

                'maTTHC' => '1',
                'kenh' => 'Trực tuyến',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi' => 'Lệ phí : 8.000 đồng/bản sao Trích lục/sự kiện hộ tịch đã đăng ký Miễn lệ phí cho người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'thoiHan' => 1,
                'moTa' => '- Thủ tục này đủ điều kiện thực hiện dịch vụ công trực tuyến toàn trình. - Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia ',
            ],
            [

                'maTTHC' => '1',
                'kenh' => 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi' => 'Lệ phí : 8.000 đồng/bản sao Trích lục/sự kiện hộ tịch đã đăng ký Miễn lệ phí cho người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'thoiHan' => 1,
                'moTa' => 'Người yêu cầu cấp bản sao Trích lục hộ tịch, bản sao Giấy khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia  ',
            ],

            [

                'maTTHC' => '2',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.Trường hợp cần xác minh điều kiện kết hôn của hai bên nam, nữ thì thời hạn giải quyết không quá 05 ngày làm việc.',
                'moTaPhiLePhi' => 'Lệ phí : Miễn lệ phí. Phí cấp bản sao Trích lục kết hôn (nếu có yêu cầu) thực hiện theo quy định tại Thông tư số 281/2016/TT-BTC ngày 14/11/2016 của Bộ Tài chính.',
                'thoiHan' => 3,
                'moTa' => 'Người có yêu cầu đăng ký kết hôn thực hiện nộp hồ sơ trực tiếp tại Bộ phận một cửa của UBND cấp xã hoặc nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn) hoặc Cổng dịch vụ công cấp tỉnh (https://dichvucong.---.gov.vn) (bên nam hoặc bên nữ có thể nộp hồ sơ mà không cần có văn bản ủy quyền của bên còn lại).',
            ],
            [

                'maTTHC' => '2',
                'kenh' => 'Trực tuyến',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo. Trường hợp cần xác minh điều kiện kết hôn của hai bên nam, nữ thì thời hạn giải quyết không quá 05 ngày làm việc.',
                'moTaPhiLePhi' => 'Lệ phí : Đồng Miễn lệ phí. Phí cấp bản sao Trích lục kết hôn (nếu có yêu cầu) thực hiện theo quy định tại Thông tư số 281/2016/TT-BTC ngày 14/11/2016 của Bộ Tài chính.',
                'thoiHan' => 3,
                'moTa' => 'Người có yêu cầu đăng ký kết hôn thực hiện nộp hồ sơ trực tiếp tại Bộ phận một cửa của UBND cấp xã hoặc nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn) hoặc Cổng dịch vụ công cấp tỉnh (https://dichvucong.---.gov.vn) (bên nam hoặc bên nữ có thể nộp hồ sơ mà không cần có văn bản ủy quyền của bên còn lại).',
            ],
            [

                'maTTHC' => '2',
                'kenh' => 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận hồ sơ; trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo. Trường hợp cần xác minh điều kiện kết hôn của hai bên nam, nữ thì thời hạn giải quyết không quá 05 ngày làm việc.',
                'moTaPhiLePhi' => 'Lệ phí : Đồng (Miễn lệ phí. Phí cấp bản sao Trích lục kết hôn (nếu có yêu cầu) thực hiện theo quy định tại Thông tư số 281/2016/TT-BTC ngày 14/11/2016 của Bộ Tài chính.)  ',
                'thoiHan' => 3,
                'moTa' => 'Người có yêu cầu đăng ký kết hôn thực hiện nộp hồ sơ trực tiếp tại Bộ phận một cửa của UBND cấp xã hoặc nộp hồ sơ trực tuyến trên Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn) hoặc Cổng dịch vụ công cấp tỉnh (https://dichvucong.---.gov.vn) (bên nam hoặc bên nữ có thể nộp hồ sơ mà không cần có văn bản ủy quyền của bên còn lại).',
            ],
            [

                'maTTHC' => '3',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận yêu cầu, trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi' => 'Lệ phí: - Đối với trường hợp đăng ký khai sinh không đúng hạn: theo mức thu lệ phí do Hội đồng nhân dân cấp tỉnh quy định. - Miễn lệ phí đối với trường hợp khai sinh đúng hạn, người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'thoiHan' => 2,
                'moTa' => 'Người yêu cầu đăng ký khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn).',
            ],
            [

                'maTTHC' => '3',
                'kenh' => 'Trực tuyến',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận yêu cầu, trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi' => 'Lệ phí: - Đối với trường hợp đăng ký khai sinh không đúng hạn: theo mức thu lệ phí do Hội đồng nhân dân cấp tỉnh quy định. - Miễn lệ phí đối với trường hợp khai sinh đúng hạn, người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'thoiHan' => 1,
                'moTa' => 'Thủ tục này đủ điều kiện thực hiện dịch vụ công trực tuyến toàn trình.
Người yêu cầu đăng ký khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn).',
            ],
            [

                'maTTHC' => '3',
                'kenh' => 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet' => 'Ngay trong ngày tiếp nhận yêu cầu, trường hợp nhận hồ sơ sau 15 giờ mà không giải quyết được ngay thì trả kết quả trong ngày làm việc tiếp theo.',
                'moTaPhiLePhi' => 'Lệ phí: - Đối với trường hợp đăng ký khai sinh không đúng hạn: theo mức thu lệ phí do Hội đồng nhân dân cấp tỉnh quy định. - Miễn lệ phí đối với trường hợp khai sinh đúng hạn, người thuộc gia đình có công với cách mạng; người thuộc hộ nghèo; người khuyết tật.',
                'thoiHan' => 2,
                'moTa' => 'Người yêu cầu đăng ký khai sinh trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn).',
            ],
            [

                'maTTHC' => '4',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => 'Thời hạn thực hiện yêu cầu chứng thực là ngay trong ngày cơ quan, tổ chức tiếp nhận yêu cầu hoặc trong ngày làm việc tiếp theo nếu tiếp nhận yêu cầu sau 15 giờ. Trường hợp trả kết quả trong ngày làm việc tiếp theo hoặc phải kéo dài thời gian theo thỏa thuận thì người tiếp nhận hồ sơ phải có phiếu hẹn ghi rõ thời gian (giờ, ngày) trả kết quả cho người yêu cầu chứng thực.',
                'moTaPhiLePhi' => 'Phí: Đồng  ',
                'thoiHan' => 1,
                'moTa' => 'Nộp hồ sơ trực tiếp tại Ủy ban nhân dân cấp xã, Tổ chức hành nghề công chứng, Cơ quan đại diện hoặc ngoài trụ sở của cơ quan thực hiện chứng thực nếu người yêu cầu chứng thực thuộc diện già yếu, không thể đi lại được, đang bị tạm giữ, tạm giam, thi hành án phạt tù hoặc có lý do chính đáng khác.',
            ],
            [

                'maTTHC' => '5',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => 'Trong ngày cơ quan, tổ chức tiếp nhận yêu cầu hoặc trong ngày làm việc tiếp theo, nếu tiếp nhận yêu cầu sau 15 giờ. Đối với trường hợp cùng một lúc yêu cầu chứng thực bản sao từ nhiều loại bản chính giấy tờ, văn bản; bản chính có nhiều trang; yêu cầu số lượng nhiều bản sao; nội dung giấy tờ, văn bản phức tạp khó kiểm tra, đối chiếu mà cơ quan, tổ chức thực hiện chứng thực không thể đáp ứng được thời hạn quy định nêu trên thì thời hạn chứng thực được kéo dài thêm không quá 02 (hai) ngày làm việc hoặc có thể dài hơn theo thỏa thuận bằng văn bản với người yêu cầu chứng thực.',
                'moTaPhiLePhi' => 'Phí: Đồng',
                'thoiHan' => 1,
                'moTa' => '',
            ],
            [

                'maTTHC' => '6',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => '- Thời hạn thực hiện liên thông các thủ tục hành chính đăng ký khai sinh, cấp thẻ bảo hiểm y tế cho trẻ em dưới 6 tuổi tối đa không quá 15 ngày làm việc, kể từ ngày nộp đủ hồ sơ theo quy định. - Trường hợp hồ sơ, thông tin chưa đầy đủ hoặc chưa đúng quy định mà Bộ phận tiếp nhận và trả kết quả của Ủy ban nhân dân cấp xã phải hoàn thiện hồ sơ, bổ sung thông tin theo yêu cầu của cơ quan Bảo hiểm xã hội thì thời hạn giải quyết được kéo dài thêm không quá 02 ngày làm việc. - Đối với các xã cách xa trụ sở cơ quan Bảo hiểm xã hội cấp huyện từ 50 km trở lên, giao thông đi lại khó khăn, chưa được kết nối Internet thì thời hạn trả kết quả được kéo dài thêm nhưng không quá 05 ngày làm việc. - Căn cứ vào tình hình thực tế, các địa phương có thể quy định cụ thể thời hạn thực hiện liên thông các thủ tục hành chính ngắn hơn thời hạn tối đa nêu trên.',
                'moTaPhiLePhi' => '',
                'thoiHan' => 2,
                'moTa' => 'Người có yêu cầu trực tiếp hoặc ủy quyền cho người khác thực hiện tại Bộ phận tiếp nhận và trả kết quả tại Ủy ban nhân dân cấp xã.',
            ],
            [

                'maTTHC' => '7',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => '05 Ngày làm việc',
                'moTaPhiLePhi' => '',
                'thoiHan' => 5,
                'moTa' => '',
            ],
            [

                'maTTHC' => '7',
                'kenh' => 'Trực tuyến',
                'thoiHanGiaiQuyet' => '05 Ngày làm việc',
                'moTaPhiLePhi' => '',
                'thoiHan' => 5,
                'moTa' => '',
            ],
            [

                'maTTHC' => '7',
                'kenh' => 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet' => '05 Ngày làm việc',
                'moTaPhiLePhi' => '',
                'thoiHan' => 5,
                'moTa' => '',
            ],
            [

                'maTTHC' => '8',
                'kenh' => 'Trực tiếp',
                'thoiHanGiaiQuyet' => '03 ngày làm việc; trường hợp phải xác minh thì thời hạn giải quyết không quá 23 ngày. ',
                'moTaPhiLePhi' => 'Lệ phí:',
                'thoiHan' => 5,
                'moTa' => 'Người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia',
            ],
            [

                'maTTHC' => '8',
                'kenh' => 'Trực tuyến',
                'thoiHanGiaiQuyet' => '03 ngày làm việc; trường hợp phải xác minh thì thời hạn giải quyết không quá 23 ngày.',
                'moTaPhiLePhi' => '',
                'thoiHan' => 15,
                'moTa' => '- Thủ tục này đủ điều kiện thực hiện dịch vụ công trực tuyến toàn trình.- Người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn).',
            ],
            [

                'maTTHC' => '8',
                'kenh' => 'Dịch vụ bưu chính',
                'thoiHanGiaiQuyet' => '03 ngày làm việc; trường hợp phải xác minh thì thời hạn giải quyết không quá 23 ngày.',
                'moTaPhiLePhi' => '',
                'thoiHan' => 15,
                'moTa' => 'Người yêu cầu cấp Giấy xác nhận tình trạng hôn nhân trực tiếp thực hiện hoặc ủy quyền cho người khác thực hiện nộp hồ sơ trực tiếp tại Trung tâm Phục vụ hành chính công, gửi qua hệ thống bưu chính hoặc nộp hồ sơ trực tuyến tại Cổng dịch vụ công quốc gia (https://dichvucong.gov.vn).',
            ],
            //             [

            //                 'maTTHC'=>'8',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'',
            //                 'moTaPhiLePhi'=>'',
            //                 'thoiHan'=>15,
            //                 'moTa'=>'',
            //             ],
            //             [

            //                 'maTTHC'=>'8',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'',
            //                 'moTaPhiLePhi'=>'',
            //                 'thoiHan'=>15,
            //                 'moTa'=>'',
            //             ],
            //             [

            //                 'maTTHC'=>'8',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'',
            //                 'moTaPhiLePhi'=>'',
            //                 'thoiHan'=>15,
            //                 'moTa'=>'',
            //             ],
            //             [

            //                 'maTTHC'=>'8',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'',
            //                 'moTaPhiLePhi'=>'',
            //                 'thoiHan'=>15,
            //                 'moTa'=>'',
            //             ],
            //             [

            //                 'maTTHC'=>'8',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'',
            //                 'moTaPhiLePhi'=>'',
            //                 'thoiHan'=>15,
            //                 'moTa'=>'',
            //             ],
            //             [

            //                 'maTTHC'=>'8',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'',
            //                 'moTaPhiLePhi'=>'',
            //                 'thoiHan'=>15,
            //                 'moTa'=>'',
            //             ],

            //             [
            //                 'maTTHC'=>'7',
            //                 'kenh'=> 'Dịch vu bưu chính',
            //                 'thoiHanGiaiQuyet'=>'10 Ngày làm việc	',
            //                 'moTaPhiLePhi'=>'Phí : 100.000 Đồng Phí cung cấp thông tin lý lịch tư pháp của sinh viên, người có công với cách mạng, thân nhân liệt sỹ (gồm cha đẻ, mẹ đẻ, vợ (hoặc chồng), con (con đẻ, con nuôi), người có công nuôi dưỡng liệt sỹ): 100.000 đồng/lần/người. Các trường hợp miễn phí cung cấp thông tin lý lịch tư pháp gồm: Trẻ em theo quy định tại Luật bảo vệ, chăm sóc và giáo dục trẻ em; Người cao tuổi theo quy định tại Luật người cao tuổi; Người khuyết tật theo quy định tại Luật người khuyết tật; Người thuộc hộ nghèo và Người cư trú tại các xã đặc biệt khó khăn, đồng bào dân tộc thiểu số ở các xã có điều kiện kinh tế - xã hội đặc biệt khó khăn, xã biên giới, xã an toàn khu theo quy định của pháp luật.
            // Phí : 200.000 Đồng Phí cung cấp thông tin lý lịch tư pháp: 200.000 đồng/lần/người.',
            //                 'thoiHan'=>10,
            //                 'moTa'=>'Trong thời hạn 10 ngày, kể từ ngày nhận được yêu cầu hợp lệ.',
            //             ],

            //             [
            //                 'maTTHC'=>'7',
            //                 'kenh'=> 'Trực tuyến',
            //                 'thoiHanGiaiQuyet'=>'10 Ngày làm việc',
            //                 'moTaPhiLePhi'=>'Phí : 100.000 Đồng Phí cung cấp thông tin lý lịch tư pháp của sinh viên, người có công với cách mạng, thân nhân liệt sỹ (gồm cha đẻ, mẹ đẻ, vợ (hoặc chồng), con (con đẻ, con nuôi), người có công nuôi dưỡng liệt sỹ): 100.000 đồng/lần/người. Các trường hợp miễn phí cung cấp thông tin lý lịch tư pháp gồm: Trẻ em theo quy định tại Luật bảo vệ, chăm sóc và giáo dục trẻ em; Người cao tuổi theo quy định tại Luật người cao tuổi; Người khuyết tật theo quy định tại Luật người khuyết tật; Người thuộc hộ nghèo và Người cư trú tại các xã đặc biệt khó khăn, đồng bào dân tộc thiểu số ở các xã có điều kiện kinh tế - xã hội đặc biệt khó khăn, xã biên giới, xã an toàn khu theo quy định của pháp luật.
            // Phí : 200.000 Đồng Phí cung cấp thông tin lý lịch tư pháp: 200.000 đồng/lần/người.',
            //                 'thoiHan'=>10,
            //                 'moTa'=>'Trường hợp người được yêu cầu cấp Phiếu lý lịch tư pháp là công dân Việt Nam đã cư trú ở nhiều nơi hoặc có thời gian cư trú ở nước ngoài, người nước ngoài, trường hợp phải xác minh về điều kiện đương nhiên được xóa án tích thì thời hạn không quá 15 ngày.',
            //             ],

            // //Xác nhận thông tin về cư trú
            //             [

            //                 'maTTHC'=>'6',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'05 Ngày làm việc	',
            //                 'moTaPhiLePhi'=>'Phí : 0 Đồng',
            //                 'thoiHan'=>5,
            //                 'moTa'=>'- Nộp hồ sơ trực tiếp tại Công an cấp xã nơi thuận lợi, phù hợp không phụ thuộc vào nơi cư trú của công dân. Thời gian tiếp nhận hồ sơ: Giờ hành chính các ngày làm việc từ thứ 2 đến thứ 6 và sáng thứ 7 hàng tuần (trừ các ngày nghỉ lễ, tết theo quy định của pháp luật). Kể từ ngày nhận được hồ sơ hợp lệ, cơ quan đăng ký cư trú có trách nhiệm cấp xác nhận thông tin về cư trú cho công dân trong thời hạn 01 ngày làm việc với trường hợp thông tin có trong Cơ sở dữ liệu quốc gia về dân cư.',
            //             ],

            //             [

            //                 'maTTHC'=>'6',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'03 Ngày làm việc',
            //                 'moTaPhiLePhi'=>'Phí : 0 Đồng',
            //                 'thoiHan'=>3,
            //                 'moTa'=>'Kể từ ngày nhận được hồ sơ hợp lệ, cơ quan đăng ký cư trú có trách nhiệm cấp xác nhận thông tin về cư trú cho công dân trong thời hạn 03 ngày làm việc với trường hợp cần xác minh; trường hợp từ chối giải quyết thì phải trả lời bằng văn bản và nêu rõ lý do',
            //             ],

            //             [
            //                 'maTTHC'=>'6',
            //                 'kenh'=> 'Trực tuyến',
            //                 'thoiHanGiaiQuyet'=>'05 Ngày làm việc',
            //                 'moTaPhiLePhi'=>'Phí : 0 Đồng',
            //                 'thoiHan'=>5,
            //                 'moTa'=>'- Nộp hồ sơ trực tuyến qua các cổng cung cấp dịch vụ công trực tuyến như: Trực tuyến qua Cổng dịch vụ công quốc gia, Cổng dịch vụ công Bộ Công an, ứng dụng VNeID hoặc dịch vụ công trực tuyến khác theo quy định của pháp luật. Thời gian tiếp nhận hồ sơ: Giờ hành chính các ngày làm việc từ thứ 2 đến thứ 6 và sáng thứ 7 hàng tuần (trừ các ngày nghỉ lễ, tết theo quy định của pháp luật). Kể từ ngày nhận được hồ sơ hợp lệ, cơ quan đăng ký cư trú có trách nhiệm cấp xác nhận thông tin về cư trú cho công dân trong thời hạn 01 ngày làm việc với trường hợp thông tin có trong Cơ sở dữ liệu quốc gia về dân cư.',
            //             ],

            //             [

            //                 'maTTHC'=>'6',
            //                 'kenh'=> 'Trực tuyến',
            //                 'thoiHanGiaiQuyet'=>'03 Ngày làm việc',
            //                 'moTaPhiLePhi'=>'Phí : 0 Đồng',
            //                 'thoiHan'=>3,
            //                 'moTa'=>'Kể từ ngày nhận được hồ sơ hợp lệ, cơ quan đăng ký cư trú có trách nhiệm cấp xác nhận thông tin về cư trú cho công dân trong thời hạn 03 ngày làm việc với trường hợp cần xác minh; trường hợp từ chối giải quyết xác nhận thông tin về cư trú thì phải trả lời bằng văn bản và nêu rõ lý do.',
            //             ],
            // //Chứng thực
            //                         [

            //                 'maTTHC'=>'5',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'Trong ngày cơ quan, tổ chức tiếp nhận yêu cầu hoặc trong ngày làm việc tiếp theo, nếu tiếp nhận yêu cầu sau 15 giờ. Đối với trường hợp cùng một lúc yêu cầu chứng thực bản sao từ nhiều loại bản chính giấy tờ, văn bản; bản chính có nhiều trang; yêu cầu số lượng nhiều bản sao; nội dung giấy tờ, văn bản phức tạp khó kiểm tra, đối chiếu mà cơ quan, tổ chức thực hiện chứng thực không thể đáp ứng được thời hạn quy định nêu trên thì thời hạn chứng thực được kéo dài thêm không quá 02 (hai) ngày làm việc hoặc có thể dài hơn theo thỏa thuận bằng văn bản với người yêu cầu chứng thực.',
            //                 'thoiHan'=>1,
            //                 'moTaPhiLePhi'=>'Phí : Đồng Tại UBND cấp xã, Phòng Tư pháp, Tổ chức hành nghề công chứng: 2.000 đồng/trang; từ trang thứ 3 trở lên thu 1.000 đồng/trang, tối đa thu không quá 200.000 đồng/bản. Trang là căn cứ để thu phí được tính theo trang của bản chính. Phí : Đồng Tại cơ quan đại diện: 10 USD/bản;',
            //                 'moTa'=>'',
            //             ],

            //             //Thủ tục chứng thực chữ ký
            //             [

            //                 'maTTHC'=>'4',
            //                 'kenh'=> 'Trực tiếp',
            //                 'thoiHanGiaiQuyet'=>'Thời hạn thực hiện yêu cầu chứng thực là ngay trong ngày cơ quan, tổ chức tiếp nhận yêu cầu hoặc trong ngày làm việc tiếp theo nếu tiếp nhận yêu cầu sau 15 giờ. Trường hợp trả kết quả trong ngày làm việc tiếp theo hoặc phải kéo dài thời gian theo thỏa thuận thì người tiếp nhận hồ sơ phải có phiếu hẹn ghi rõ thời gian (giờ, ngày) trả kết quả cho người yêu cầu chứng thực.c',
            //                 'moTaPhiLePhi'=>'Phí : Đồng ',
            //                 'thoiHan'=>1,
            //                 'moTa'=>'Nộp hồ sơ trực tiếp tại Ủy ban nhân dân cấp xã, Tổ chức hành nghề công chứng, Cơ quan đại diện hoặc ngoài trụ sở của cơ quan thực hiện chứng thực nếu người yêu cầu chứng thực thuộc diện già yếu, không thể đi lại được, đang bị tạm giữ, tạm giam, thi hành án phạt tù hoặc có lý do chính đáng khác.',
            //             ],

        ]);
    }
}
