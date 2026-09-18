<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThuTucDoiTuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thutucdoituong')->insert([
            [
                'maTTHC' => 1,
                'maDoiTuong' => 1,
            ],
            [
                'maTTHC' => 1,
                'maDoiTuong' => 2,
            ],
            [
                'maTTHC' => 3,
                'maDoiTuong' => 1,
            ],
        ]);
    }
}
