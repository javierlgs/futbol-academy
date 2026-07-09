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
        Schema::create('tests_fisicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->date('fecha');
            $table->float('velocidad_segundos')->nullable();
            $table->integer('coordinacion_puntos')->nullable();
            $table->float('reaccion_segundos')->nullable();
            $table->integer('equilibrio_segundos')->nullable();
            $table->integer('resistencia_vueltas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests_fisicos');
    }
};