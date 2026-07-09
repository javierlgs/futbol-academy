<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('equipos')) {
            Schema::create('equipos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('categoria_id')->constrained();
                $table->foreignId('entrenador_id')->constrained('users');
                $table->string('nombre_campeonato');
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->enum('estado', ['inscrito','activo','finalizado'])->default('inscrito');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('equipo_jugador')) {
    Schema::create('equipo_jugador', function (Blueprint $table) {
        $table->id();
        $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
        $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade'); // ← AQUI
        $table->string('posicion')->nullable();
        $table->timestamps();
        $table->unique(['equipo_id', 'jugador_id']);
    });
}
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_jugador');
        Schema::dropIfExists('equipos');
    }
};