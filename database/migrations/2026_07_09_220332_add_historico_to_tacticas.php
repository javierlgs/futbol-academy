<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up() {
    Schema::table('tacticas', function (Blueprint $table) {
        $table->string('nombre_tactica')->default('Análisis Táctico'); // Para identificar el guardado
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tacticas', function (Blueprint $table) {
            //
        });
    }
};
