<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoso_opinion_files', function (Blueprint $table): void {
            $table->id();
            $table->string('maHSXL');
            $table->string('original_name', 180);
            $table->string('path', 500);
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('maHSXL')->references('maHSXL')->on('hosoxuly')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('IDnguoiDung')->on('nguoi')->nullOnDelete();
            $table->index(['maHSXL', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoso_opinion_files');
    }
};
