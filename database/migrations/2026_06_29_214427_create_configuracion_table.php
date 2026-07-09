<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_escuela');
            $table->string('logo')->nullable();
            $table->string('color_primario')->default('#0EA5E9');
            $table->string('color_secundario')->default('#0F172A');
            $table->text('direccion_principal');
            $table->string('telefono');
            $table->string('correo');
            $table->string('iva')->default('12'); // Ecuador
            $table->string('ruc')->nullable();
            $table->json('plantillas_whatsapp')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('configuracion');
    }
};