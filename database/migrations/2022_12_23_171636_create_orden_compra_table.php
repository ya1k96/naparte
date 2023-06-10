<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_compra', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('proveedor_id')->unsigned()->nullable();
            $table->integer('base_emite_id')->unsigned()->nullable();
            $table->integer('base_recibe_id')->unsigned()->nullable();            
            $table->enum('prioridad', ['baja', 'normal', 'alta']);
            $table->enum('estado', ['abierta', 'aprobada', 'cancelada']);
            $table->text('observacion')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_entrega')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_compra');
    }
}
