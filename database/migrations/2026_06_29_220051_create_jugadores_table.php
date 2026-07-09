<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->string('foto')->nullable();
            $table->enum('posicion', ['POR', 'DEF', 'MED', 'DEL'])->nullable();
            $table->enum('estado', ['activo', 'lesionado', 'inactivo', 'prueba'])->default('activo');
            $table->text('observaciones_medicas')->nullable();

            // Datos Representante
            $table->string('rep_nombres');
            $table->string('rep_apellidos');
            $table->string('rep_relacion'); // Padre, Madre, Tío
            $table->string('rep_telefono');
            $table->string('rep_correo')->nullable();
            $table->text('rep_direccion');
            $table->string('rep_cedula')->nullable();
            $table->text('datos_facturacion')->nullable();

            $table->foreignId('padre_user_id')->nullable()->constrained('users'); // Para acceso del padre
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('jugadores');
    }
};