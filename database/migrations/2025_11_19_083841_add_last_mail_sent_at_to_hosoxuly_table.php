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
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->timestamp('last_mail_sent_at')->nullable()->after('ghiChu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->dropColumn('last_mail_sent_at');
        });
    }
};
