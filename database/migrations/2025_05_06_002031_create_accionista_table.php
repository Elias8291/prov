<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccionistaTable extends Migration
{
    public function up()
    {
        Schema::create('accionista', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('apellido_paterno', 255);
            $table->string('apellido_materno', 255);
            $table->string('nombre', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accionista');
    }
}