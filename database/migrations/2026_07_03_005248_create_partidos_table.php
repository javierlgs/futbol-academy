<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->string('rival'); // Ej: "Emelec Filial"
            $table->date('fecha');
            $table->string('hora')->nullable();
            $table->string('resultado_gutigol')->nullable();
            $table->string('resultado_rival')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('partidos');
    }
};