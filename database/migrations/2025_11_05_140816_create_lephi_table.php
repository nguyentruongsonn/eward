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
        Schema::create('lephi', function (Blueprint $table) {
            $table->increments('maLePhi');
            $table->string('loaiLePhi');
            $table->integer('maTTHC')->unsigned();
            $table->decimal('soTien', 15, 2);
            $table->string('batBuoc')->nullable();
            $table->string('moTa')->nullable();
            $table->foreign('maTTHC')->references('maTTHC')->on('tthc');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lephi');
    }
};
