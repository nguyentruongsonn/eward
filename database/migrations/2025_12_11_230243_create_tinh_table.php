<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tinh', function (Blueprint $table) {
            $table->increments('maTinh');           // Khóa chính tự tăng
            $table->string('tenTinh');
            $table->string('tenTinhKhongDau')->nullable(); // Tiện cho tìm kiếm, SEO  // Ví dụ: MB, MT, MN...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tinh');
    }
};
