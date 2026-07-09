<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['Ahorros', 'Corriente']);
            $table->string('numero_cuenta');
            $table->string('titular');
            $table->boolean('activo')->default(true);
            $table->boolean('principal')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bancos');
    }
};