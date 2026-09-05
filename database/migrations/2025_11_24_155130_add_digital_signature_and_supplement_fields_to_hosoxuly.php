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
            // JSON field to store file signatures
            $table->json('file_signatures')->nullable()->after('duongdanfileketqua');

            // Text field for document supplement requests
            $table->text('yeu_cau_bo_sung')->nullable()->after('file_signatures');

            // Backup status before supplement request
            $table->integer('maTrangThai_backup')->nullable()->after('yeu_cau_bo_sung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->dropColumn(['file_signatures', 'yeu_cau_bo_sung', 'maTrangThai_backup']);
        });
    }
};
