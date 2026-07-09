<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jugadores', function (Blueprint $table) {
            // Solo cambia si la columna existe
            if (Schema::hasColumn('jugadores', 'rep_apellidos')) {
                $table->string('rep_apellidos')->nullable()->change();
            }
            if (Schema::hasColumn('jugadores', 'rep_relacion')) {
                $table->string('rep_relacion')->nullable()->change();
            }
            if (Schema::hasColumn('jugadores', 'rep_telefono')) {
                $table->string('rep_telefono')->nullable()->change();
            }
            if (Schema::hasColumn('jugadores', 'rep_correo')) {
                $table->string('rep_correo')->nullable()->change();
            }
            if (Schema::hasColumn('jugadores', 'rep_direccion')) {
                $table->string('rep_direccion')->nullable()->change();
            }
            if (Schema::hasColumn('jugadores', 'rep_cedula')) {
                $table->string('rep_cedula')->nullable()->change();
            }
            // Borra esta línea si no tienes cedula_jugador
            // $table->string('cedula_jugador')->nullable()->change();
        });
    }

    public function down(): void {
        // No hacer down
    }
};