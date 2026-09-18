<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nguoi')->insert([
            [
                'IDnguoiDung' => 1,
                'maCCCD' => '012345678901',
                'hoTen' => 'Nguyễn Văn A',
                'gioiTinh' => 'Nam',
                'ngaySinh' => '2003-04-14',
                'queQuan' => 'Đồng Nai',
                'noiThuongTru' => 'Đồng Nai',
                'noiTamTru' => 'TP. Biên Hòa, Đồng Nai',
                'email' => 'nts594187a@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0905123456',
                'vaiTro' => 'Công dân/ Tổ chức',
            ],
            [
                'IDnguoiDung' => 2,
                'maCCCD' => '012345678902',
                'hoTen' => 'Trần Thị B',
                'gioiTinh' => 'Nữ',
                'ngaySinh' => '2001-09-21',
                'queQuan' => 'TP. Hồ Chí Minh',
                'noiThuongTru' => 'TP. Hồ Chí Minh',
                'noiTamTru' => 'TP. Thủ Đức, TP. HCM',
                'email' => 'canbo1@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0918123456',
                'vaiTro' => 'Cán bộ một cửa',
            ],
            [
                'IDnguoiDung' => 3,
                'maCCCD' => '012345678903',
                'hoTen' => 'Lê Văn C',
                'gioiTinh' => 'Nữ',
                'ngaySinh' => '2001-09-21',
                'queQuan' => 'TP. Hồ Chí Minh',
                'noiThuongTru' => 'TP. Hồ Chí Minh',
                'noiTamTru' => 'TP. Thủ Đức, TP. HCM',
                'email' => 'canbo2@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0918123457',
                'vaiTro' => 'Cán bộ thụ lý',
            ],
            [
                'IDnguoiDung' => 4,
                'maCCCD' => '012345678904',
                'hoTen' => 'Nguyễn Văn Tuấn (Chủ tịch UBND Phường)',
                'gioiTinh' => 'Nam',
                'ngaySinh' => '1978-04-12',
                'queQuan' => 'TP. Hồ Chí Minh',
                'noiThuongTru' => 'TP. Hồ Chí Minh',
                'noiTamTru' => 'TP. Thủ Đức, TP. HCM',
                'email' => 'lanhdao@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0918123458',
                'vaiTro' => 'Lãnh đạo',
            ],
            [
                'IDnguoiDung' => 8,
                'maCCCD' => '012345678908',
                'hoTen' => 'Phạm Thị Dung (Phó Chủ tịch UBND Phường)',
                'gioiTinh' => 'Nữ',
                'ngaySinh' => '1985-05-18',
                'queQuan' => 'TP. Hồ Chí Minh',
                'noiThuongTru' => 'TP. Hồ Chí Minh',
                'noiTamTru' => 'TP. Thủ Đức, TP. HCM',
                'email' => 'phochutich@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0918123460',
                'vaiTro' => 'Lãnh đạo',
            ],

            [
                'IDnguoiDung' => 5,
                'maCCCD' => '012345678905',
                'hoTen' => 'Nguyễn Văn E',
                'gioiTinh' => 'Nữ',
                'ngaySinh' => '2001-09-21',
                'queQuan' => 'TP. Hồ Chí Minh',
                'noiThuongTru' => 'TP. Hồ Chí Minh',
                'noiTamTru' => 'TP. Thủ Đức, TP. HCM',
                'email' => 'admin@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0918123459',
                'vaiTro' => 'Quản trị viên',
            ],
            [
                'IDnguoiDung' => 6,
                'maCCCD' => '012345678906',
                'hoTen' => 'Kiosk',
                'gioiTinh' => 'Nam',
                'ngaySinh' => '1999-12-05',
                'queQuan' => 'Hà Nội',
                'noiThuongTru' => 'Hà Nội',
                'noiTamTru' => 'Hà Đông, Hà Nội',
                'email' => 'kiosk@gmail.com',
                'password' => '$2y$12$QqCkYxiF0yeSl1FqtBGLKO9eFBKo2H5OWd/QJoIOqQlJMUTY7/ovm',
                'soDienThoai' => '0987654321',
                'vaiTro' => 'Checkin',
            ],
            // [
            //     'IDnguoiDung' => 4,
            //     'maCCCD' => '012345678904',
            //     'hoTen' => 'Phạm Thị D',
            //     'gioiTinh' => 'Nữ',
            //     'ngaySinh' => '2000-06-11',
            //     'queQuan' => 'Bình Dương',
            //     'noiThuongTru' => 'Bình Dương',
            //     'noiTamTru' => 'TP. Thủ Dầu Một, Bình Dương',
            //     'email' => 'phamthid@example.com',
            //     'soDienThoai' => '0904556677',
            //     'vaiTro' => 'Cán bộ'
            // ],
            // [
            //     'IDnguoiDung' => 5,
            //     'maCCCD' => '012345678905',
            //     'hoTen' => 'Đặng Văn E',
            //     'gioiTinh' => 'Nam',
            //     'ngaySinh' => '1995-08-25',
            //     'queQuan' => 'Long An',
            //     'noiThuongTru' => 'Long An',
            //     'noiTamTru' => 'TP. Tân An, Long An',
            //     'email' => 'dangvane@example.com',
            //     'soDienThoai' => '0933445566',
            //     'vaiTro' => 'Công dân/ Tổ chức'
            // ],
            // [
            //     'IDnguoiDung' => 6,
            //     'maCCCD' => '012345678906',
            //     'hoTen' => 'Đặng Văn A',
            //     'gioiTinh' => 'Nam',
            //     'ngaySinh' => '2000-08-25',
            //     'queQuan' => 'Long An',
            //     'noiThuongTru' => 'Long An',
            //     'noiTamTru' => 'TP. Tân An, Long An',
            //     'email' => 'dangvana@example.com',
            //     'soDienThoai' => '0933445576',
            //     'vaiTro' => 'Lãnh đạo'
            // ],
            // [
            //     'IDnguoiDung' => 7,
            //     'maCCCD' => '012345678907',
            //     'hoTen' => 'Nguyễn Văn B',
            //     'gioiTinh' => 'Nam',
            //     'ngaySinh' => '2002-07-25',
            //     'queQuan' => 'Tây Ninh',
            //     'noiThuongTru' => 'Tây Ninh',
            //     'noiTamTru' => 'TP. Tân An, Tây Ninh',
            //     'email' => 'nguyenvanb@example.com',
            //     'soDienThoai' => '0933445567',
            //     'vaiTro' => 'Quản trị viên'
            // ],
        ]);
    }
}
