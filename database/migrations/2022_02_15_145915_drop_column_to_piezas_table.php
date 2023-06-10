<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnToPiezasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('piezas', function (Blueprint $table) {
            $table->dropForeign(['base_operacion_id']);
            $table->dropColumn('base_operacion_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('piezas', function (Blueprint $table) {
            $table->unsignedBigInteger('base_operacion_id')->nullable();

            $table->foreign('base_operacion_id')->references('id')->on('bases_operaciones');
        });
    }
}
