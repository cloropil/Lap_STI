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
        Schema::create('input_tikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_id')->constrained('lokasis')->onDelete('cascade');
            $table->string('no_tiket')->unique();
            $table->dateTime('open_tiket');
            $table->dateTime('stopclock')->nullable();
            $table->dateTime('link_up')->nullable();
            $table->string('durasi')->nullable();
            $table->text('penyebab');
            $table->text('action')->nullable();
            $table->string('status_koneksi')->nullable();
            $table->text('status_tiket')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_tikets_tabel');
    }
};
