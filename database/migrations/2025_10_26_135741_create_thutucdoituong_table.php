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
        Schema::create('thutucdoituong', function (Blueprint $table) {
            $table->unsignedInteger('maTTHC');
            $table->unsignedInteger('maDoiTuong');

            $table->foreign('maTTHC')->references('maTTHC')->on('tthc')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('maDoiTuong')->references('maDoiTuong')->on('doituongthuchien')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thutucdoituong');
    }
};
