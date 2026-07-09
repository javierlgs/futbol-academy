<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\IvaTarifa;

class IvaTarifasSeeder extends Seeder {
    public function run(): void {
        $tarifas = [
            ['porcentaje' => 0, 'codigo_sri' => '0', 'descripcion' => 'IVA 0%', 'activo' => true, 'por_defecto' => false],
            ['porcentaje' => 5, 'codigo_sri' => '5', 'descripcion' => 'IVA 5%', 'activo' => true, 'por_defecto' => false],
            ['porcentaje' => 12, 'codigo_sri' => '2', 'descripcion' => 'IVA 12%', 'activo' => true, 'por_defecto' => false],
            ['porcentaje' => 14, 'codigo_sri' => '3', 'descripcion' => 'IVA 14%', 'activo' => true, 'por_defecto' => false],
            ['porcentaje' => 15, 'codigo_sri' => '4', 'descripcion' => 'IVA 15%', 'activo' => true, 'por_defecto' => true],
        ];

        foreach ($tarifas as $tarifa) {
            IvaTarifa::updateOrCreate(['porcentaje' => $tarifa['porcentaje']], $tarifa);
        }
    }
}