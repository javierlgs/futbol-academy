<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes');
            $table->string('nombre'); // Sub-12, Sub-15
            $table->integer('edad_min');
            $table->integer('edad_max');
            $table->text('horarios')->nullable(); // JSON: Lunes 16:00, Miércoles 16:00
            $table->string('lugar_entrenamiento')->nullable();
            $table->foreignId('entrenador_id')->nullable()->constrained('users');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('categorias');
    }
};