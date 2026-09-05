<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi', function (Blueprint $table): void {
            $table->index('email', 'nguoi_email_index');
            $table->index('vaiTro', 'nguoi_role_index');
        });

        Schema::table('hosoxuly', function (Blueprint $table): void {
            $table->index(['IDCD', 'maTrangThai'], 'hosoxuly_citizen_status_index');
            $table->index(['maTTHC', 'ngayTiepNhan'], 'hosoxuly_procedure_received_index');
        });

        Schema::table('lichhen', function (Blueprint $table): void {
            $table->index(['IDCD', 'thoiGianHen', 'trangThai'], 'lichhen_citizen_time_status_index');
            $table->index(['maTTHC', 'thoiGianHen', 'trangThai'], 'lichhen_procedure_time_status_index');
        });

        Schema::table('lichsuthanhtoan', function (Blueprint $table): void {
            $table->index(['maHSXL', 'trangThai'], 'lichsuthanhtoan_application_status_index');
            $table->index(['IDCD', 'ngayGD'], 'lichsuthanhtoan_citizen_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('lichsuthanhtoan', function (Blueprint $table): void {
            $table->dropIndex('lichsuthanhtoan_application_status_index');
            $table->dropIndex('lichsuthanhtoan_citizen_date_index');
        });

        Schema::table('lichhen', function (Blueprint $table): void {
            $table->dropIndex('lichhen_citizen_time_status_index');
            $table->dropIndex('lichhen_procedure_time_status_index');
        });

        Schema::table('hosoxuly', function (Blueprint $table): void {
            $table->dropIndex('hosoxuly_citizen_status_index');
            $table->dropIndex('hosoxuly_procedure_received_index');
        });

        Schema::table('nguoi', function (Blueprint $table): void {
            $table->dropIndex('nguoi_email_index');
            $table->dropIndex('nguoi_role_index');
        });
    }
};
