<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAnuladaToOrdenCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orden_compra', function (Blueprint $table) {
            DB::statement("ALTER TABLE orden_compra MODIFY COLUMN estado ENUM('abierta', 'aprobada', 'parcial', 'recibida', 'cerrada', 'anulada') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orden_compra', function (Blueprint $table) {
            DB::statement("ALTER TABLE orden_compra MODIFY COLUMN estado ENUM('abierta', 'aprobada', 'parcial', 'recibida', 'cerrada', 'cancelada') NOT NULL");
        });
    }
}