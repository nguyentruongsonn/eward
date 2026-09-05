<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormTrucTuyenSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('formtructuyen')->insert([

            // ========================================Cấp bản sao trích lục hộ tịch, bản sao giấy khai sinh (ĐÃ SỬA LỖI)
            [
                'maTTHC' => 1,
                'cauHinhForm' => json_encode([
                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [
                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,
                                        'required' => true],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,
                                        'required' => true],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,
                                        'required' => true],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ],
                            ],
                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Chứng minh nhân dân', 'Căn cước công dân', 'Hộ chiếu', 'Thẻ căn cước',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to', 'type' => 'text', 'col' => 3,
                                        'required' => true],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 3,
                                        'required' => true],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],
                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3],
                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên cơ quan / hộ kinh doanh', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 5],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],
                                    [
                                        'label' => 'Hình thức nộp hồ sơ',
                                        'name' => 'hinh_thuc_nop',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => ['Trực tiếp', 'Trực tuyến', 'Dịch vụ bưu chính'],
                                    ],
                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 3],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại trích lục bản sao cần cấp',
                                        'name' => 'loai_trich_luc',
                                        'type' => 'select',
                                        'col' => 6,
                                        'required' => true,
                                        'options' => [
                                            '0 - Giấy khai sinh bản sao/ Trích lục ghi vào Sổ hộ tịch việc khai sinh (bản sao)', '1 - Trích lục kết hôn (bản sao)/ Trích lục ghi chú kết hôn (bản sao)', '2 - Trích lục khai tử (bản sao)', '3 - Trích lục đăng ký giám hộ (bản sao)', '4 - Trích lục đăng ký chấm dứt giám hộ (bản sao)', '5 - Trích lục đăng ký nhận cha, me, con (bản sao)', '6 - Trích lục ghi vào Sổ hộ tịch việc nuôi con nuôi (bản sao)',
                                        ],
                                    ],
                                    [
                                        'label' => 'Số lượng bản sao đề nghị cấp',
                                        'name' => 'so_luong_ban_sao',
                                        'type' => 'number',
                                        'col' => 6,
                                        'required' => true,
                                        'min' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 💍 FORM 2: THÔNG TIN NGƯỜI YÊU CẦU
                    // ==================================================
                    [
                        'group' => 'Thông tin người yêu cầu',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Họ',
                                        'name' => 'Họ',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Chữ đệm',
                                        'name' => 'dem_nguoi_yeu_cau',
                                        'required' => true,
                                        'type' => 'text',
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Tên',
                                        'name' => 'ten_nguoi_yeu_cau',
                                        'required' => true,
                                        'type' => 'text',
                                        'col' => 4,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Ngày',
                                        'name' => 'ngay_nguoi_yeu_cau',
                                        'type' => 'number',
                                        'min' => 1,
                                        'max' => 31,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Tháng',
                                        'name' => 'thang_nguoi_yeu_cau',
                                        'type' => 'number',
                                        'min' => 1,
                                        'max' => 12,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Năm',
                                        'name' => 'nam_nguoi_yeu_cau',
                                        'type' => 'number',
                                        'col' => 4,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_nguoi_yeu_cau',
                                        'col' => 4,
                                        'type' => 'select',
                                        'required' => true,
                                        'options' => [
                                            'Thường trú',
                                            'Tạm trú',
                                            'Nơi ở hiện tại',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nguoi_yeu_cau', 'type' => 'text', 'col' => 3],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_tuy_than_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước công dân', 'Giấy chứng minh Quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', 'Giấy chứng minh Công an nhân dân', 'Giấy tờ khác', 'Giấy khai sinh', 'Giấy chứng sinh', 'Thẻ căn cước', 'Giấy chứng nhân căn cước',
                                        ],
                                    ],
                                    [
                                        'label' => 'Số giấy tờ',
                                        'name' => 'so_giay_to_nguoi_yeu_cau',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 3,
                                    ],
                                    [
                                        'label' => 'Ngày cấp',
                                        'name' => 'ngay_cap_nguoi_yeu_cau',
                                        'type' => 'date',
                                        'required' => true,
                                        'col' => 3,
                                    ],
                                    ['label' => 'Nơi cấp giấy tờ', 'name' => 'noi_cap_giay_to_nguoi_yeu_cau', 'type' => 'text', 'col' => 3,
                                        'required' => true],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quan hệ với người đề nghị',
                                        'name' => 'quan_he_voi_nguoi_de_nghi',
                                        'type' => 'text',
                                        'col' => 6,
                                    ],
                                    [
                                        'label' => 'Loại hộ tịch',
                                        'name' => 'loai_ho_tich_nguoi_yeu_cau',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['disabled' => true],
                                        'value' => 'BS',
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN NGƯỜI ĐƯỢC CẤP BẢN SAO
                    // ==================================================
                    [
                        'group' => 'Thông tin người được cấp bản sao Giấy khai sinh, bản sao Trích lục hộ tịch',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Họ',
                                        'name' => 'ho_nguoi_duoc_cap',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Chữ đệm',
                                        'name' => 'dem_nguoi_duoc_cap',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Tên',
                                        'name' => 'ten_nguoi_duoc_cap',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Ngày',
                                        'name' => 'ngay_nguoi_duoc_cap',
                                        'type' => 'number',
                                        'min' => 1,
                                        'max' => 31,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Tháng',
                                        'name' => 'thang_nguoi_duoc_cap',
                                        'type' => 'number',
                                        'min' => 1,
                                        'max' => 12,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Năm',
                                        'name' => 'nam_nguoi_duoc_cap',
                                        'type' => 'number',
                                        'col' => 4,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Giới tính',
                                        'name' => 'gioi_tinh_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Nam', 'Nữ',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [ // FIX: Wrapped column definitions in an array
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_nguoi_duoc_cap',
                                        'col' => 4,
                                        'type' => 'select',
                                        'required' => true,
                                        'options' => [
                                            'Thường trú',
                                            'Tạm trú',
                                            'Nơi ở hiện tại',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nguoi_duoc_cap', 'type' => 'text', 'col' => 3],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_tuy_than_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước công dân', 'Giấy chứng minh Quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', 'Giấy chứng minh Công an nhân dân', 'Giấy tờ khác', 'Giấy khai sinh', 'Giấy chứng sinh', 'Thẻ căn cước', 'Giấy chứng nhân căn cước',
                                        ],
                                    ],
                                    [
                                        'label' => 'Số giấy tờ',
                                        'name' => 'so_giay_to_nguoi_duoc_cap',
                                        'type' => 'text', // FIX: Changed from select to text
                                        'col' => 3,
                                    ],
                                    [
                                        'label' => 'Ngày cấp',
                                        'name' => 'ngay_cap_nguoi_duoc_cap',
                                        'type' => 'date',
                                        'col' => 3,
                                    ],
                                    ['label' => 'Nơi cấp giấy tờ', 'name' => 'noi_cap_giay_to_nguoi_duoc_cap', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                        ],
                    ],
                    // ==================================================
                    // 🤵 FORM 4: NƠI ĐĂNG KÝ HỒ SƠ TRƯỚC ĐÂY
                    // ==================================================
                    [
                        'group' => 'Nơi đăng ký hồ sơ trước đây',
                        'fields' => [
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Nơi đăng ký hồ sơ trước đây', 'name' => 'noi_dang_ky_ho_so_truoc_day', 'type' => 'text', 'col' => 8],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Sổ đăng ký hộ tịch trước đây', 'name' => 'so_dang_ky_ho_tich_truoc_day', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Quyển sổ', 'name' => 'quyen_so', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Ngày đăng ký', 'name' => 'ngay_dang_ky', 'type' => 'date', 'col' => 4],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Thông tin khác', 'name' => 'thong_tin_khac', 'type' => 'text', 'col' => 12],
                                ],
                            ],
                        ],
                    ],
                ]),
            ],

            //  ==========THỦ TỤC ĐĂNG KÝ KẾT HÔN
            [
                'maTTHC' => 2, // Đăng ký kết hôn
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ],
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Chứng minh nhân dân', 'Căn cước công dân', 'Hộ chiếu', 'Thẻ căn cước',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                ],
                            ],

                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên cơ quan / hộ kinh doanh', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 5],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],
                                    [
                                        'label' => 'Hình thức nộp hồ sơ',
                                        'name' => 'hinh_thuc_nop',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => ['Trực tiếp', 'Trực tuyến', 'Dịch vụ bưu chính'],
                                    ],
                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                        ],
                    ],

                    // ==================================================
                    // 💍 FORM 2: THÔNG TIN KẾT HÔN
                    // ==================================================
                    [
                        'group' => 'Thông tin kết hôn',
                        'fields' => [
                            [
                                'label' => 'Loại đăng ký',
                                'name' => 'loai_dang_ky',
                                'type' => 'radio',
                                'required' => true,
                                'options' => [
                                    'Đăng ký mới',
                                    'Đăng ký lại',
                                    'Ghi sổ việc đã kết hôn ở nước ngoài',
                                ],
                            ],
                            [
                                'label' => 'Loại hồ sơ liên thông',
                                'name' => 'loai_ho_so_lien_thong',
                                'required' => true,
                                'type' => 'select',
                                'options' => ['Loại hồ sơ liên thông', 'Không phải'],
                            ],
                            ['label' => 'Số lượng bản sao đề nghị cấp', 'name' => 'so_luong_ban_sao', 'type' => 'number'],
                        ],
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN BÊN NỮ
                    // ==================================================
                    [
                        'group' => 'Thông tin bên nữ',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ và tên bên nữ',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nu', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_nu', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nu', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nu', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nu', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nu', 'type' => 'number', 'col' => 4, 'required' => true, 'min' => 1900, 'max' => date('Y')],
                                ],
                            ],
                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nu',
                                'type' => 'radio',
                                'required' => true,
                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nu', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nu', 'type' => 'date', 'col' => 3],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nu',
                                        'type' => 'text',
                                        'col' => 3,

                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Số lần kết hôn của người vợ',
                                        'name' => 'so_lan_ket_hon_cua_nguoi_vo',
                                        'type' => 'text',
                                        'col' => 3,
                                    ],
                                    [
                                        'label' => 'Số định danh cá nhân',
                                        'name' => 'so_dinh_danh_ca_nhan_vo',
                                        'type' => 'text',
                                        'col' => 3,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại trình trạng hôn nhân',
                                        'name' => 'loai_tinh_trang_hon_nhan_vo',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 8,
                                        'options' => [
                                            'Hiện tại chưa đăng ký kết hôn với ai', 'Hiện tại đang có vợ/chồng', 'Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng đã ly hôn; hiện tại chưa đăng ký kết hôn với ai', 'Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng vợ/chồng đã chết; hiện tại chưa đăng ký kết hôn với ai', 'Từ ngày...tháng...năm... đến ngày...tháng...năm... chưa đăng ký kết hôn với ai; hiện tại đang có vợ chồng', 'Khác - nếu không thuộc trường hợp trên',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Tình trang hôn nhân',
                                        'name' => 'tinh_trang_hon_nhan_vo',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 8,
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN BÊN NAM
                    // ==================================================
                    [
                        'group' => 'Thông tin bên nam',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ và tên bên nam',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nam', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_nam', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nam', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nam', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nam', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nam', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nam',
                                'type' => 'radio',
                                'required' => true,
                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nam', 'type' => 'text', 'col' => 3, 'required' => true],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nam',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nam', 'type' => 'text', 'col' => 3, 'required' => true],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nam', 'type' => 'date', 'col' => 3],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nam',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Số lần kết hôn của người chồng',
                                        'name' => 'so_lan_ket_hon_cua_nguoi_chong',
                                        'type' => 'text',
                                        'col' => 3,
                                    ],
                                    [
                                        'label' => 'Số định danh cá nhân',
                                        'name' => 'so_dinh_danh_ca_nhan_chồng',
                                        'type' => 'text',
                                        'col' => 3,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại trình trạng hôn nhân',
                                        'name' => 'loai_tinh_trang_hon_nhan_chong',
                                        'type' => 'select',
                                        'col' => 8,
                                        'required' => true,
                                        'options' => [
                                            'Hiện tại chưa đăng ký kết hôn với ai', 'Hiện tại đang có vợ/chồng', 'Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng đã ly hôn; hiện tại chưa đăng ký kết hôn với ai', 'Đã đăng ký kết hôn hoặc đã có vợ/chồng nhưng vợ/chồng đã chết; hiện tại chưa đăng ký kết hôn với ai', 'Từ ngày...tháng...năm... đến ngày...tháng...năm... chưa đăng ký kết hôn với ai; hiện tại đang có vợ chồng', 'Khác - nếu không thuộc trường hợp trên',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Tình trang hôn nhân',
                                        'name' => 'tinh_trang_hon_nhan_chong',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 8,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
            ],

            // ===================== THỦ TỤC ĐĂNG KÝ KHAI SINH
            [
                'maTTHC' => 3,
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ],
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Số CMND/CCCD', 'name' => 'so_cmnd_cccd', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 4],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'text',
                                        'col' => 3,

                                    ],
                                ],
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                        ],
                    ],

                    // ==================================================
                    // 💍 FORM 2: THÔNG TIN CHI TIẾT
                    // ==================================================
                    [
                        'group' => 'Thông tin chi tiết',
                        'fields' => [
                            [
                                'label' => 'Nơi đăng ký khai sinh',
                                'name' => 'noi_dang_ky_khai_sinh',
                                'type' => 'text',
                                'required' => true,
                                'col' => 4,
                            ],
                            [
                                'label' => 'Loại đăng ký',
                                'name' => 'loai_dang_ky',
                                'type' => 'select',
                                'options' => ['Đăng ký đúng hạn', 'Đăng ký quá hạn', 'Đăng ký lại', 'Ghi vào sổ việc khai sinh đã đăng ký tại cơ quan có thẩm quyền ở nước ngoài'],
                            ],
                            ['label' => 'Số lượng bản sao đề nghị cấp', 'name' => 'so_luong_ban_sao', 'type' => 'number', 'min' => 1],
                        ],
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN NGƯỜI YÊU CẦU ĐĂNG KÝ KHAI SINH
                    // ==================================================
                    [
                        'group' => 'Thông tin người yêu cầu đăng ký khai sinh',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nguoi_yeu_cau', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Đệm', 'name' => 'dem_nguoi_yeu_cau', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nguoi_yeu_cau', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_nu',
                                        'type' => 'radio',
                                        'col' => 4,
                                        'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                                    ],
                                ],

                            ],

                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nguoi_yeu_cau', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nguoi_yeu_cau', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quan hệ với người được khai sinh',
                                        'name' => 'quan_he_nguoi_duoc_khai_sinh',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Xác thực thông tin ',
                                        'name' => 'xac_thuc_thong_tin',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Thông tin chưa được xác thực', 'Thông tin danh tính cá nhân, cư trú đã được xác thực với CSDLQG về dân cư', 'Thông tin danhh tính cá nhân đã được xác thực với CSDLQG về dân cư',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN NGƯỜI ĐƯỢC ĐĂNG KÝ KHAI SINH
                    // ==================================================
                    [
                        'group' => 'Thông tin về người được đăng ký khai sinh',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nguoi_duoc_dk', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_nguoi_duoc_dk', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nguoi_duoc_dk', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nam_nguoi_duoc_dk', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nam_nguoi_duoc_dk', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nam_nguoi_duoc_dk', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Ngày tháng năm sinh ghi bằng chữ',
                                        'name' => 'ngay_thang_nam_chu_nguoi_duoc_dk',
                                        'type' => 'text',
                                        'col' => 8,
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giói tính',
                                        'name' => 'gioi_tinh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Nam', 'Nữ', 'Chưa xác định giới tính',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],

                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Nơi sinh trong nước/ nước ngoài',
                                        'name' => 'noi_sinh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Trong nước', 'Nước ngoài',
                                        ],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Nơi sinh của người đăng ký khai sinh',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'noi_sinh_tinh_thanh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'noi_sinh_phuong_xa_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'noi_sinh_dia_chi_chi_tiet_nguoi_duoc_dk', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Quê quán của người được đăng ký khai sinh',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'que_quan_tinh_thanh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'que_quan_phuong_xa_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'que_quan_dia_chi_chi_tiet_nguoi_duoc_dk', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại khai sinh',
                                        'name' => 'loai_khai_sinh',
                                        'type' => 'select',
                                        'col' => 6,
                                        'required' => true,
                                        'options' => [
                                            'Đã xác định được cả cha lẫn mẹ', 'Chưa xác định được mẹ', 'Chưa xác định được cha', 'Chưa xác định được cả cha lẫn mẹ', 'Trẻ bị bỏ rơi',
                                        ],
                                    ],
                                ],
                            ],

                        ],
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN VỀ NGƯỜI MẸ
                    // ==================================================
                    [
                        'group' => 'Thông tin về người mẹ',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_me', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_me', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_me', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_me', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_me', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_me', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_nu',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                                    ],
                                ],

                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 4],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Xác thực thông tin',
                                        'name' => 'xac_thuc_thong_tin_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thông tin cá nhân, cư trú đã được xác thực với CSDLQG về dân cư', 'Thông tin chưa được xác thực', 'Thông tin các nhân đã được xác thực với CSDLQG về dân cư',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân của người mẹ',
                                        'name' => 'loai_giay_to_nu',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ tùy thân', 'name' => 'so_giay_to_thuy_than_me', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Số định danh cá nhân của người mẹ', 'name' => 'sddcn_me', 'type' => 'date', 'col' => 4],

                                ],
                            ],

                        ],
                    ],
                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN NGƯỜI ĐƯỢC ĐĂNG KÝ KHAI SINH
                    // ==================================================
                    [
                        'group' => 'Thông tin về người cha',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_cha', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_cha', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_cha', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_cha', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_cha', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_cha', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                                    ],
                                ],

                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_cha', 'type' => 'text', 'col' => 4],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Xác thực thông tin',
                                        'name' => 'xac_thuc_thong_tin_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thông tin cá nhân, cư trú đã được xác thực với CSDLQG về dân cư', 'Thông tin chưa được xác thực', 'Thông tin các nhân đã được xác thực với CSDLQG về dân cư',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân của người mẹ',
                                        'name' => 'loai_giay_to_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ tùy thân', 'name' => 'so_giay_to_thuy_than_cha', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Số định danh cá nhân của người cha', 'name' => 'sddcn_cha', 'type' => 'date', 'col' => 4],

                                ],
                            ],

                        ],
                    ],

                    [
                        'label' => 'Loại HTTP',
                        'name' => 'loai_http',
                        'type' => 'text',
                        'value' => 'KS',
                        'disable' => true,
                    ],

                ]),
            ],

            // ==================================================
            // 💍 THỦ TỤC CHỨNG THỰC CHỮ KÝ
            // ==================================================
            [
                'maTTHC' => 4,
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Số CMND/ CCCD', 'name' => 'so_cmnd_cccd', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 4],

                                ],
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [

                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 5],

                                ],
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Số nhà/ đường', 'name' => 'so_nha', 'type' => 'text', 'col' => 4],
                                ],
                            ],

                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên doanh nghiệp', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],

                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 4],
                                ],
                            ],

                        ],
                    ],

                ]),
            ],

            // ==================================================
            // 💍 THỦ TỤC CHỨNG THỰC BẢN SAO TỪ BẢN CHÍNH
            // ==================================================
            [
                'maTTHC' => 5,
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Số CMND/ CCCD', 'name' => 'so_cmnd_cccd', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 4],

                                ],
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [

                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 5],

                                ],
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Số nhà/ đường', 'name' => 'so_nha', 'type' => 'text', 'col' => 4],
                                ],
                            ],

                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên doanh nghiệp', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],

                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 4],
                                ],
                            ],

                        ],
                    ],

                ]),
            ],

            // ====================== LIÊN THÔNG CÁC THỦ TỤC HÀNH CHÍNH VỀ ĐĂNG KÝ KHAI SINH

            [
                'maTTHC' => 6, // Đăng ký kết hôn
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ],
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nguoi_nop', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 3,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3,
                                        'required' => true,
                                    ],
                                ],
                            ],

                        ],
                    ],

                    // ==================================================
                    // 💍 FORM 2: THÔNG TIN CHI TIẾT
                    // ==================================================
                    [
                        'group' => 'Thông tin chi tiết',
                        'fields' => [
                            [

                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Nơi đăng ký khai sinh',
                                        'name' => 'noi_dang_ky_khai_sinh',
                                        'type' => 'text',
                                        'disable' => true,
                                        'value' => 'UBND phường ABC',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Loại đăng ký',
                                        'name' => 'loai_dang_ky',
                                        'type' => 'select',
                                        'options' => ['Đăng ký đúng hạn', 'Đăng ký quá hạn', 'Đăng ký lại', 'Ghi vào sổ việc khai sinh đã đăng ký tại cơ quan có thẩm quyền ở nước ngoài'],
                                    ],
                                    ['label' => 'Số lượng bản sao đề nghị cấp', 'name' => 'so_luong_ban_sao', 'type' => 'number', 'min' => 1],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN NGƯỜI YÊU CẦU ĐĂNG KÝ KHAI SINH
                    // ==================================================
                    [
                        'group' => 'Thông tin người yêu cầu đăng ký khai sinh',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nguoi_yeu_cau', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Đệm', 'name' => 'dem_nguoi_yeu_cau', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nguoi_yeu_cau', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_nu',
                                        'type' => 'radio',
                                        'col' => 4,
                                        'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                                    ],
                                ],

                            ],

                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nguoi_yeu_cau', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nguoi_yeu_cau', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nguoi_yeu_cau',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quan hệ với người được khai sinh',
                                        'name' => 'quan_he_nguoi_duoc_khai_sinh',
                                        'type' => 'text',
                                        'required' => true,
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Xác thực thông tin ',
                                        'name' => 'xac_thuc_thong_tin',
                                        'type' => 'select',
                                        'required' => true,
                                        'col' => 3,
                                        'options' => [
                                            'Thông tin chưa được xác thực', 'Thông tin danh tính cá nhân, cư trú đã được xác thực với CSDLQG về dân cư', 'Thông tin danhh tính cá nhân đã được xác thực với CSDLQG về dân cư',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN NGƯỜI ĐƯỢC ĐĂNG KÝ KHAI SINH
                    // ==================================================
                    [
                        'group' => 'Thông tin về người được đăng ký khai sinh',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nguoi_duoc_dk', 'type' => 'text', 'col' => 4,
                                        'required' => true,
                                    ],
                                    ['label' => 'Đệm', 'name' => 'dem_nguoi_duoc_dk', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nguoi_duoc_dk', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nam_nguoi_duoc_dk', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nam_nguoi_duoc_dk', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nam_nguoi_duoc_dk', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Ngày tháng năm sinh ghi bằng chữ',
                                        'name' => 'ngay_thang_nam_chu_nguoi_duoc_dk',
                                        'type' => 'text',
                                        'col' => 8,
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giói tính',
                                        'name' => 'gioi_tinh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Nam', 'Nữ', 'Chưa xác định giới tính',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],

                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Nơi sinh trong nước/ nước ngoài',
                                        'name' => 'noi_sinh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Trong nước', 'Nước ngoài',
                                        ],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Nơi sinh của người đăng ký khai sinh',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'noi_sinh_tinh_thanh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'noi_sinh_phuong_xa_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'noi_sinh_dia_chi_chi_tiet_nguoi_duoc_dk', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Quê quán của người được đăng ký khai sinh',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'que_quan_tinh_thanh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'que_quan_phuong_xa_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'que_quan_dia_chi_chi_tiet_nguoi_duoc_dk', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại khai sinh',
                                        'name' => 'loai_khai_sinh',
                                        'type' => 'select',
                                        'col' => 6,
                                        'required' => true,
                                        'options' => [
                                            'Đã xác định được cả cha lẫn mẹ', 'Chưa xác định được mẹ', 'Chưa xác định được cha', 'Chưa xác định được cả cha lẫn mẹ', 'Trẻ bị bỏ rơi',
                                        ],
                                    ],
                                ],
                            ],

                        ],
                    ],

                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN VỀ NGƯỜI MẸ
                    // ==================================================
                    [
                        'group' => 'Thông tin về người mẹ',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_me', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_me', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_me', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_me', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_me', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_me', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_nu',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                                    ],
                                ],

                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nu', 'type' => 'text', 'col' => 4],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Xác thực thông tin',
                                        'name' => 'xac_thuc_thong_tin_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thông tin cá nhân, cư trú đã được xác thực với CSDLQG về dân cư', 'Thông tin chưa được xác thực', 'Thông tin các nhân đã được xác thực với CSDLQG về dân cư',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_me',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân của người mẹ',
                                        'name' => 'loai_giay_to_nu',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ tùy thân', 'name' => 'so_giay_to_thuy_than_me', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Số định danh cá nhân của người mẹ', 'name' => 'sddcn_me', 'type' => 'date', 'col' => 4],

                                ],
                            ],

                        ],
                    ],
                    // ==================================================
                    // 🤵 FORM 4: THÔNG TIN NGƯỜI ĐƯỢC ĐĂNG KÝ KHAI SINH
                    // ==================================================
                    [
                        'group' => 'Thông tin về người cha',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_cha', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Đệm', 'name' => 'dem_cha', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_cha', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_cha', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_cha', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_cha', 'type' => 'number', 'col' => 4,
                                        'required' => true, 'min' => 1900, 'max' => date('Y'), ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                                    ],
                                ],

                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [

                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_cha', 'type' => 'text', 'col' => 4],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Xác thực thông tin',
                                        'name' => 'xac_thuc_thong_tin_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thông tin cá nhân, cư trú đã được xác thực với CSDLQG về dân cư', 'Thông tin chưa được xác thực', 'Thông tin các nhân đã được xác thực với CSDLQG về dân cư',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân của người mẹ',
                                        'name' => 'loai_giay_to_cha',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ tùy thân', 'name' => 'so_giay_to_thuy_than_cha', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Số định danh cá nhân của người cha', 'name' => 'sddcn_cha', 'type' => 'date', 'col' => 4],

                                ],
                            ],

                        ],
                    ],

                    [
                        'label' => 'Loại HTTP',
                        'name' => 'loai_http',
                        'type' => 'text',
                        'value' => 'KS',
                        'disable' => true,
                    ],

                ]),
            ],

            // 💍 GIA HẠN GIẤY PHÉP XÂY DỰNG ĐỐI VỚI CÔNG TRÌNH CẤP 3
            // ==================================================

            [
                'maTTHC' => 7,
                'cauHinhForm' => json_encode([
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [
                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ],
                            ],
                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Chứng minh nhân dân', 'Căn cước công dân', 'Hộ chiếu', 'Thẻ căn cước',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],
                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3],
                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên cơ quan / hộ kinh doanh', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 5],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],
                                    [
                                        'label' => 'Hình thức nộp hồ sơ',
                                        'name' => 'hinh_thuc_nop',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => ['Trực tiếp', 'Trực tuyến', 'Dịch vụ bưu chính'],
                                    ],
                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 3],
                                ],
                            ],
                        ],
                    ],

                    [
                        'group' => 'Thông tin chi tiết',
                        'fields' => [
                            // 1. PHẦN TỬ ĐIỀU KHIỂN (TRIGGER)
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Chọn loại công trình',
                                        'name' => 'loai_cong_trinh',
                                        'type' => 'select',
                                        'col' => 12,
                                        'options' => [
                                            'nha_o' => 'Công trình/ Nhà ở riêng lẻ',
                                            'du_an' => 'Công trình không theo tuyến',
                                        ],

                                        'attributes' => ['data-trigger' => 'group_loai_ct'],
                                    ],
                                ],
                            ],

                            // 2. CÁC TRƯỜNG HIỆN KHI CHỌN 'nha_o'
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '1. Tên chủ đầu tư (chủ hộ)', 'name' => 'ten_chu_dau_tu', 'type' => 'text', 'col' => 6,
                                        // Chỉ hiện khi trigger 'group_loai_ct' có giá trị 'nha_o'
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                    [
                                        'label' => 'Tên người được ủy quyền', 'name' => 'ten_nguoi_duoc_uy_quyen', 'type' => 'text', 'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Địa chỉ liên hệ',
                                'columns' => [
                                    [
                                        'label' => 'Tỉnh/ Thành phố',
                                        'name' => 'tinh_nha_o',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],

                                    [
                                        'label' => 'Phường/ xã',
                                        'name' => 'phuong_nha_o',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],

                                    [
                                        'label' => 'Phường/ Xã/ Thị trấn cũ',
                                        'name' => 'phuong_nha_o_cu',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],

                                    [
                                        'label' => 'Địa chỉ chi tiết',
                                        'name' => 'dia_chi_chi_tiet_cu',
                                        'type' => 'text',
                                        'col' => 3,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => '2. Địa điểm xây dựng',
                                'columns' => [
                                    [
                                        'label' => 'Lô đất sổ',
                                        'name' => 'lo_dat_so',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                    [
                                        'label' => 'Diện tích',
                                        'name' => 'dich_tich',
                                        'type' => 'text',
                                        'placeholder' => 'm²',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Tỉnh/ Thành phố',
                                        'name' => 'tinh_nha_o_xay_dung',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],

                                    [
                                        'label' => 'Phường/ xã',
                                        'name' => 'phuong_nha_o_xay_dung',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],

                                    [
                                        'label' => 'Phường/ Xã/ Thị trấn cũ',
                                        'name' => 'phuong_nha_o_cu_xay_dung',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],

                                    [
                                        'label' => 'Địa chỉ chi tiết',
                                        'name' => 'dia_chi_chi_tiet_cu_xay_dung',
                                        'type' => 'text',
                                        'col' => 3,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Người đại điện',
                                        'name' => 'nguoi_dai_dien_xay_dung',
                                        'type' => 'text',
                                        'col' => '6',
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                    [
                                        'label' => 'Chức vụ',
                                        'name' => 'chuc_vu_xay_dung',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => '3. Giấy phép xây dựng đã được cấp',
                                'columns' => [
                                    [
                                        'label' => 'Số giấy phép ',
                                        'name' => 'so_giay_phep',
                                        'type' => 'text',
                                        'col' => '4',
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                    [
                                        'label' => 'Ngày cấp',
                                        'name' => 'ngay_cap',
                                        'type' => 'date',
                                        'col' => 4,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                    [
                                        'label' => 'Cơ quan cấp',
                                        'name' => 'co_quan_cap',
                                        'type' => 'text',
                                        'col' => 4,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Nội dung Giấy phép', 'name' => 'Nội dung Giấy phép', 'type' => 'text', 'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '4. Nội dung đề nghị điều chỉnh so với Giấy phép đã được cấp (hoặc lý do đề nghị gia hạn/cấp lại):', 'name' => 'noi_dung_de_nghi', 'type' => 'text', 'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '5.  Dự kiến thời gian hoàn thành công trình theo thiết kế điều chỉnh/gia hạn:', 'name' => 'du_kien_hoan_thanh', 'placeholder' => 'Số tháng', 'type' => 'text', 'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'nha_o'],
                                    ],
                                ],
                            ],

                            // 3. CÁC TRƯỜNG HIỆN KHI CHỌN 'du_an'
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Cơ quan cấp giấy phép xây dựng', 'name' => 'co_quan_cap_GPXD', 'type' => 'text', 'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    [
                                        'label' => 'Số GPXD', 'name' => 'so_GPXD', 'type' => 'text', 'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '1. Cấp cho',
                                        'name' => 'cap_cho',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Địa chỉ',
                                'columns' => [
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 4, 'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an']],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '2. Được phép xây dựng công trình:',
                                        'name' => 'duoc_phep_xay_dung',
                                        'type' => 'text',
                                        'placeholder' => 'Tên công trình',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '- Theo thiết kế:',
                                        'name' => 'theo_thiet_ke',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '- Do',
                                        'name' => 'do',
                                        'type' => 'text',
                                        'placeholder' => 'Tên tổ chức tư vấn lập',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '- Chủ nhiệm, chủ trì thiết kế:',
                                        'name' => 'Chủ nhiệm, chủ trì thiết kế:',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '- Đơn vị thẩm định, thẩm tra (nếu có):',
                                        'name' => 'Đơn vị thẩm định, thẩm tra (nếu có):',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '- Chủ trì thẩm tra thiết kế:',
                                        'name' => 'Chủ trì thẩm tra thiết kế:',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => '+ Vị trí xây dựng (ghi rõ lô đất, địa chỉ):',

                                'columns' => [

                                    [

                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 4, 'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an']],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '+ Cốt nền xây dựng công trình',
                                        'name' => '+ Cốt nền xây dựng công trình',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '+ Mật độ xây dựng:',
                                        'name' => '+ Mật độ xây dựng:',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    [
                                        'label' => 'hệ số sử dụng đất:',
                                        'name' => 'hệ số sử dụng đất:',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '+ Chỉ giới đường đỏ',
                                        'name' => '+ Chỉ giới đường đỏ',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    [
                                        'label' => 'chỉ giới xây dựng:',
                                        'name' => 'chỉ giới xây dựng:',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '+ Màu sắc công trình (nếu có):',
                                        'name' => '+ Màu sắc công trình (nếu có):',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '+ Chiều sâu công trình (tính từ cốt 0,00 đối với công trình có tầng hầm):',
                                        'name' => '+ Chiều sâu công trình (tính từ cốt 0,00 đối với công trình có tầng hầm):',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'Đối với công trình dân dụng và công trình có kết cầu dạng nhà, bổ sung các nội dung sau:',
                                'columns' => [
                                    [
                                        'label' => '+ Diện tích xây dựng tầng 1 (tầng trệt):',
                                        'name' => '+ Diện tích xây dựng tầng 1 (tầng trệt):',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],

                                    [
                                        'label' => '+ Tổng diện tích sàn (bao gồm cả tầng hầm và tầng lửng):',
                                        'name' => '+ Tổng diện tích sàn (bao gồm cả tầng hầm và tầng lửng):',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],

                                    [
                                        'label' => '+ Chiều cao công trình:',
                                        'name' => '+ Chiều cao công trình:',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                    [
                                        'label' => '+ Số tầng (trong đó ghi rõ số tầng hầm và tầng lửng):',
                                        'name' => '+ Số tầng (trong đó ghi rõ số tầng hầm và tầng lửng):',
                                        'type' => 'text',
                                        'col' => 6,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => '3. Giấy tờ về đất đai:',
                                        'name' => '3. Giấy tờ về đất đai:',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'title' => 'ĐIỀU CHỈNH/GIA HẠN GIẤY PHÉP',
                                'columns' => [
                                    [
                                        'label' => 'Nội dung điều chỉnh/gia hạn:',
                                        'name' => 'Nội dung điều chỉnh/gia hạn:',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Thời gian có hiệu lực của giấy phép:',
                                        'name' => 'Thời gian có hiệu lực của giấy phép:',
                                        'type' => 'text',
                                        'col' => 12,
                                        'attributes' => ['data-group' => 'group_loai_ct', 'data-show' => 'du_an'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
            ],

            // ======================================
            // THỦ TỤC CẤP GIẤY XÁC NHẬN TÌNH TRẠNG HÔN NHÂN
            // ================================================
            [
                'maTTHC' => 8, // Đăng ký kết hôn
                'cauHinhForm' => json_encode([

                    // ==================================================
                    // 🧾 FORM 1: THÔNG TIN NGƯỜI NỘP
                    // ==================================================
                    [
                        'group' => 'Thông tin người nộp',
                        'fields' => [

                            // Họ tên - Ngày sinh - SĐT - Email
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ và tên ', 'name' => 'ho_ten', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Số điện thoại', 'name' => 'so_dien_thoai', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Email', 'name' => 'email', 'type' => 'email', 'col' => 3],
                                ],
                            ],

                            // Giấy tờ tùy thân - Số giấy tờ - Ngày cấp - Nơi cấp
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Chứng minh nhân dân', 'Căn cước công dân', 'Hộ chiếu', 'Thẻ căn cước',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap', 'type' => 'date', 'col' => 3,
                                        'required' => true, ],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Công an Tỉnh Băc Kạn', 'Công an Tỉnh Bạc Liêu', 'Công an Tỉnh Bắc Ninh', 'Công an Tỉnh Bình Định', 'Công an Tỉnh Bình Dương', 'Công an Tỉnh Bình Phước', 'Công an Tỉnh Bình Thuận', 'Công an Tỉnh Cà Mau', 'Công an TP.Cần Thơ', 'Công an Tỉnh Cao Bằng ', 'Công an TP. Hải Phòng', 'Công an Tỉnh Gia Lai', 'Công an Tỉnh Hà Nam', 'Công an Tỉnh Hòa Bình', 'Công an Tỉnh Hưng Yên ', 'Công an Tỉnh Hải Dương', 'Công an Tỉnh Hậu Giang', 'Công an Tỉnh Khánh Hòa', 'Công an Tỉnh Kiên Giang', 'Công an Tỉnh Kon Tum', 'Công an Tỉnh Lai Châu', '',
                                        ],
                                    ],
                                ],
                            ],

                            // Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                ],
                            ],

                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Tên cơ quan / hộ kinh doanh', 'name' => 'ten_co_quan', 'type' => 'text', 'col' => 5],
                                    ['label' => 'Mã số thuế / Mã doanh nghiệp', 'name' => 'ma_so_thue', 'type' => 'text', 'col' => 4],
                                    [
                                        'label' => 'Hình thức nộp hồ sơ',
                                        'name' => 'hinh_thuc_nop',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => ['Trực tiếp', 'Trực tuyến', 'Dịch vụ bưu chính'],
                                    ],
                                ],
                            ],
                            // Cơ quan - Mã số thuế - Hình thức nộp hồ sơ  - Quốc gia - Tỉnh/TP - Phường/Xã - Địa chỉ
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_co_quan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_co_quan', 'type' => 'text', 'col' => 3],
                                ],
                            ],

                        ],
                    ],

                    // ==================================================
                    // 💍 FORM 2: THÔNG TIN VỀ NGƯỜI YÊU CẦU
                    // ==================================================
                    [
                        'group' => 'Thông tin về người yêu cầu',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nguoi_yeu_cau', 'type' => 'text', 'col' => 4, 'required' => true],
                                    ['label' => 'Chữ đệm', 'name' => 'dem_nguoi_yeu_cau', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nguoi_yeu_cau', 'type' => 'text', 'col' => 4], 'required' => true,
                                ],
                            ],
                            [
                                [
                                    'type' => 'row',
                                    'title' => 'Ngày tháng năm sinh',
                                    'columns' => [
                                        ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nguoi_yeu_cau', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                        ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nguoi_yeu_cau', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                        ['label' => 'Năm sinh', 'name' => 'nam_sinh_nguoi_yeu_cau', 'type' => 'number', 'col' => 4, 'required' => true, 'min' => 1900, 'max' => date('Y')],
                                    ],
                                ],

                                [
                                    'type' => 'row',
                                    'title' => 'Nơi cư trú',
                                    'columns' => [
                                        [
                                            'label' => 'Quốc gia',
                                            'name' => 'quoc_gia_nguoi_yeu_cau',
                                            'type' => 'select',
                                            'col' => 3,
                                            'options' => [
                                                'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                            ],
                                        ],
                                        [
                                            'label' => 'Tỉnh/Thành phố',
                                            'name' => 'tinh_thanh_nguoi_yeu_cau',
                                            'type' => 'select',
                                            'col' => 3,
                                            'options' => [
                                                'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                            ],
                                        ],
                                        [
                                            'label' => 'Phường/Xã',
                                            'name' => 'phuong_xa_nguoi_yeu_cau',
                                            'type' => 'select',
                                            'col' => 3,
                                            'options' => [
                                                'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                            ],
                                        ],
                                        ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nguoi_yeu_cau', 'type' => 'text', 'col' => 3,
                                            'required' => true, ],
                                    ],
                                ],
                                [
                                    'type' => 'row',
                                    'columns' => [

                                        ['label' => 'Quan hệ với người được cấp Giấy XNTTHN', 'name' => 'quan_he_voi_nguoi_duoc_cap', 'type' => 'text', 'col' => 6],
                                        ['label' => 'Loại hộ tịch', 'name' => 'loai_ho_tich', 'type' => 'text', 'value' => 'XNTTHN', 'disable' => true],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // ==================================================
                    // 👰 FORM 3: THÔNG TIN VỀ NGƯỜI ĐƯỢC CẤP GIẤY XÁC NHẬN TÌNH TRẠNG HÔN NHÂN
                    // ==================================================
                    [
                        'group' => 'Thông tin về người được cấp giấy xác nhận tình trạng hôn nhân',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'ho_nguoi_duoc_cap', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                    ['label' => 'Đệm', 'name' => 'dem_nguoi_duoc_cap', 'type' => 'text', 'col' => 4],
                                    ['label' => 'Tên', 'name' => 'ten_nguoi_duoc_cap', 'type' => 'text', 'col' => 4,
                                        'required' => true, ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày tháng năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày sinh', 'name' => 'ngay_sinh_nguoi_duoc_cap', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 31],
                                    ['label' => 'Tháng sinh', 'name' => 'thang_sinh_nguoi_duoc_cap', 'type' => 'number', 'col' => 4, 'min' => 1, 'max' => 12],
                                    ['label' => 'Năm sinh', 'name' => 'nam_sinh_nguoi_duoc_cap', 'type' => 'number', 'col' => 4, 'required' => true, 'min' => 1900, 'max' => date('Y')],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giói tính',
                                        'name' => 'gioi_tinh_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Nam', 'Nữ', 'Chưa xác định giới tính',
                                        ],
                                    ],
                                    [
                                        'label' => 'Dân tộc',
                                        'name' => 'dan_toc_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'options' => [
                                            'Kinh', 'Khơ me', 'Hà Nhì', 'Giẻ Triêng', 'Hơ mông', 'Ê Đê', 'Ba Na', 'La Chí', 'Cờ Ho', 'Brâu', 'Xơ Đăng', 'Thổ', 'Thái', 'Tà Ôi', 'Hoa', 'Sán Dìu', 'Pu Péo',
                                        ],
                                    ],
                                    [
                                        'label' => 'Quốc tịch',
                                        'name' => 'quoc_tich_nguoi_duoc_dk',
                                        'type' => 'select',
                                        'col' => 4,
                                        'required' => true,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],

                                ],
                            ],

                            [
                                'label' => 'Loại cư trú',
                                'name' => 'loai_cu_tru_nguoi_duoc_cap',
                                'type' => 'select',
                                'required' => true,
                                'options' => ['Thường trú', 'Tạm trú', 'Nơi ở hiện tại'],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_nguoi_duoc_cap', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                ],
                            ],

                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_nguoi_duoc_cap',
                                        'type' => 'select',
                                        'col' => 3,
                                        'required' => true,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Thẻ Căn cước công dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước', 'Giấy chứng minh quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', '',
                                        ],
                                    ],
                                    ['label' => 'Số giấy tờ', 'name' => 'so_giay_to_nguoi_duoc_cap', 'type' => 'text', 'col' => 3,
                                        'required' => true, ],
                                    ['label' => 'Ngày cấp', 'name' => 'ngay_cap_nguoi_duoc_cap', 'type' => 'date', 'col' => 3],
                                    [
                                        'label' => 'Nơi cấp giấy tờ',
                                        'name' => 'noi_cap_giay_to_nguoi_duoc_cap',
                                        'type' => 'text',
                                        'col' => 3,

                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại tình trạng hôn nhân',
                                        'name' => 'loai_tinh_trang_hon_nhan',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => ['Hiện tại chưa đăng ký kết hôn với ai', 'Hiện tại đang có vợ/ chồng', 'Đã đăng ký kết hôn hoặc đã có vợ/ chồng', 'nhưng đã ly hôn, hiện tại chưa đăng ký kết hôn với ai', 'Đã đăng ký kết hôn hoặc đã có vợ/ chồng nhưng vợ/ chồng đã chết, hiện tại chưa đăng ký kết hôn với ai', 'Từ...ngày...tháng...năm đến ngày...tháng...năm chưa đăng ký kết hôn với ai, hiện tại đang có vợ/ chồng'],
                                    ],

                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Vui lòng bỏ qua thông tin phía dưới trong trường hợp tình trạng hôn nhân là chưa đăng ký kết hôn với ai',
                                'columns' => [
                                    [
                                        'label' => 'Số giấy tờ kết hôn/ Quyết định ly hôn',
                                        'name' => 'so_giay_to_ket_hon',
                                        'type' => 'text',
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Cơ quan cấp giấy tờ',
                                        'name' => 'co_quan_cap_giay_to',
                                        'type' => 'text',
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Ngày cấp giấy tờ',
                                        'name' => 'ngay_cap_giay_to',
                                        'type' => 'date',
                                        'col' => 4,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Xác nhận từ ngày',
                                        'name' => 'xac_nhan_tu_ngay',
                                        'type' => 'date',
                                        'col' => 4,
                                    ],
                                    [
                                        'label' => 'Xác nhận đến ngày',
                                        'name' => 'xac_nhan_den_ngay',
                                        'type' => 'date',
                                        'col' => 4,
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Họ tên của vợ/ chồng của người yêu cầu XNTTHN', 'name' => 'ho_ten_nguoi_yeu_cau', 'type' => 'text', 'col' => 6],

                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Loại mục đích sử dụng', 'name' => 'loai_muc_dich_su_dung', 'type' => 'select', 'col' => 6,
                                        'options' => ['dai_dien_viet_nam' => 'Để kết hôn tại cơ quan đại diện Việt Nam ở nước ngoài', 'dai_dien_viet_nam' => 'Để kết hôn tại cơ quan có thẩm quyền của nước ngoài', 'muc_dich_khac' => 'Sử dụng mục đích khác'],
                                        'attributes' => ['data-trigger' => 'group_loai_mdsd']],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    ['label' => 'Nội dung chi tiết mục đích xác nhận tình trạng hôn nhân', 'name' => 'noi_dung_chi_tiet_muc_dich', 'type' => 'text', 'col' => 6, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'muc_dich_khac']],

                                ],
                            ],
                        ],
                    ],
                    [
                        'group' => 'Thông tin về đối tượng đăng ký kết hôn',
                        'fields' => [
                            [
                                'type' => 'row',
                                'title' => 'Họ, chữ đệm, tên',
                                'columns' => [
                                    ['label' => 'Họ', 'name' => 'doi_tuong_dk', 'type' => 'text', 'col' => 4, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                    ['label' => 'Chữ đệm', 'name' => 'dem_doi_tuong_dk', 'type' => 'text', 'col' => 4, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                    ['label' => 'Tên', 'name' => 'ten_doi_tuong_dk', 'type' => 'text', 'col' => 4, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Ngày, tháng, năm sinh',
                                'columns' => [
                                    ['label' => 'Ngày', 'name' => 'ngay_doi_tuong_dk', 'type' => 'number', 'min' => 1, 'max' => 31, 'col' => 4, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                    ['label' => 'Tháng', 'name' => 'thang_doi_tuong_dk', 'type' => 'number', 'min' => 1, 'max' => 12, 'col' => 4, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                    ['label' => 'Năm', 'name' => 'nam_doi_tuong_dk', 'type' => 'number', 'min' => 1900, 'col' => 4, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại cư trú',
                                        'name' => 'loai_cu_tru_doi_tuong_dk',
                                        'col' => 4,
                                        'type' => 'select',
                                        'required' => true,
                                        'options' => [
                                            'Thường trú',
                                            'Tạm trú',
                                            'Nơi ở hiện tại',
                                        ], 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'title' => 'Nơi cư trú',
                                'columns' => [
                                    [
                                        'label' => 'Quốc gia',
                                        'name' => 'quoc_gia_doi_tuong_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Việt Nam', 'CH Séc', 'Brunei', 'Triều Tiên', 'Venezuela', 'Myanmar', 'Tây Ban Nha', 'Ả Rập Xê Úp', 'Ả Rập', 'Campuchia', 'Indonesia', 'Bhutan', 'Hungary', 'Australia', 'Lào', 'Iran', 'Pakistan',
                                        ],
                                        'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                    [
                                        'label' => 'Tỉnh/Thành phố',
                                        'name' => 'tinh_thanh_doi_tuong_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ], 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                    [
                                        'label' => 'Phường/Xã',
                                        'name' => 'phuong_xa_doi_tuong_dk',
                                        'type' => 'select',
                                        'col' => 3,
                                        'options' => [
                                            'Thành phố Cần Thơ', 'Thủ đô Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Hồ Chí Minh', 'Tỉnh Đồng Nai',
                                        ], 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                    ['label' => 'Địa chỉ chi tiết', 'name' => 'dia_chi_chi_tiet_doi_tuong_dk', 'type' => 'text', 'col' => 3, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                ],
                            ],
                            [
                                'type' => 'row',
                                'columns' => [
                                    [
                                        'label' => 'Loại giấy tờ tùy thân',
                                        'name' => 'loai_giay_to_tuy_than_doi_tuong_dk',
                                        'type' => 'select',

                                        'col' => 3,
                                        'options' => [
                                            'Giấy Chứng minh nhân dân', 'Hộ chiếu', 'Thẻ thường trú', 'Thẻ căn cước công dân', 'Giấy chứng minh Quân đội nhân dân', 'Giấy chứng minh Sỹ quan quân đội', 'Giấy chứng minh Công an nhân dân', 'Giấy tờ khác', 'Giấy khai sinh', 'Giấy chứng sinh', 'Thẻ căn cước', 'Giấy chứng nhân căn cước',
                                        ], 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                    [
                                        'label' => 'Số giấy tờ',
                                        'name' => 'so_giay_to_doi_tuong_dk',
                                        'type' => 'text',

                                        'col' => 3,
                                        'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                    [
                                        'label' => 'Ngày cấp',
                                        'name' => 'ngay_cap_doi_tuong_dk',
                                        'type' => 'date',

                                        'col' => 3,
                                        'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam'],
                                    ],
                                    ['label' => 'Nơi cấp giấy tờ', 'name' => 'noi_cap_giay_to_doi_tuong_dk', 'type' => 'text', 'col' => 3, 'attributes' => ['data-group' => 'group_loai_mdsd', 'data-show' => 'dai_dien_viet_nam']],
                                ],
                            ],
                        ],
                    ],

                ]),
            ],

        ]);
    }
}
