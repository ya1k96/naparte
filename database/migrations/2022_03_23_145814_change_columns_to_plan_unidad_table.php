<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsToPlanUnidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_unidad', function (Blueprint $table) {
            $table->integer('km_inicial')->charset(null)->collation(null)->change();;
            $table->integer('estimativo')->charset(null)->collation(null)->change();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_unidad', function (Blueprint $table) {
            //
        });
    }
}
