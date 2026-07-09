<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('autoevaluaciones_jugadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade');
            $table->tinyInteger('animo_entrenamiento');
            $table->tinyInteger('calidad_sueno');
            $table->boolean('siente_dolor')->default(false);
            $table->tinyInteger('diversion');
            $table->tinyInteger('cansancio_extremo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autoevaluaciones_jugadores');
    }
};