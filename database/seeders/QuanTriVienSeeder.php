<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuanTriVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quantrivien')->insert([
            // [
            //     'IDQTV'=>1,
            //     'IDnguoiDung'=>7
            // ],
        ]);
    }
}
