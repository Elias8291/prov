<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cita', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('solicitante_id');
            $table->unsignedBigInteger('tramite_id');
            $table->date('fecha_cita');
            $table->time('hora_cita');
            $table->enum('estado', ['Pendiente', 'Confirmada', 'Cancelada', 'Completada'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->foreign('solicitante_id')->references('id')->on('solicitante')->onDelete('cascade');
            $table->foreign('tramite_id')->references('id')->on('tramite')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita');
    }
};