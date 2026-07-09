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
        Schema::create('evaluaciones_psicologicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('psicologo_id')->constrained('users')->onDelete('cascade');
            
            // Campos clínicos (Acceso exclusivo Psicólogo/Admin)
            $table->text('observaciones_clinicas');
            $table->integer('puntaje_ansiedad')->default(0); // Escala 1-10
            $table->integer('puntaje_depresion')->default(0); // Escala 1-10
            $table->integer('puntaje_autoestima')->default(0); // Escala 1-10
            
            // Recomendaciones y Semáforo (Visibles por Entrenador/Padres según corresponda)
            $table->text('recomendaciones_entrenador');
            $table->text('recomendaciones_padres');
            $table->enum('semaforo_riesgo', ['verde', 'amarillo', 'rojo'])->default('verde');
            $table->boolean('alerta_coordinacion')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_psicologicas');
    }
};