<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gasto_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color', 7)->default('#3B82F6');
            $table->string('icono')->default('DocumentTextIcon');
            $table->boolean('deducible_sri')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('gasto_categorias');
    }
};