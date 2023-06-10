<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnidadVinculacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidad_vinculacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unidad_id');
            $table->unsignedBigInteger('vinculacion_id');

            $table->foreign('unidad_id')->references('id')->on('unidades');
            $table->foreign('vinculacion_id')->references('id')->on('vinculaciones');

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
        Schema::dropIfExists('unidad_vinculacion');
    }
}
