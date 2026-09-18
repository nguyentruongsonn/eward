<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->text('yKienXuLy')->nullable()->comment('Ý kiến xử lý của cán bộ');
            $table->text('fileYKien')->nullable()->comment('File đính kèm ý kiến xử lý (JSON)');
            $table->text('fileKetQua')->nullable()->comment('File kết quả xử lý (JSON)');
        });
    }

    public function down(): void
    {
        Schema::table('hosoxuly', function (Blueprint $table) {
            $table->dropColumn(['yKienXuLy', 'fileYKien', 'fileKetQua']);
        });
    }
};
