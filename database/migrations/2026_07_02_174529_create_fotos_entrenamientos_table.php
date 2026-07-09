<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos_entrenamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade');
            $table->string('ruta'); // Guarda el path del archivo: 'fotos_entrenamientos/imagen.jpg'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos_entrenamientos');
    }
};