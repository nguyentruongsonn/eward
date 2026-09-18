<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuayLamViecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quaylamviec')->insert([
            [
                'maQuayLamViec' => 1,
                'tenQuayLamViec' => 'Quầy 1',
            ],
            [
                'maQuayLamViec' => 2,
                'tenQuayLamViec' => 'Quầy 2',
            ],
            [
                'maQuayLamViec' => 3,
                'tenQuayLamViec' => 'Quầy 3',
            ],
            [
                'maQuayLamViec' => 4,
                'tenQuayLamViec' => 'Quầy 4',
            ],
            [
                'maQuayLamViec' => 5,
                'tenQuayLamViec' => 'Quầy 5',
            ],
        ]);
    }
}
