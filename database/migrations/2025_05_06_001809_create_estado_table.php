<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoTable extends Migration
{
    public function up()
    {
        Schema::create('estado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pais_id')->unsigned();
            $table->string('nombre', 255);
            $table->timestamps();

            $table->foreign('pais_id')->references('id')->on('pais')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('estado');
    }
}