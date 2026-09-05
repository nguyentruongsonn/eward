<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sửa kiểu cột maCCCD từ INT sang VARCHAR để lưu được mã CCCD 12 chữ số
        // The initial schema is already VARCHAR for fresh SQLite test databases. Keep
        // the MySQL alteration for installations that started with the old INT column,
        // but do not execute MySQL-only syntax against SQLite.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `nguoi` MODIFY COLUMN `maCCCD` VARCHAR(20) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không rollback để tránh mất dữ liệu
        // DB::statement('ALTER TABLE `nguoi` MODIFY COLUMN `maCCCD` INT NULL');
    }
};
