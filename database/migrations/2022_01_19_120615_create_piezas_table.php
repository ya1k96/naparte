<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePiezasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piezas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('nro_pieza');
            $table->string('descripcion');
            $table->unsignedBigInteger('unidad_medida_id');
            $table->unsignedBigInteger('base_operacion_id');
            $table->string('observacion')->nullable();

            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medidas');
            $table->foreign('base_operacion_id')->references('id')->on('bases_operaciones');

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
        Schema::dropIfExists('piezas');
    }
}
