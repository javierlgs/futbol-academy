<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        if (Schema::hasTable('entrenamientos')) {
            DB::table('entrenamientos')->delete();
        }
        
        DB::table('categorias')->truncate();
        
        Schema::table('categorias', function (Blueprint $table) {
            if (Schema::hasColumn('categorias', 'nombre')) {
                $table->dropColumn('nombre');
            }
            if (Schema::hasColumn('categorias', 'edad_min')) {
                $table->dropColumn('edad_min');
            }
            if (Schema::hasColumn('categorias', 'edad_max')) {
                $table->dropColumn('edad_max');
            }
            
            if (!Schema::hasColumn('categorias', 'año_nacimiento')) {
                $table->integer('año_nacimiento')->after('id');
            }
            if (!Schema::hasColumn('categorias', 'sexo')) {
                $table->enum('sexo', ['M','F','Mixto'])->default('Mixto')->after('año_nacimiento');
            }
        });

        // Para el unique usamos try-catch porque Laravel no tiene hasIndex
        try {
            Schema::table('categorias', function (Blueprint $table) {
                $table->unique(['año_nacimiento', 'sexo', 'sede_id']);
            });
        } catch (\Exception $e) {
            // Ya existe el índice, no pasa nada
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            try {
                $table->dropUnique(['año_nacimiento', 'sexo', 'sede_id']);
            } catch (\Exception $e) {}
            
            $table->dropColumn(['año_nacimiento', 'sexo']);
            $table->string('nombre');
            $table->integer('edad_min')->nullable();
            $table->integer('edad_max')->nullable();
        });
    }
};