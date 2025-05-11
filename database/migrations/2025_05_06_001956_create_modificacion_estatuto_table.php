<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModificacionEstatutoTable extends Migration
{
    public function up()
    {
        Schema::create('modificacion_estatuto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dato_constitutivo_id')->unsigned();
            $table->bigInteger('instrumento_notarial_id')->unsigned();
            $table->timestamps();

            $table->foreign('dato_constitutivo_id')->references('id')->on('datos_constitutivo')->onDelete('cascade');
            $table->foreign('instrumento_notarial_id')->references('id')->on('instrumento_notarial')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('modificacion_estatuto');
    }
}