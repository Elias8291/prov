<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentanteLegalTable extends Migration
{
    public function up()
    {
        Schema::create('representante_legal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);
            $table->bigInteger('instrumento_notarial_id')->unsigned();
            $table->timestamps();

            $table->foreign('instrumento_notarial_id')->references('id')->on('instrumento_notarial')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('representante_legal');
    }
}