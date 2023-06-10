<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenTransferenciaDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_transferencia_detalle', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('orden_transferencia_id')->unsigned();
            $table->integer('piezas_id')->unsigned();
            $table->decimal('cantidad', 10, 2)->default(0);            

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
        Schema::dropIfExists('orden_transferencia_detalle');
    }
}
