<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('campeonatos', function (Blueprint $table) {
            $table->year('año_base')->after('tipo')->nullable()->comment('Año máximo que puede jugar');
            $table->json('años_habilitados')->after('año_base')->nullable()->comment('Array de años permitidos');
        });
    }

    public function down(): void {
        Schema::table('campeonatos', function (Blueprint $table) {
            $table->dropColumn(['año_base', 'años_habilitados']);
        });
    }
};