<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechaRealizacionToOrdenesTrabajoTareaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_trabajo_tarea', function (Blueprint $table) {
            $table->date('fecha_realizacion')->nullable()->after('comentario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_trabajo_tarea', function (Blueprint $table) {
            $table->dropColumn('fecha_realizacion');
        });
    }
}
