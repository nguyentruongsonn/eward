<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canbo', function (Blueprint $table) {
            $table->increments('IDCB');
            $table->integer('IDnguoiDung')->unsigned();
            $table->integer('maQuayLamViec')->unsigned()->nullable();

            // Khóa ngoại
            $table->foreign('IDnguoiDung')
                ->references('IDNguoiDung')
                ->on('nguoi')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('maQuayLamViec')
                ->references('maQuayLamViec')
                ->on('quaylamviec');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canbo');
    }
};
