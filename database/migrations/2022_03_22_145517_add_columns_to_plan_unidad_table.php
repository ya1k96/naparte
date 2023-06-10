<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPlanUnidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_unidad', function (Blueprint $table) {
            $table->string('km_inicial')->after('unidad_id');
            $table->date('fecha')->after('km_inicial');
            $table->string('estimativo')->after('fecha');
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
            $table->dropColumn(['km_inicial', 'fecha', 'estimativo']);
        });
    }
}
