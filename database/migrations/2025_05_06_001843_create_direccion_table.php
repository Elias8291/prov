<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDireccionTable extends Migration
{
    public function up()
    {
        Schema::create('direccion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('codigo_postal');
            $table->bigInteger('asentamiento_id')->unsigned()->nullable();
            $table->string('calle', 255)->nullable();
            $table->string('numero_exterior', 255)->nullable();
            $table->string('numero_interior', 255)->nullable();
            $table->string('entre_calle_1', 255)->nullable();
            $table->string('entre_calle_2', 255)->nullable();
            $table->timestamps();

            $table->foreign('asentamiento_id')->references('id')->on('asentamiento')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('direccion');
    }
}