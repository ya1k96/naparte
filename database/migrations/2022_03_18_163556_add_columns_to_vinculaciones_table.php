<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToVinculacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vinculaciones', function (Blueprint $table) {
            $table->renameColumn('cantidad', 'km_inicial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vinculaciones', function (Blueprint $table) {
            $table->renameColumn('km_inicial', 'cantidad');
        });
    }
}
