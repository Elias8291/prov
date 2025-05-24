<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_inhabiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('descripcion', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_inhabiles');
    }
};