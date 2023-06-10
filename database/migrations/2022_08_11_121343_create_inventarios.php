<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('bases_operacion_id');
            $table->unsignedBigInteger('pieza_id');
            $table->boolean('compra_unica')->default(false);
            $table->integer('stock');
            $table->decimal('precio', 7,2);
            $table->string('ubicacion');
            $table->integer('maximo_compra')->nullable();
            $table->integer('minimo_compra')->nullable();
            

            $table->foreign('bases_operacion_id')->references('id')->on('bases_operaciones')->onDelete('cascade');
            $table->foreign('pieza_id')->references('id')->on('piezas')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventarios');
    }
}
