<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanhphanhoso', function (Blueprint $table) {
            $table->increments('maThanhPhan');
            $table->integer('maTTHC')->unsigned();
            $table->string('tenThanhPhan', 500);

            // Khóa ngoại
            $table->foreign('maTTHC')
                ->references('maTTHC')
                ->on('tthc');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanhphanhoso');
    }
};
