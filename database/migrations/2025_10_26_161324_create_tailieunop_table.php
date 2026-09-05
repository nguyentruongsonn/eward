<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tailieunop', function (Blueprint $table) {
            $table->increments('taiLieuID');
            $table->string('maHSXL');
            $table->integer('maGiayTo')->unsigned();
            $table->string('tenTep');
            $table->string('duongDan');
            $table->string('dinhDang');
            $table->string('kichThuoc');
            $table->datetime('ngayTai');

            $table->foreign('maHSXL')->references('maHSXL')->on('hosoxuly');
            $table->foreign('maGiayTo')->references('maGiayTo')->on('giayto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailieunop');
    }
};
