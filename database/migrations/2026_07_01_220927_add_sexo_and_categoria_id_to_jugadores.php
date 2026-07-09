<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jugadores', function (Blueprint $table) {
            if (!Schema::hasColumn('jugadores', 'sexo')) {
                $table->enum('sexo', ['M', 'F'])->after('fecha_nacimiento')->default('M');
            }
            if (!Schema::hasColumn('jugadores', 'categoria_id')) {
                $table->foreignId('categoria_id')->nullable()->after('sede_id')->constrained('categorias')->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn(['sexo', 'categoria_id']);
        });
    }
};