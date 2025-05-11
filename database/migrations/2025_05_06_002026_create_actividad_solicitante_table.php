<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadSolicitanteTable extends Migration
{
    public function up()
    {
        Schema::create('actividad_solicitante', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tramite_id')->unsigned();
            $table->bigInteger('actividad_id')->unsigned();
            $table->timestamps();

            $table->foreign('tramite_id')->references('id')->on('tramite')->onDelete('cascade');
            $table->foreign('actividad_id')->references('id')->on('actividad')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividad_solicitante');
    }
}