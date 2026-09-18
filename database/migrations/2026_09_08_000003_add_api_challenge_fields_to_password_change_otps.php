<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_change_otps', function (Blueprint $table): void {
            $table->uuid('challenge_id')->nullable()->unique();
            $table->unsignedInteger('nguoi_dung_id')->nullable()->index();
            $table->foreign('nguoi_dung_id', 'password_change_otps_nguoi_dung_id_foreign')
                ->references('IDnguoiDung')
                ->on('nguoi')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('password_change_otps', function (Blueprint $table): void {
            $table->dropForeign('password_change_otps_nguoi_dung_id_foreign');
            $table->dropIndex(['nguoi_dung_id']);
            $table->dropUnique(['challenge_id']);
            $table->dropColumn(['challenge_id', 'nguoi_dung_id']);
        });
    }
};
