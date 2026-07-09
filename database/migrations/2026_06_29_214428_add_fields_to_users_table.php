<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sede_id')->nullable()->constrained('sedes');
            $table->string('cedula')->unique()->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->boolean('es_superadmin')->default(false);
            $table->timestamp('ultimo_acceso')->nullable();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sede_id', 'cedula', 'telefono', 'direccion', 'estado', 'es_superadmin', 'ultimo_acceso']);
        });
    }
};