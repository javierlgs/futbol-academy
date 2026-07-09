<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tacticas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->longText('dibujo_data'); // Aquí guardamos el base64 del canvas
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tacticas');
    }
};