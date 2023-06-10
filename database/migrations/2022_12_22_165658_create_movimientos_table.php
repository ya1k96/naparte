<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pieza_id');
            $table->unsignedBigInteger('base_operacion_id');
            $table->unsignedBigInteger('inventario_id');
            $table->dateTime('fecha');
            $table->integer('cantidad');
            $table->float('precio_unitario');
            $table->string('ubicacion');
            $table->unsignedBigInteger('orden_compra_id')->nullable()->default(null)->comment('Orden de Compra. Movimiento positivo');
            $table->unsignedBigInteger('orden_trabajo_id')->nullable()->default(null)->comment('OT y Vale. Movimiento negativo');
            $table->unsignedBigInteger('vale_id')->nullable()->default(null)->comment('OT y Vale. Movimiento negativo');
            $table->unsignedBigInteger('orden_transferencia_id')->nullable()->default(null)->comment('Orden de Transferencia. Movimiento negativo');
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->comment('Usuario que realiza el movimiento');
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
        Schema::dropIfExists('movimientos');
    }
}
