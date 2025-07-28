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
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('id');
            $table->text('apa')->nullable()->after('kegiatan');
            $table->text('siapa')->nullable()->after('apa');
            $table->text('kapan')->nullable()->after('siapa');
            $table->text('dimana')->nullable()->after('kapan');
            $table->text('mengapa')->nullable()->after('dimana');
            $table->text('bagaimana')->nullable()->after('mengapa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->dropColumn([
                'judul',
                'apa',
                'siapa',
                'kapan',
                'dimana',
                'mengapa',
                'bagaimana',
            ]);
        });
    }
};
