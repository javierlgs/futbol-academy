<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('estadisticas_partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->boolean('convocado')->default(false);
            $table->integer('goles')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('minutos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('estadisticas_partidos');
    }
};