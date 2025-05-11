<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionTable extends Migration
{
    public function up()
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo', 255);
            $table->text('mensaje');
            $table->timestamp('fecha');
            $table->enum('tipo', ['Informativo', 'Advertencia', 'Error']);
            $table->enum('estado', ['Pendiente', 'Leido', 'Eliminado'])->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacion');
    }
}