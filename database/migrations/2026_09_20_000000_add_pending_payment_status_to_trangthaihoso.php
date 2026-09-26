<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('trangthaihoso')->updateOrInsert(
            ['maTrangThai' => 13],
            ['tenTrangThai' => 'Chờ thanh toán'],
        );

        DB::table('hosoxuly')
            ->where('maTrangThai', 1)
            ->where('lePhi', '>', 0)
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('lichsuthanhtoan')
                    ->whereColumn('lichsuthanhtoan.maHSXL', 'hosoxuly.maHSXL')
                    ->where('lichsuthanhtoan.trangThai', 'Thành công');
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('payment_intents')
                    ->whereColumn('payment_intents.maHSXL', 'hosoxuly.maHSXL')
                    ->where('payment_intents.status', 'paid');
            })
            ->update(['maTrangThai' => 13]);
    }

    public function down(): void
    {
        DB::table('hosoxuly')
            ->where('maTrangThai', 13)
            ->update(['maTrangThai' => 1]);

        DB::table('trangthaihoso')->where('maTrangThai', 13)->delete();
    }
};
