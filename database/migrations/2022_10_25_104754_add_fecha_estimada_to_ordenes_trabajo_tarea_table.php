<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechaEstimadaToOrdenesTrabajoTareaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_trabajo_tarea', function (Blueprint $table) {
            $table->date('fecha_estimada')->nullable()->after('fecha_realizacion');
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
            $table->dropColumn('fecha_estimada');
        });
    }
}
