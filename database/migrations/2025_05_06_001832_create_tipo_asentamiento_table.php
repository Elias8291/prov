<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoAsentamientoTable extends Migration
{
    public function up()
    {
        Schema::create('tipo_asentamiento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 300);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_asentamiento');
    }
}