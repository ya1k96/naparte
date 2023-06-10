<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterOrdenCompraNewField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE orden_compra MODIFY COLUMN estado ENUM('abierta', 'aprobada', 'parcial', 'recibida', 'cerrada', 'cancelada') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE orden_compra MODIFY COLUMN estado ENUM('abierta', 'aprobada', 'parcial', 'cancelada') NOT NULL");
    }
}
