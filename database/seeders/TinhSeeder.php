<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TinhSeeder extends Seeder
{
    public function run(): void
    {
        $tinhs =
        [
            ['maTinh' => '01', 'tenTinh' => 'Thành phố Hà Nội',           'tenTinhKhongDau' => 'ha-noi'],
            ['maTinh' => '02', 'tenTinh' => 'Tỉnh Cao Bằng',             'tenTinhKhongDau' => 'cao-bang'],
            ['maTinh' => '03', 'tenTinh' => 'Tỉnh Tuyên Quang',          'tenTinhKhongDau' => 'tuyen-quang'],
            ['maTinh' => '04', 'tenTinh' => 'Tỉnh Điện Biên',             'tenTinhKhongDau' => 'dien-bien'],
            ['maTinh' => '05', 'tenTinh' => 'Tỉnh Lai Châu',             'tenTinhKhongDau' => 'lai-chau'],
            ['maTinh' => '06', 'tenTinh' => 'Tỉnh Sơn La',               'tenTinhKhongDau' => 'son-la'],
            ['maTinh' => '07', 'tenTinh' => 'Tỉnh Lào Cai',              'tenTinhKhongDau' => 'lao-cai'],
            ['maTinh' => '08', 'tenTinh' => 'Tỉnh Thái Nguyên',          'tenTinhKhongDau' => 'thai-nguyen'],
            ['maTinh' => '09', 'tenTinh' => 'Tỉnh Lạng Sơn',             'tenTinhKhongDau' => 'lang-son'],
            ['maTinh' => '10', 'tenTinh' => 'Tỉnh Quảng Ninh',           'tenTinhKhongDau' => 'quang-ninh'],
            ['maTinh' => '11', 'tenTinh' => 'Tỉnh Bắc Ninh',             'tenTinhKhongDau' => 'bac-ninh'],
            ['maTinh' => '12', 'tenTinh' => 'Tỉnh Phú Thọ',              'tenTinhKhongDau' => 'phu-tho'],
            ['maTinh' => '13', 'tenTinh' => 'Thành phố Hải Phòng',       'tenTinhKhongDau' => 'hai-phong'],
            ['maTinh' => '14', 'tenTinh' => 'Tỉnh Hưng Yên',             'tenTinhKhongDau' => 'hung-yen'],
            ['maTinh' => '15', 'tenTinh' => 'Tỉnh Ninh Bình',            'tenTinhKhongDau' => 'ninh-binh'],
            ['maTinh' => '16', 'tenTinh' => 'Tỉnh Thanh Hóa',            'tenTinhKhongDau' => 'thanh-hoa'],
            ['maTinh' => '17', 'tenTinh' => 'Tỉnh Nghệ An',              'tenTinhKhongDau' => 'nghe-an'],
            ['maTinh' => '18', 'tenTinh' => 'Tỉnh Hà Tĩnh',              'tenTinhKhongDau' => 'ha-tinh'],
            ['maTinh' => '19', 'tenTinh' => 'Tỉnh Quảng Trị',            'tenTinhKhongDau' => 'quang-tri'],
            ['maTinh' => '20', 'tenTinh' => 'Thành phố Huế',             'tenTinhKhongDau' => 'hue'],
            ['maTinh' => '21', 'tenTinh' => 'Thành phố Đà Nẵng',         'tenTinhKhongDau' => 'da-nang'],
            ['maTinh' => '22', 'tenTinh' => 'Tỉnh Quảng Ngãi',           'tenTinhKhongDau' => 'quang-ngai'],
            ['maTinh' => '23', 'tenTinh' => 'Tỉnh Gia Lai',              'tenTinhKhongDau' => 'gia-lai'],
            ['maTinh' => '24', 'tenTinh' => 'Tỉnh Khánh Hòa',            'tenTinhKhongDau' => 'khanh-hoa'],
            ['maTinh' => '25', 'tenTinh' => 'Tỉnh Đắk Lắk',               'tenTinhKhongDau' => 'dak-lak'],
            ['maTinh' => '26', 'tenTinh' => 'Tỉnh Lâm Đồng',             'tenTinhKhongDau' => 'lam-dong'],
            ['maTinh' => '27', 'tenTinh' => 'Tỉnh Đồng Nai',             'tenTinhKhongDau' => 'dong-nai'],
            ['maTinh' => '28', 'tenTinh' => 'Thành phố Hồ Chí Minh',     'tenTinhKhongDau' => 'ho-chi-minh'],
            ['maTinh' => '29', 'tenTinh' => 'Tỉnh Tây Ninh',             'tenTinhKhongDau' => 'tay-ninh'],
            ['maTinh' => '30', 'tenTinh' => 'Tỉnh Đồng Tháp',            'tenTinhKhongDau' => 'dong-thap'],
            ['maTinh' => '31', 'tenTinh' => 'Tỉnh Vĩnh Long',            'tenTinhKhongDau' => 'vinh-long'],
            ['maTinh' => '32', 'tenTinh' => 'Tỉnh An Giang',             'tenTinhKhongDau' => 'an-giang'],
            ['maTinh' => '33', 'tenTinh' => 'Thành phố Cần Thơ',         'tenTinhKhongDau' => 'can-tho'],
            ['maTinh' => '34', 'tenTinh' => 'Tỉnh Cà Mau',               'tenTinhKhongDau' => 'ca-mau'],
        ];

        DB::table('tinh')->insert($tinhs);
    }
}
