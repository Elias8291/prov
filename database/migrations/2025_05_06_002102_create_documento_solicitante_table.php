<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoSolicitanteTable extends Migration
{
    public function up()
    {
        Schema::create('documento_solicitante', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tramite_id')->unsigned();
            $table->bigInteger('documento_id')->unsigned();
            $table->date('fecha_entrega');
            $table->enum('estado', ['Pendiente', 'Recibido', 'Rechazado', 'En Revision', 'Aprobado']);
            $table->integer('version_documento')->default(1);
            $table->text('observaciones')->nullable();
            $table->string('ruta_archivo', 500); // Campo para la ruta del archivo subido
            $table->timestamps();

            $table->foreign('tramite_id')->references('id')->on('tramite')->onDelete('cascade');
            $table->foreign('documento_id')->references('id')->on('documento')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documento_solicitante');
    }
}