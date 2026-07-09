<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder {
    public function run(): void {
        // 1. Sede principal
        $sede = Sede::create([
            'nombre' => 'Sede Principal',
            'direccion' => 'Cuenca, Azuay, Ecuador',
            'telefono' => '0999999999',
            'principal' => 1,
            'activa' => 1,
        ]);

        // 2. IVA - usa insertGetId para obtener el ID
        $iva12 = DB::table('iva_tarifas')->insertGetId([
            'porcentaje' => 12.00,
            'codigo_sri' => '2',
            'descripcion' => 'IVA 12% - Tarifa vigente',
            'activo' => 1,
            'por_defecto' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('iva_tarifas')->insert([
            [
                'porcentaje' => 0.00,
                'codigo_sri' => '0',
                'descripcion' => 'IVA 0% - Productos exentos',
                'activo' => 1,
                'por_defecto' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Settings - updateOrInsert evita el error 1062
        $settings = [
            ['key' => 'sede_principal_id', 'value' => $sede->id],
            ['key' => 'nombre_escuela', 'value' => 'Academia Elite FC'],
            ['key' => 'direccion_principal', 'value' => 'Cuenca, Azuay, Ecuador'],
            ['key' => 'telefono', 'value' => '0999999999'],
            ['key' => 'correo', 'value' => 'info@elitefc.com'],
            ['key' => 'color_primario', 'value' => '#16a34a'],
            ['key' => 'color_secundario', 'value' => '#0ea5e9'],
            ['key' => 'iva_tarifa_id', 'value' => $iva12],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}