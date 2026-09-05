<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_intents', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->unsignedInteger('IDCD');
            $table->string('maHSXL');
            $table->string('provider', 30);
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('VND');
            $table->string('status', 20)->default('pending');
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('IDCD')->references('IDCD')->on('congdan')->cascadeOnDelete();
            $table->foreign('maHSXL')->references('maHSXL')->on('hosoxuly')->cascadeOnDelete();
            $table->index(['IDCD', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};
