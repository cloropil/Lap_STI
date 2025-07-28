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
        Schema::create('tiket_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_tiket_id')
                    ->constrained('input_tikets')
                    ->onDelete('cascade');

            $table->string('status');                    // contoh: "Dibuat", "Diproses", "Ditutup"
            $table->text('keterangan')->nullable();      // detail log opsional
            $table->timestamp('log_time')->nullable();   // waktu log opsional
            $table->timestamps();                        // created_at dan updated_at
        });
    }
};
