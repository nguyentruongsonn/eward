<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoso_workflow_events', function (Blueprint $table) {
            $table->id();
            $table->string('maHSXL', 100);
            $table->string('event_type', 50);
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_name', 255)->nullable();
            $table->string('actor_role', 100)->nullable();
            $table->integer('from_status')->nullable();
            $table->integer('to_status')->nullable();
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['maHSXL', 'created_at']);
            $table->index('event_type');
            $table->index('actor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoso_workflow_events');
    }
};
