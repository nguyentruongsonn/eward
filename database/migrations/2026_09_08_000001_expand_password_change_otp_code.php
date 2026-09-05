<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_change_otps', function (Blueprint $table): void {
            $table->string('code', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('password_change_otps', function (Blueprint $table): void {
            $table->string('code', 10)->change();
        });
    }
};
