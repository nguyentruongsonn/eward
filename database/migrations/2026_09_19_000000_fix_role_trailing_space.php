<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE nguoi MODIFY COLUMN vaiTro VARCHAR(50) NOT NULL');
            DB::statement('UPDATE nguoi SET vaiTro = TRIM(vaiTro)');
            DB::statement("ALTER TABLE nguoi MODIFY COLUMN vaiTro ENUM('Công dân/ Tổ chức', 'Cán bộ một cửa', 'Cán bộ thụ lý', 'Lãnh đạo', 'Quản trị viên', 'Checkin') NOT NULL");
        } else {
            DB::table('nguoi')->where('vaiTro', 'Cán bộ thụ lý ')->update(['vaiTro' => 'Cán bộ thụ lý']);
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE nguoi MODIFY COLUMN vaiTro ENUM('Công dân/ Tổ chức', 'Cán bộ một cửa', 'Cán bộ thụ lý ', 'Lãnh đạo', 'Quản trị viên', 'Checkin') NOT NULL");
        }
    }
};
