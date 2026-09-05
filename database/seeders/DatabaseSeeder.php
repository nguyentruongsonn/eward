<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LinhVucSeeder::class,
            QuayLamViecSeeder::class,
            DoiTuongThucHienSeeder::class,
            TTHCSeeder::class,
            CachThucHienSeeder::class,
            ThuTucDoiTuongSeeder::class,
            FormTrucTuyenSeeder::class,
            NguoiSeeder::class,
            CongDanSeeder::class,
            CanBoSeeder::class,
            // QuanTriVienSeeder::class,
            ThanhPhanHoSoSeeder::class,
            GiayToSeeder::class,
            ThanhPhanGiayToSeeder::class,
            FormTrucTuyenSeeder::class,
            LePhiSeeder::class,
            TrangThaiHoSoSeeder::class,
            TinhSeeder::class,
            XaSeeder::class,
        ]);

    }
}
