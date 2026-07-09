<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. ENTRENAMIENTOS - NUEVO
        Schema::create('entrenamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('user_id')->constrained('users'); // entrenador responsable
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('lugar')->nullable();
            $table->text('objetivos')->nullable(); // habilidades a trabajar
            $table->text('ejercicios')->nullable(); 
            $table->text('materiales')->nullable();
            $table->enum('estado', ['planificado', 'realizado', 'cancelado'])->default('planificado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 2. ASISTENCIA ENTRENAMIENTOS - NUEVO
        Schema::create('asistencia_entrenamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade');
            $table->boolean('presente')->default(false);
            $table->enum('estado', ['puntual', 'atraso', 'falta_justificada', 'falta_injustificada'])->default('falta_injustificada');
            $table->text('observacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->unique(['jugador_id', 'entrenamiento_id']);
        });

        // 3. CAMPEONATOS - TU CÓDIGO ORIGINAL
        Schema::create('campeonatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->string('nombre');
            $table->enum('tipo', ['liga', 'copa', 'amistoso', 'torneo']);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['planificado', 'en_curso', 'finalizado'])->default('planificado');
            $table->timestamps();
        });

        // 4. PARTIDOS - TU CÓDIGO ORIGINAL
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campeonato_id')->constrained('campeonatos')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->string('rival');
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->string('lugar')->nullable();
            $table->integer('goles_favor')->default(0);
            $table->integer('goles_contra')->default(0);
            $table->enum('resultado', ['victoria', 'derrota', 'empate', 'pendiente'])->default('pendiente');
            $table->enum('condicion', ['local', 'visitante', 'neutral'])->default('local');
            $table->enum('estado', ['planificado', 'en_juego', 'finalizado', 'cancelado'])->default('planificado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 5. ALINEACIONES - TU CÓDIGO ORIGINAL - ESTO ES LA "ASISTENCIA A PARTIDOS"
        Schema::create('alineaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->enum('tipo', ['titular', 'suplente']);
            $table->string('posicion_partido')->nullable(); // POR, DEF, MED, DEL
            $table->integer('minuto_entrada')->default(0);
            $table->integer('minuto_salida')->nullable();
            $table->timestamps();
            
            $table->unique(['partido_id', 'jugador_id']);
        });

        // 6. EVENTOS PARTIDO - TU CÓDIGO ORIGINAL
        Schema::create('eventos_partido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('jugador_id')->nullable()->constrained('jugadores');
            $table->enum('tipo', ['gol', 'asistencia', 'amarilla', 'roja', 'cambio_entra', 'cambio_sale', 'fallo_portero']);
            $table->integer('minuto')->nullable();
            $table->string('detalle')->nullable(); // "Mala salida", "Reflejos", etc para porteros
            $table->foreignId('jugador_relacionado_id')->nullable()->constrained('jugadores'); // Para cambios
            $table->timestamps();
        });

        // 7. EVALUACIONES PARTIDO - TU CÓDIGO ORIGINAL
        Schema::create('evaluaciones_partido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->tinyInteger('rendimiento_general'); // 1-5
            $table->tinyInteger('tecnica')->nullable();
            $table->tinyInteger('tactica')->nullable();
            $table->tinyInteger('fisico')->nullable();
            $table->tinyInteger('mental')->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->unique(['partido_id', 'jugador_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('evaluaciones_partido');
        Schema::dropIfExists('eventos_partido');
        Schema::dropIfExists('alineaciones');
        Schema::dropIfExists('partidos');
        Schema::dropIfExists('campeonatos');
        Schema::dropIfExists('asistencia_entrenamientos');
        Schema::dropIfExists('entrenamientos');
    }
};