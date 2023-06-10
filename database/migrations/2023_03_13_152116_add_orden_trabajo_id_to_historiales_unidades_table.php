<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrdenTrabajoIdToHistorialesUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historiales_unidades', function (Blueprint $table) {
            $table->unsignedBigInteger('orden_trabajo_id')->nullable()->after('kilometraje');
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
            $table->dropColumn('orden_trabajo_id');
        });
    }
}
