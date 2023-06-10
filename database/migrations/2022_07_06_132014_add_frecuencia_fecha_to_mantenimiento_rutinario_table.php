<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFrecuenciaFechaToMantenimientoRutinarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mantenimiento_rutinario', function (Blueprint $table) {
            $table->dropColumn('frecuencia');
        });

        Schema::table('mantenimiento_rutinario', function (Blueprint $table) {
            $table->integer('frecuencia')->after('ult_mantenimiento_fecha')->nullable();
            $table->integer('frecuencia_dias')->after('frecuencia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mantenimiento_rutinario', function (Blueprint $table) {
            $table->dropIfExists('frecuencia_dias');
        });
    }
}
