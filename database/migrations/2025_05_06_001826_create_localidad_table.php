<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalidadTable extends Migration
{
    public function up()
    {
        Schema::create('localidad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('municipio_id')->unsigned();
            $table->string('nombre', 370);
            $table->timestamps();

            $table->foreign('municipio_id')->references('id')->on('municipio')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('localidad');
    }
}