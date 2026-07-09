<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadesSeeder extends Seeder {
    public function run(): void {
        // Catálogo de habilidades según tu prompt inicial - Módulo 3
        $habilidades = [
            // Técnicas
            ['nombre' => 'Control de balón', 'categoria' => 'tecnica', 'descripcion' => 'Dominio del balón con ambos pies', 'orden' => 1],
            ['nombre' => 'Pase corto', 'categoria' => 'tecnica', 'descripcion' => 'Precisión en pases a corta distancia', 'orden' => 2],
            ['nombre' => 'Pase largo', 'categoria' => 'tecnica', 'descripcion' => 'Precisión en pases largos', 'orden' => 3],
            ['nombre' => 'Tiro a puerta', 'categoria' => 'tecnica', 'descripcion' => 'Potencia y precisión en remates', 'orden' => 4],
            ['nombre' => 'Regate', 'categoria' => 'tecnica', 'descripcion' => 'Capacidad de eludir rivales', 'orden' => 5],
            ['nombre' => 'Cabeceo', 'categoria' => 'tecnica', 'descripcion' => 'Juego aéreo ofensivo y defensivo', 'orden' => 6],
            
            // Físicas
            ['nombre' => 'Velocidad', 'categoria' => 'fisica', 'descripcion' => 'Aceleración y velocidad punta', 'orden' => 7],
            ['nombre' => 'Resistencia', 'categoria' => 'fisica', 'descripcion' => 'Capacidad aeróbica', 'orden' => 8],
            ['nombre' => 'Fuerza', 'categoria' => 'fisica', 'descripcion' => 'Potencia muscular', 'orden' => 9],
            
            // Tácticas
            ['nombre' => 'Posicionamiento', 'categoria' => 'tactica', 'descripcion' => 'Ubicación en el campo', 'orden' => 10],
            ['nombre' => 'Visión de juego', 'categoria' => 'tactica', 'descripcion' => 'Lectura del juego', 'orden' => 11],
        ];

        foreach ($habilidades as $habilidad) {
            DB::table('habilidades')->insert([
                'nombre' => $habilidad['nombre'],
                'categoria' => $habilidad['categoria'],
                'descripcion' => $habilidad['descripcion'],
                'orden' => $habilidad['orden'],
                'activa' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}