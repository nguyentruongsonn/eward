<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CanBoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('canbo')->insert([
            [
                'IDCB' => 1,
                'IDnguoiDung' => 2,     // Cán bộ một cửa
                'maQuayLamViec' => '1',
                'chucVu' => 'Cán bộ một cửa',
            ],
            [
                'IDCB' => 2,
                'IDnguoiDung' => 3,     // Cán bộ thụ lý
                'maQuayLamViec' => null,
                'chucVu' => 'Cán bộ thụ lý',
            ],
            [
                'IDCB' => 3,
                'IDnguoiDung' => 4,     // Chủ tịch UBND
                'maQuayLamViec' => null,
                'chucVu' => 'Chủ tịch UBND Phường',
            ],
            [
                'IDCB' => 4,
                'IDnguoiDung' => 8,     // Phó Chủ tịch UBND
                'maQuayLamViec' => null,
                'chucVu' => 'Phó Chủ tịch UBND Phường',
            ],
        ]);
    }
}
