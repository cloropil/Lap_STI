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
        Schema::create('stopclocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_tiket_id')->constrained()->onDelete('cascade');
            $table->datetime('start_clock');
            $table->datetime('stop_clock')->nullable();
            $table->text('alasan')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stopclocks');
    }
};
