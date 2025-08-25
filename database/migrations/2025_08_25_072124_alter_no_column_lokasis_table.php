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
        // Ubah kolom 'no' dari string ke integer
        Schema::table('lokasis', function (Blueprint $table) {
            $table->integer('no')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom 'no' ke string jika rollback
        Schema::table('lokasis', function (Blueprint $table) {
            $table->string('no')->change();
        });
    }
};
