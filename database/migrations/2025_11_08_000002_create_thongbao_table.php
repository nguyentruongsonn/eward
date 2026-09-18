<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thongbao', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('IDCD');
            $table->string('tieuDe', 255);
            $table->text('noiDung')->nullable();
            $table->string('loai', 100)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('IDCD')->references('IDCD')->on('congdan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thongbao');
    }
};
