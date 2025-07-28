<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPenyebabNullableOnInputTiketsTable extends Migration
{
    public function up()
    {
        Schema::table('input_tikets', function (Blueprint $table) {
            $table->text('penyebab')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('input_tikets', function (Blueprint $table) {
            $table->text('penyebab')->nullable(false)->change();
        });
    }
}
