<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecursosActividadesValesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos_actividades_vales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vale_id');
            $table->unsignedBigInteger('recursos_actividad_id');
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
        Schema::dropIfExists('recursos_actividades_vales');
    }
}
