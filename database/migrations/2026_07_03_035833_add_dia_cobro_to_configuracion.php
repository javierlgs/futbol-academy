<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            // Guardaremos un entero del 1 al 28
            $table->integer('dia_cobro_automatico')->default(1)->after('modulo_facturas_habilitado');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn('dia_cobro_automatico');
        });
    }
};