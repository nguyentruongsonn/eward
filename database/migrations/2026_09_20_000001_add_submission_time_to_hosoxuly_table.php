<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table): void {
            $table->timestamp('ngayNop')->nullable()->after('dulieu');
            $table->index('ngayNop', 'hosoxuly_submission_time_index');
        });
    }

    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table): void {
            $table->dropIndex('hosoxuly_submission_time_index');
            $table->dropColumn('ngayNop');
        });
    }
};
