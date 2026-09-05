<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('canbo', function (Blueprint $table) {
            if (! Schema::hasColumn('canbo', 'chucVu')) {
                $table->string('chucVu', 100)->nullable()->after('maQuayLamViec');
            }
        });
    }

    public function down(): void
    {
        Schema::table('canbo', function (Blueprint $table) {
            if (Schema::hasColumn('canbo', 'chucVu')) {
                $table->dropColumn('chucVu');
            }
        });
    }
};
