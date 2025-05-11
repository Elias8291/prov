<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadTable extends Migration
{
    public function up()
    {
        Schema::create('actividad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);
            $table->bigInteger('sector_id')->unsigned();
            $table->timestamps();

            $table->foreign('sector_id')->references('id')->on('sector')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividad');
    }
}