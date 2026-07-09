<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evaluaciones_psicologicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('psicologo_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha');
            $table->enum('tipo', ['motivacion', 'concentracion', 'autoconfianza', 'manejo_presion', 'trabajo_equipo']);
            $table->integer('puntaje')->comment('1-10');
            $table->json('respuestas')->nullable()->comment('Respuestas del test');
            $table->text('observaciones')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->enum('nivel', ['bajo', 'medio', 'alto']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('evaluaciones_psicologicas');
    }
};