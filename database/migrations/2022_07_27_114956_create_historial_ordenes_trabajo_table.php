<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistorialOrdenesTrabajoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_ordenes_trabajo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ordenes_trabajo_id');
            $table->string('status');
            $table->unsignedBigInteger('user_id');
            $table->datetime('fecha');
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
        Schema::dropIfExists('historial_ordenes_trabajo');
    }
}
