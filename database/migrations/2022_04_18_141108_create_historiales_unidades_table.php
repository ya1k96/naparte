<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistorialesUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historiales_unidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unidad_id');
            $table->bigInteger('kilometraje');

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
        Schema::dropIfExists('historiales_unidades');
    }
}
