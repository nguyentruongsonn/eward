<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi', function (Blueprint $table) {
            $table->increments('IDnguoiDung'); // INT UNSIGNED AUTO_INCREMENT
            $table->string('maCCCD')->nullable();
            $table->string('hoTen'); // VARCHAR(255)
            $table->enum('gioiTinh', ['Nam', 'Nữ'])->nullable();
            $table->date('ngaySinh')->nullable();
            $table->string('queQuan', 500)->nullable();
            $table->string('noiThuongTru', 500)->nullable();
            $table->string('noiTamTru', 500)->nullable();
            $table->string('email', 255);
            $table->string('password')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('soDienThoai', 10);
            $table->enum('vaiTro', ['Công dân/ Tổ chức', 'Cán bộ một cửa', 'Cán bộ thụ lý', 'Lãnh đạo', 'Quản trị viên', 'Checkin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi');
    }
};
