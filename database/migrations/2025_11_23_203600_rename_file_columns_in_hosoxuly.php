<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('fileYKien', 'duongdanfileykien');
            $table->renameColumn('fileKetQua', 'duongdanfileketqua');
        });
    }

    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->renameColumn('duongdanfileykien', 'fileYKien');
            $table->renameColumn('duongdanfileketqua', 'fileKetQua');
        });
    }
};
