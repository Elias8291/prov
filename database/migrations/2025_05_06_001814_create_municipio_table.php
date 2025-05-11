<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMunicipioTable extends Migration
{
    public function up()
    {
        Schema::create('municipio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('estado_id')->unsigned();
            $table->string('nombre', 370);
            $table->timestamps();

            $table->foreign('estado_id')->references('id')->on('estado')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('municipio');
    }
}