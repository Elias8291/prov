<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsentamientoTable extends Migration
{
    public function up()
    {
        Schema::create('asentamiento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 405);
            $table->string('codigo_postal', 5);
            $table->bigInteger('localidad_id')->unsigned();
            $table->bigInteger('tipo_asentamiento_id')->unsigned();
            $table->timestamps();

            $table->foreign('localidad_id')->references('id')->on('localidad')->onDelete('cascade');
            $table->foreign('tipo_asentamiento_id')->references('id')->on('tipo_asentamiento')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asentamiento');
    }
}