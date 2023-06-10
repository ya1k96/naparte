<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechaInicioPeriodoToOrdenesTrabajo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {
            $table->date('fecha_inicio_periodo')->nullable()->after('numeracion');
            $table->date('fecha_fin_periodo')->nullable()->after('numeracion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {
            //
        });
    }
}
