<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('categorias', function (Blueprint $table) {
            // Solo borra si existen
            if (Schema::hasColumn('categorias', 'nombre')) {
                $table->dropColumn('nombre');
            }
            if (Schema::hasColumn('categorias', 'edad_min')) {
                $table->dropColumn('edad_min');
            }
            if (Schema::hasColumn('categorias', 'edad_max')) {
                $table->dropColumn('edad_max');
            }
            if (Schema::hasColumn('categorias', 'entrenador_id')) {
                $table->dropColumn('entrenador_id');
            }
            if (Schema::hasColumn('categorias', 'lugar_entrenamiento')) {
                $table->dropColumn('lugar_entrenamiento');
            }
            if (Schema::hasColumn('categorias', 'precio_inscripcion')) {
                $table->dropColumn('precio_inscripcion');
            }
            if (Schema::hasColumn('categorias', 'precio_mensual')) {
                $table->dropColumn('precio_mensual');
            }
        });
    }

    public function down(): void {
        // No hacer down
    }
};