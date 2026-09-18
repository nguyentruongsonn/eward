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
        Schema::create('thanhphangiayto', function (Blueprint $table) {
            $table->increments('maTPGT');
            $table->integer('maThanhPhan')->unsigned();
            $table->integer('maGiayTo')->unsigned();
            $table->integer('soLuongBanChinh');
            $table->integer('soLuongBanSao');

            $table->foreign('maThanhPhan')->references('maThanhPhan')->on('thanhphanhoso')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('maGiayTo')->references('maGiayTo')->on('giayto')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanhphangiayto');
    }
};
