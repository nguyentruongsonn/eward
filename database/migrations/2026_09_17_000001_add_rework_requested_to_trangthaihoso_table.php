<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! app()->runningUnitTests()) {
            DB::table('trangthaihoso')->updateOrInsert(
                ['maTrangThai' => 12],
                ['tenTrangThai' => 'Yêu cầu xử lý lại']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! app()->runningUnitTests()) {
            DB::table('trangthaihoso')->where('maTrangThai', 12)->delete();
        }
    }
};
