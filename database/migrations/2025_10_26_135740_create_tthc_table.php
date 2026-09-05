<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tthc', function (Blueprint $table) {
            $table->increments('maTTHC');
            $table->string('tenTTHC', 500);
            $table->integer('maLinhVuc')->unsigned();
            $table->integer('maQuayLamViec')->unsigned()->nullable();
            $table->text('trinhTuThucHien');
            $table->string('doiTuongThucHien');
            $table->string('coQuanThucHien');
            $table->enum('trangThai', ['Công khai', 'Chờ công khai', 'Bãi bỏ'])->nullable();
            $table->text('yeuCauDieuKien');
            $table->text('canCuPhapLy');
            $table->string('ketQuaThucHien', 500);

            // Khóa ngoại
            $table->foreign('maLinhVuc')
                ->references('maLinhVuc')
                ->on('linhvuc');

            $table->foreign('maQuayLamViec')
                ->references('maQuayLamViec')
                ->on('quaylamviec');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tthc');
    }
};
