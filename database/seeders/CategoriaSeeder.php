<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       class CategoriaSeeder extends Seeder {
    public function run() {
        $sedes = Sede::all();
        $añoActual = date('Y');
        
        // Generamos categorías desde Sub-6 hasta Sub-18, mixto por defecto
        for ($edad = 6; $edad <= 18; $edad++) {
            $añoNacimiento = $añoActual - $edad;
            
            foreach ($sedes as $sede) {
                Categoria::firstOrCreate([
                    'año_nacimiento' => $añoNacimiento,
                    'sexo' => 'Mixto',
                    'sede_id' => $sede->id
                ]);
            }
        }
    }
}
    }
}
