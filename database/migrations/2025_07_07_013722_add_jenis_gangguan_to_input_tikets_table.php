<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisGangguanToInputTiketsTable extends Migration
{
    public function up()
    {
        Schema::table('input_tikets', function (Blueprint $table) {
            $table->string('jenis_gangguan')->nullable()->after('lokasi_id');
        });
    }

    public function down()
    {
        Schema::table('input_tikets', function (Blueprint $table) {
            $table->dropColumn('jenis_gangguan');
        });
    }
}
