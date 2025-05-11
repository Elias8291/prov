<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactoSolicitanteTable extends Migration
{
    public function up()
    {
        Schema::create('contacto_solicitante', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);
            $table->string('puesto', 255);
            $table->string('telefono', 255);
            $table->string('email', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacto_solicitante');
    }
}