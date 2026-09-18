<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quaylamviec', function (Blueprint $table) {
            $table->increments('maQuayLamViec');
            $table->string('tenQuayLamViec', 500);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quaylamviec');
    }
};
