<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('iva_tarifas', function (Blueprint $table) {
            $table->id();
            $table->decimal('porcentaje', 5, 2)->unique();
            $table->string('codigo_sri', 1);
            $table->string('descripcion');
            $table->boolean('activo')->default(true);
            $table->boolean('por_defecto')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('iva_tarifas');
    }
};