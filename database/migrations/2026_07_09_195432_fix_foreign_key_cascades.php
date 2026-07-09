<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Corregir Partidos (depende de Torneos)
        Schema::table('partidos', function (Blueprint $table) {
            $table->dropForeign(['torneo_id']);
            $table->foreign('torneo_id')->references('id')->on('torneos')->onDelete('cascade');
        });

        // 2. Corregir Alineaciones (depende de Partidos)
        Schema::table('alineaciones', function (Blueprint $table) {
            $table->dropForeign(['partido_id']);
            $table->foreign('partido_id')->references('id')->on('partidos')->onDelete('cascade');
        });

        // 3. Corregir Fotos (depende de Partidos)
        Schema::table('fotos', function (Blueprint $table) {
            $table->dropForeign(['partido_id']);
            $table->foreign('partido_id')->references('id')->on('partidos')->onDelete('cascade');
        });

        // 4. Corregir Tácticas (depende de Partidos)
        Schema::table('tacticas_partido', function (Blueprint $table) {
            $table->dropForeign(['partido_id']);
            $table->foreign('partido_id')->references('id')->on('partidos')->onDelete('cascade');
        });
    }

    public function down()
    {
        // En caso de revertir, aquí pondrías la lógica inversa
    }
};