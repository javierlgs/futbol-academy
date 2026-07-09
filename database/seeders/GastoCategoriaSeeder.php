<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GastoCategoriaSeeder extends Seeder {
    public function run(): void {
        $categorias = [
            ['nombre' => 'Arriendo Sede', 'color' => '#EF4444', 'icono' => 'BuildingOfficeIcon', 'deducible_sri' => true],
            ['nombre' => 'Servicios Básicos', 'color' => '#F59E0B', 'icono' => 'BoltIcon', 'deducible_sri' => true],
            ['nombre' => 'Implementos Deportivos', 'color' => '#10B981', 'icono' => 'ShoppingBagIcon', 'deducible_sri' => true],
            ['nombre' => 'Transporte', 'color' => '#3B82F6', 'icono' => 'TruckIcon', 'deducible_sri' => true],
            ['nombre' => 'Salarios Entrenadores', 'color' => '#8B5CF6', 'icono' => 'UserGroupIcon', 'deducible_sri' => true],
            ['nombre' => 'Mantenimiento', 'color' => '#EC4899', 'icono' => 'WrenchScrewdriverIcon', 'deducible_sri' => true],
            ['nombre' => 'Marketing', 'color' => '#06B6D4', 'icono' => 'MegaphoneIcon', 'deducible_sri' => true],
            ['nombre' => 'Otros Gastos', 'color' => '#6B7280', 'icono' => 'DocumentTextIcon', 'deducible_sri' => false],
        ];

        foreach ($categorias as $cat) {
            DB::table('gasto_categorias')->insert([
                ...$cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}