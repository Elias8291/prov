<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoTable extends Migration
{
     public function up()
    {
        Schema::create('documento', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);
            $table->enum('tipo', ['Certificado', 'Copia', 'Formulario', 'Carta', 'Comprobante', 'Acta', 'Otro']);
            $table->text('descripcion');
            $table->date('fecha_expiracion');
            $table->boolean('es_visible')->default(true); // Campo para visibilidad (true = visible, false = no visible)
            $table->enum('tipo_persona', ['Física', 'Moral', 'Ambas']); // Campo para tipo de persona, incluye 'Ambas'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento');
    }
}