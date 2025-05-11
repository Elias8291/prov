<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleTramiteTable extends Migration
{
    public function up()
    {
        Schema::create('detalle_tramite', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tramite_id');
            $table->string('razon_social', 255);
            $table->string('email', 255)->nullable(); 
            $table->string('telefono', 255)->nullable();
            $table->unsignedBigInteger('direccion_id')->nullable();
            $table->unsignedBigInteger('contacto_id')->nullable();
            $table->unsignedBigInteger('representante_legal_id')->nullable();
            $table->unsignedBigInteger('dato_constitutivo_id')->nullable();
            $table->string('sitio_web', 255)->nullable();
            $table->timestamps();

            $table->foreign('tramite_id')->references('id')->on('tramite')->onDelete('cascade');
            $table->foreign('direccion_id')->references('id')->on('direccion')->onDelete('set null');
            $table->foreign('contacto_id')->references('id')->on('contacto_solicitante')->onDelete('set null');
            $table->foreign('representante_legal_id')->references('id')->on('representante_legal')->onDelete('set null');
            $table->foreign('dato_constitutivo_id')->references('id')->on('datos_constitutivo')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalle_tramite');
    }
}