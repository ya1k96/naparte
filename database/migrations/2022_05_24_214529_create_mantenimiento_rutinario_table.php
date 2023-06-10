<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMantenimientoRutinarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mantenimiento_rutinario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unidad_id');
            $table->unsignedBigInteger('componente_id');
            $table->unsignedBigInteger('tarea_id');
            $table->integer('ult_mantenimiento')->nullable();
            $table->date('ult_mantenimiento_fecha')->nullable();
            $table->integer('frecuencia');
            $table->integer('prox_mantenimiento')->nullable();
            $table->date('prox_mantenimiento_fecha')->nullable();
            $table->integer('mantenimiento_modif')->nullable();
            $table->date('mantenimiento_modif_fecha')->nullable();
            $table->enum('estado', ['Normal', 'Pospuesta', 'Adelantada'])->default('Normal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mantenimiento_rutinario');
    }
}
