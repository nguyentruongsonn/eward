<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lichsuthanhtoan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('maGD')->unique();
            $table->string('soGD')->nullable();
            $table->string('loaiGD')->nullable();
            $table->dateTime('ngayGD')->nullable();
            $table->decimal('soTien', 14, 2)->nullable();
            $table->string('trangThai')->nullable();
            $table->unsignedInteger('IDCD');
            $table->string('maHSXL')->nullable();
            $table->text('moTa')->nullable();

            $table->foreign('IDCD')->references('IDCD')->on('congdan');
            $table->foreign('maHSXL')->references('maHSXL')->on('hosoxuly');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lichsuthanhtoan');
    }
};
