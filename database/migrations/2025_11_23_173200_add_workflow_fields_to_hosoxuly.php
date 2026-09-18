<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->integer('nguoiTiepNhan')->unsigned()->nullable()->comment('ID người tiếp nhận hồ sơ');
            $table->integer('nguoiDuyet')->unsigned()->nullable()->comment('ID lãnh đạo phê duyệt');
            $table->dateTime('ngayDuyet')->nullable()->comment('Ngày lãnh đạo phê duyệt');
            $table->text('yKienDuyet')->nullable()->comment('Ý kiến phê duyệt của lãnh đạo');
        });
    }

    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->dropColumn(['nguoiTiepNhan', 'nguoiDuyet', 'ngayDuyet', 'yKienDuyet']);
        });
    }
};
