<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('componente_id');
            $table->string('descripcion');
            $table->decimal('kilometros', 5,2);
            $table->string('frecuencia');
            $table->unsignedBigInteger('especialidad_id');
            $table->string('observaciones');
            $table->foreign('componente_id')->references('id')->on('componentes');
            $table->foreign('especialidad_id')->references('id')->on('especialidades');

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
        Schema::dropIfExists('tareas');
    }
}
