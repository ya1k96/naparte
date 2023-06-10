<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrdenTrabajoToPersonalTareaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personal_tarea', function (Blueprint $table) {
            $table->unsignedBigInteger('orden_trabajo_id')->nullable()->after('tarea_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal_tarea', function (Blueprint $table) {
            //
        });
    }
}
