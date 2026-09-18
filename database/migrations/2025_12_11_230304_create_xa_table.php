<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xa', function (Blueprint $table) {
            $table->increments('maXa');
            $table->string('tenXa');
            $table->unsignedInteger('maTinh');      // Khóa ngoại

            $table->timestamps();

            // Khóa ngoại + cascade delete (xóa tỉnh thì xóa luôn các xã thuộc tỉnh đó)
            $table->foreign('maTinh')
                ->references('maTinh')
                ->on('tinh')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xa');
    }
};
