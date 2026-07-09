<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        // 🔥 Desactivar claves foráneas para permitir eliminar la tabla
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categorias');
        Schema::enableForeignKeyConstraints();

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->year('año_nacimiento');
            $table->enum('sexo', ['M', 'F', 'Mixto'])->default('M');
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['año_nacimiento', 'sexo', 'sede_id']);
        });
    }

    public function down(): void {

        // 🔥 También aquí para evitar errores al revertir migraciones
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categorias');
        Schema::enableForeignKeyConstraints();
    }
};
