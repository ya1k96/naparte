<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCerradoToValeDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vale_detalles', function (Blueprint $table) {
            $table->boolean('cerrado')->default(false)->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vale_detalles', function (Blueprint $table) {
            $table->dropColumn('cerrado');
        });
    }
}
