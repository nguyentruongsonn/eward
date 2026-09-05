<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table): void {
            $table->index(['ngayHenTra', 'maTrangThai'], 'hosoxuly_due_status_index');
            $table->index(['maTrangThai', 'ngayTiepNhan'], 'hosoxuly_status_received_index');
            $table->index('ngayTiepNhan', 'hosoxuly_received_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table): void {
            $table->dropIndex('hosoxuly_due_status_index');
            $table->dropIndex('hosoxuly_status_received_index');
            $table->dropIndex('hosoxuly_received_date_index');
        });
    }
};
