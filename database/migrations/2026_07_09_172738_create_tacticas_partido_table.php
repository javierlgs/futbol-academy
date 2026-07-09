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
    Schema::create('tacticas_partido', function (Blueprint $table) {
        $table->id();
        $table->foreignId('partido_id')->constrained();
        $table->longText('dibujo_data'); // Aquí guardamos el dibujo base64
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tacticas_partido');
    }
};
