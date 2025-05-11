<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedorTable extends Migration
{
    public function up()
    {
        Schema::create('proveedor', function (Blueprint $table) {
            $table->string('pv', 10)->primary();
            $table->bigInteger('solicitante_id')->unsigned();
            $table->date('fecha_registro');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['Activo', 'Inactivo', 'Pendiente Renovacion']);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('solicitante_id')->references('id')->on('solicitante')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proveedor');
    }
}