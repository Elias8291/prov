<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstrumentoNotarialTable extends Migration
{
    public function up()
    {
        Schema::create('instrumento_notarial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_escritura', 255);
            $table->date('fecha');
            $table->string('nombre_notario', 255);
            $table->string('numero_notario', 255);
            $table->bigInteger('estado_id')->unsigned();
            $table->string('registro_mercantil', 255);
            $table->date('fecha_registro');
            $table->timestamps();

            $table->foreign('estado_id')->references('id')->on('estado')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('instrumento_notarial');
    }
}