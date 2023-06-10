<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeToHistorialesUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historiales_unidades', function (Blueprint $table) {
            $table->dropForeign('historiales_unidades_unidad_id_foreign');

            $table->foreign('unidad_id')->references('id')->on('unidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historiales_unidades', function (Blueprint $table) {
            $table->dropForeign('historiales_unidades_unidad_id_foreign');

            $table->foreign('unidad_id')->references('id')->on('unidades');
        });
    }
}
