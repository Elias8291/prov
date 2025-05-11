<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionUsuarioTable extends Migration
{
    public function up()
    {
        Schema::create('notificacion_usuario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('usuario_id')->unsigned();
            $table->bigInteger('notificacion_id')->unsigned();
            $table->timestamp('fecha_notificacion');
            $table->enum('estado', ['Pendiente', 'Leido', 'Eliminado'])->default('Pendiente');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('notificacion_id')->references('id')->on('notificacion')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacion_usuario');
    }
}