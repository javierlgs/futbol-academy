<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Solo crea habilidades si no existe
        if (!Schema::hasTable('habilidades')) {
            Schema::create('habilidades', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Insertar habilidades por defecto solo si la tabla estaba vacía
            DB::table('habilidades')->insert([
                ['nombre' => 'Técnica', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Táctica', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Físico', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Mental', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Rendimiento General', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Solo crea evaluaciones si no existe
        if (!Schema::hasTable('evaluaciones')) {
    Schema::create('evaluaciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade'); // ← específico
        $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade'); // ← específico
        $table->foreignId('habilidad_id')->constrained('habilidades')->onDelete('cascade'); // ← específico
        $table->tinyInteger('puntaje')->unsigned();
        $table->text('comentario')->nullable();
        $table->foreignId('evaluador_id')->constrained('users');
        $table->timestamps();
        $table->unique(['entrenamiento_id', 'jugador_id', 'habilidad_id']);
    });
}
    }

    public function down(): void {
        Schema::dropIfExists('evaluaciones');
        Schema::dropIfExists('habilidades');
    }
};