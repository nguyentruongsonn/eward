<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosoxuly_mail_history', function (Blueprint $table): void {
            $table->string('message_id')->nullable()->unique();
            $table->string('in_reply_to')->nullable()->index();
            $table->string('provider_uid')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('hosoxuly_mail_history', function (Blueprint $table): void {
            $table->dropUnique(['message_id']);
            $table->dropIndex(['in_reply_to']);
            $table->dropUnique(['provider_uid']);
            $table->dropColumn(['message_id', 'in_reply_to', 'provider_uid']);
        });
    }
};
