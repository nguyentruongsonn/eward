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
        Schema::table('hosoxuly_mail_history', function (Blueprint $table) {
            $table->string('direction')->default('outgoing')->after('maHSXL'); // 'outgoing' hoặc 'incoming'
            $table->string('sender_type')->default('admin')->after('direction'); // 'admin' hoặc 'citizen'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosoxuly_mail_history', function (Blueprint $table) {
            $table->dropColumn(['direction', 'sender_type']);
        });
    }
};
