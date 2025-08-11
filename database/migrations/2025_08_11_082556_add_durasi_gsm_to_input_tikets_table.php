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
    Schema::table('input_tikets', function (Blueprint $table) {
        $table->string('durasi_GSM')->after('link_upGSM')->nullable();
    });
}

public function down(): void
{
    Schema::table('input_tikets', function (Blueprint $table) {
        $table->dropColumn('durasi_GSM');
    });
}
};