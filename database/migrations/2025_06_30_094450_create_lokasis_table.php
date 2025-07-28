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
        Schema::create('lokasis', function (Blueprint $table) {
            $table->id();
            $table->string('no')->unique();       // contoh: 001, 002
            $table->string('lokasi');             // nama unit/lokasi
            $table->string('sid')->unique();      // nomor SID

            // ✅ Tambahan kolom baru
            $table->string('product')->nullable();                  // Nama produk/layanan
            $table->string('bandwith')->nullable();                 // Kapasitas bandwith
            $table->string('kategori_layanan')->nullable();         // Dedicated / Broadband / dll
            $table->integer('jumlah_gangguan')->default(0);         // Jumlah gangguan terjadi
            $table->float('standard_availability')->default(99.5);  // SLA target
            $table->float('realisasi_availability')->default(100);  // SLA realisasi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasis');
    }
};
