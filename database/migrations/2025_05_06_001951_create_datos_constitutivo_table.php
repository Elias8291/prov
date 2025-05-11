<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatosConstitutivoTable extends Migration
{
    public function up()
    {
        Schema::create('datos_constitutivo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('instrumento_notarial_id')->unsigned();
            $table->text('objeto_social');
            $table->timestamps();

            $table->foreign('instrumento_notarial_id')->references('id')->on('instrumento_notarial')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('datos_constitutivo');
    }
}