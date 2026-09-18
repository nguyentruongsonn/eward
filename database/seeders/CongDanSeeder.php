<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CongDanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('congdan')->insert([
            [
                'IDCD' => 1,
                'IDnguoiDung' => 1,
            ],
            // [
            //     'IDCD'=>2,
            //     'IDnguoiDung'=>3
            // ],
            // [
            //     'IDCD'=>3,
            //     'IDnguoiDung'=>5
            // ],
        ]);
    }
}
