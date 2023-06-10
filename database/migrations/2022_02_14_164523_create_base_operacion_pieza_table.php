<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseOperacionPiezaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_operacion_pieza', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('base_operacion_id');
            $table->unsignedBigInteger('pieza_id');

            $table->foreign('base_operacion_id')->references('id')->on('bases_operaciones')->onDelelete('cascade');
            $table->foreign('pieza_id')->references('id')->on('piezas')->onDelelete('cascade');

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
        Schema::dropIfExists('base_operacion_pieza');
    }
}
