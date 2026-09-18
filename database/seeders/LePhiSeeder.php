<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LePhiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lephi')->insert([
            [
                'maLePhi' => 1,
                'loaiLePhi' => 'Phí số lượng bản',
                'maTTHC' => 1,
                'soTien' => 8000,
                'batBuoc' => 'Không',
                'moTa' => '8000/ bản sao',
            ],
            [
                'maLePhi' => 2,
                'loaiLePhi' => 'Phí số lượng bản',
                'maTTHC' => 2,
                'soTien' => 8000,
                'batBuoc' => 'Không',
                'moTa' => '8000/ bản sao',
            ],
            [
                'maLePhi' => 3,
                'loaiLePhi' => 'Phí số lượng bản',
                'maTTHC' => 3,
                'soTien' => 8000,
                'batBuoc' => 'Không',
                'moTa' => 'Trường hợp được tính là một hoặc nhiều chữ ký trong một giấy tờ, văn bản',
            ],
            [
                'maLePhi' => 4,
                'loaiLePhi' => 'Phí số lượng bản',
                'maTTHC' => 4,
                'soTien' => 10000,
                'batBuoc' => 'Không',
                'moTa' => 'trường hợp được tính là một hoặc nhiều chữ ký trong một giấy tờ, văn bản',
            ],
            [
                'maLePhi' => 5,
                'loaiLePhi' => 'Phí số lượng bản',
                'maTTHC' => 5,
                'soTien' => 8000,
                'batBuoc' => 'Không',
                'moTa' => 'trường hợp được tính là một hoặc nhiều chữ ký trong một giấy tờ, văn bản',
            ],
            [
                'maLePhi' => 6,
                'loaiLePhi' => 'Phí, lệ phí   ',
                'maTTHC' => 6,
                'soTien' => 0,
                'batBuoc' => 'Không',
                'moTa' => 'trường hợp được tính là một hoặc nhiều chữ ký trong một giấy tờ, văn bản',
            ],
            [
                'maLePhi' => 7,
                'loaiLePhi' => 'Phí số lượng bản',
                'maTTHC' => 7,
                'soTien' => 8000,
                'batBuoc' => 'Không',
                'moTa' => 'trường hợp được tính là một hoặc nhiều chữ ký trong một giấy tờ, văn bản',
            ],
            [
                'maLePhi' => 8,
                'loaiLePhi' => 'Lệ phí',
                'maTTHC' => 8,
                'soTien' => 8000,
                'batBuoc' => 'Không',
                'moTa' => 'trường hợp được tính là một hoặc nhiều chữ ký trong một giấy tờ, văn bản',
            ],
        ]);
    }
}
