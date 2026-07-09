<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder {
    public function run(): void {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lee el sede_id real que creó SettingsSeeder
        $sedeId = DB::table('settings')->where('key', 'sede_principal_id')->value('value');
        
        if (!$sedeId) {
            throw new \Exception('SettingsSeeder debe ejecutarse primero. No existe sede_principal_id');
        }

        // Permisos
        $permisos = [
            'ver_dashboard', 'ver_jugadores', 'crear_jugador', 'editar_jugador', 'eliminar_jugador',
            'ver_entrenamientos', 'crear_entrenamiento', 'evaluar_entrenamiento',
            'ver_partidos', 'crear_partido', 'gestionar_alineacion', 'registrar_evento_partido',
            'ver_psicologia', 'crear_evaluacion_psicologica', 'editar_evaluacion_psicologica',
            'ver_finanzas', 'crear_factura', 'gestionar_gastos', 'ver_caja',
            'ver_galeria', 'subir_fotos', 'gestionar_galeria',
            'gestionar_usuarios', 'gestionar_configuracion', 'gestionar_sedes'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // Roles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'administrador']);
        $director = Role::firstOrCreate(['name' => 'director_deportivo']);
        $entrenador = Role::firstOrCreate(['name' => 'entrenador']);
        $psicologo = Role::firstOrCreate(['name' => 'psicologo']);
        $secretaria = Role::firstOrCreate(['name' => 'secretaria']);
        $padre = Role::firstOrCreate(['name' => 'padre']);

        // Asignar permisos
        $superadmin->syncPermissions(Permission::all());
        
        $admin->syncPermissions([
            'ver_dashboard', 'ver_jugadores', 'crear_jugador', 'editar_jugador',
            'ver_entrenamientos', 'ver_partidos', 'ver_finanzas', 'crear_factura',
            'gestionar_gastos', 'ver_caja', 'ver_galeria', 'gestionar_usuarios',
            'gestionar_configuracion', 'gestionar_sedes'
        ]);

        $psicologo->syncPermissions([
            'ver_dashboard', 'ver_jugadores',
            'ver_psicologia', 'crear_evaluacion_psicologica', 'editar_evaluacion_psicologica'
        ]);

        // Usuario superadmin con sede_id dinámico
        User::updateOrCreate(
            ['email' => 'superadmin@elitefc.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'sede_id' => $sedeId, // Usa el ID real de MySQL
                'es_superadmin' => 1,
                'estado' => 'activo'
            ]
        )->assignRole('superadmin');
    }
}