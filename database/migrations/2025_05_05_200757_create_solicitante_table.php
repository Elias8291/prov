<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitanteTable extends Migration
{
    public function up()
    {
        Schema::create('solicitante', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->enum('tipo_persona', ['Física', 'Moral']);
            $table->string('curp', 255)->nullable();
            $table->string('rfc', 255);
            $table->text('objeto_social')->nullable(); // Added objeto_social field
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitante');
    }
}