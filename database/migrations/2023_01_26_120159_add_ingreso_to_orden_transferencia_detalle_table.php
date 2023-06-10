<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIngresoToOrdenTransferenciaDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orden_transferencia_detalle', function (Blueprint $table) {
            $table->decimal('ingreso', 10, 2)->nullable()->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orden_transferencia_detalle', function (Blueprint $table) {
            $table->dropColumn('ingreso');
        });
    }
}
