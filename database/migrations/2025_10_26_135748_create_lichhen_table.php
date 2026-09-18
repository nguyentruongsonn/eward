<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lichhen', function (Blueprint $table) {
            $table->string('id')->primary(); // Dùng id tự tăng chuẩn Laravel
            $table->string('maLichHen')->unique(); // Mã định danh dạng LH_IDCD_yyyymmdd_rand
            $table->unsignedInteger('IDCD'); // Khóa ngoại công dân
            $table->unsignedInteger('maTTHC'); // Khóa ngoại thủ tục
            $table->unsignedInteger('maQuayLamViec')->nullable(); // Có thể chọn quầy sau
            $table->dateTime('thoiGianHen');
            $table->enum('trangThai', [
                'Đã đặt lịch',
                'Chờ đến',
                'Đang xử lý',
                'Hoàn thành',
                'Đã hủy',
                'Yêu cầu bổ sung giấy tờ',
                'Không đến',
            ])->default('Đã đặt lịch');

            // 👇 Thêm cho chức năng QR check-in
            $table->uuid('checkin_token')->unique(); // token QR duy nhất
            $table->dateTime('checkin_time')->nullable(); // thời gian check-in
            $table->integer('soThuTu')->nullable(); // số thứ tự tự động khi check-in

            // timestamps để lưu ngày tạo / cập nhật
            $table->timestamps();

            // Các khóa ngoại
            $table->foreign('IDCD')
                ->references('IDCD')->on('congdan')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('maTTHC')
                ->references('maTTHC')->on('tthc')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('maQuayLamViec')
                ->references('maQuayLamViec')->on('quaylamviec')
                ->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lichhen');
    }
};
