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
        Schema::create('cachthuchien', function (Blueprint $table) {
            $table->increments('maCTH');
            $table->integer('maTTHC')->unsigned();
            $table->string('kenh'); // 1: trực tiếp, 2: trực tuyến, 3: bưu chính
            $table->text('thoiHanGiaiQuyet')->nullable();
            $table->text('moTaPhiLePhi')->nullable();
            $table->integer('thoiHan');
            $table->text('moTa')->nullable();

            $table->foreign('maTTHC')->references('maTTHC')->on('tthc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cachthuchien');
    }
};
