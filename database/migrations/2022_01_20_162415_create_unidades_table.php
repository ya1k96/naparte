<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo_unidad');
            $table->integer('num_interno');
            $table->integer('modelo_id');
            $table->string('num_serie');
            $table->string('num_motor');
            $table->string('dominio');
            $table->integer('carroceria_id');
            $table->integer('cantidad_asientos');
            $table->integer('aire_acondicionado_id');
            $table->date('puesta_servicio')->nullable();
            $table->integer('tipo_vehiculo_id');
            $table->string('motor');
            $table->integer('base_operacion_id');
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('unidades');
    }
}
