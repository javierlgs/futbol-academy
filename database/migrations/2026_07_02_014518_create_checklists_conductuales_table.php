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
        Schema::create('checklists_conductuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('entrenador_id')->constrained('users')->onDelete('cascade');
            
            // Los 3 indicadores críticos del documento
            $table->boolean('aislamiento_social')->default(false);
            $table->boolean('frustracion_extrema')->default(false);
            $table->boolean('agresividad')->default(false);
            
            // Estado de derivación automática
            $table->boolean('derivado_automatico')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists_conductuales');
    }
};