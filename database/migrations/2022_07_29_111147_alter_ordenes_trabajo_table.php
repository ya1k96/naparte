<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrdenesTrabajoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {
            $table->bigInteger('kilometraje')->after('observaciones')->nullable();
            $table->unsignedBigInteger('historial_unidad_id')->after('kilometraje')->nullable();
            $table->datetime('fecha_hora_inicio')->after('hora_inicio_trabajo')->nullable();
            $table->datetime('fecha_hora_fin')->after('hora_fin_trabajo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
