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
        Schema::create('hosoxuly_mail_history', function (Blueprint $table) {
            $table->id();
            $table->string('maHSXL');
            $table->string('loai_mail'); // 'lien_lac' hoặc 'bo_sung'
            $table->string('subject');
            $table->text('content');
            $table->string('email'); // Email người nhận
            $table->timestamp('sent_at');
            $table->unsignedInteger('sent_by')->nullable(); // ID admin gửi (nếu cần)

            $table->foreign('maHSXL')->references('maHSXL')->on('hosoxuly')->onDelete('cascade');
            $table->index('maHSXL');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosoxuly_mail_history');
    }
};
