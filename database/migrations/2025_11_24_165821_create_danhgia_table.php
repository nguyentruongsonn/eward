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
        Schema::create('danhgia', function (Blueprint $table) {
            $table->id();
            $table->string('maHSXL');
            $table->integer('soDiem'); // 1-5 stars
            $table->text('nhanXet')->nullable();
            $table->integer('IDCD'); // Citizen ID
            $table->timestamp('ngayDanhGia');
            $table->timestamps();

            $table->foreign('maHSXL')->references('maHSXL')->on('hosoxuly')->onDelete('cascade');
            $table->unique('maHSXL'); // One rating per application
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danhgia');
    }
};
