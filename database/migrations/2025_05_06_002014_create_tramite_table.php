<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTramiteTable extends Migration
{
    public function up()
    {
        Schema::create('tramite', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('solicitante_id')->unsigned()->nullable();
            $table->enum('tipo_tramite', ['Inscripcion', 'Renovacion', 'Actualizacion']);
            $table->enum('estado', ['Pendiente', 'En Revision', 'Aprobado', 'Rechazado', 'Por Cotejar'])->default('Pendiente');
            $table->tinyInteger('progreso_tramite')->default(0);
            $table->bigInteger('revisado_por')->unsigned()->nullable();
            $table->timestamp('fecha_revision')->nullable();
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_finalizacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('solicitante_id')->references('id')->on('solicitante')->onDelete('set null');
            $table->foreign('revisado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tramite');
    }
}