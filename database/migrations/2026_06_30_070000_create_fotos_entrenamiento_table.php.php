<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fotos_entrenamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('ruta');
            $table->text('descripcion')->nullable();
            $table->boolean('visible_padres')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fotos_entrenamiento');
    }
};