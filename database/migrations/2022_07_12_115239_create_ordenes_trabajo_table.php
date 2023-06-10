<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesTrabajoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unidad_id');
            $table->string('tarea_a_realizar');
            $table->unsignedBigInteger('especialidad_id');
            $table->unsignedBigInteger('personal_id');
            $table->string('tipo_orden');
            $table->unsignedBigInteger('base_operacion_id');
            $table->text('comentario_mecanico')->nullable();
            $table->dateTime('fecha_hora_recepcion')->nullable();
            $table->dateTime('fecha_hora_devolucion')->nullable();
            $table->time('hora_inicio_trabajo')->nullable();
            $table->time('hora_fin_trabajo')->nullable();
            $table->string('status');
            $table->text('revisado_por')->nullable();
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->string('url')->nullable();
            $table->boolean('impresa')->nullable();
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
        Schema::dropIfExists('ordenes_trabajo');
    }
}
