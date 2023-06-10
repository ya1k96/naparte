<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenTransferenciaAccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_transferencia_accion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('orden_transferencia_id')->unsigned();
            $table->string('tipo');
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->comment('Usuario que realiza la accion');
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
        Schema::dropIfExists('orden_transferencia_accion');
    }
}
