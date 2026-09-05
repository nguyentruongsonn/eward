<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('giayto', function (Blueprint $table) {
            $table->increments('maGiayTo');
            $table->string('tenGiayTo', 2000);
            $table->enum('loaiGiayTo', [
                'Tài liệu',
                'Mẫu đơn',
                'Tờ khai',
                'Giấy tờ cá nhân',
                'Biên bản xác nhận',
                'Quyết định',
                'Giấy chứng nhận',
                'Bản ghi',
                'Khác',
            ]);
            $table->enum('yeuCau', ['Bắt buộc', 'Không bắt buộc']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giayto');
    }
};
