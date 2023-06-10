<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecursoActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_actividades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tarea_id');
            $table->unsignedBigInteger('pieza_id');
            $table->unsignedBigInteger('unidad_id');
            $table->integer('cantidad');

            $table->foreign('tarea_id')->references('id')->on('tareas');
            $table->foreign('pieza_id')->references('id')->on('piezas');
            $table->foreign('unidad_id')->references('id')->on('unidades');
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
        Schema::dropIfExists('recursos_actividades');
    }
}
