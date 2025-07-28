<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('stopclocks', function (Blueprint $table) {
            $table->integer('durasi')->default(0)->after('alasan'); // durasi dalam menit
        });
    }

    public function down()
    {
        Schema::table('stopclocks', function (Blueprint $table) {
            $table->dropColumn('durasi');
        });
    }
};
