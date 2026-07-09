<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('fotografias_entrenamientos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->onDelete('cascade');
        $table->string('ruta_foto'); // Ruta física en el storage: 'fotos_entrenamientos/abc.jpg'
        $table->foreignId('subido_por')->constrained('users');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotografia_entrenamientos');
    }
};
