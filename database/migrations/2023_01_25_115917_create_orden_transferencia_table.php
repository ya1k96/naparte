<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenTransferenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_transferencia', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->dateTime('fecha_emision')->nullable();
            $table->integer('base_origen_id')->unsigned()->nullable();
            $table->integer('base_destino_id')->unsigned()->nullable();            
            $table->enum('estado', ['abierta', 'aprobada', 'parcial', 'recibida', 'cerrada', 'cancelada']);            
            $table->text('observacion')->nullable();
            $table->string('solicitado_nombre')->nullable();            
            $table->string('entregado_nombre')->nullable();            

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
        Schema::dropIfExists('orden_transferencia');
    }
}
