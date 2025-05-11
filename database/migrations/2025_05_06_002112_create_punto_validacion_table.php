<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuntoValidacionTable extends Migration
{
    public function up()
    {
        Schema::create('punto_validacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('documento_id')->unsigned();
            $table->string('descripcion', 255);
            $table->tinyInteger('cumplido')->default(0);
            $table->timestamps();

            $table->foreign('documento_id')->references('id')->on('documento')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('punto_validacion');
    }
}