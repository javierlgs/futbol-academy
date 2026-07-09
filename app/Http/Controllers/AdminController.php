<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sede;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware; // <-- Importación necesaria
use Illuminate\Routing\Controllers\Middleware;    // <-- Importación necesaria

class AdminController extends Controller implements HasMiddleware
{
    // Método estático obligatorio para aplicar middleware en Laravel 11/12+
    public static function middleware(): array
    {
        return [
            // Protege todas las funciones de este controlador
            new Middleware(['auth', 'verified']),
            new Middleware('role:superadmin|administrador'), 
        ];
    }

    // Listar Usuarios, sus roles asignados y sedes correspondientes
    public function usuariosIndex()
    {
        $usuarios = User::with(['roles'])->orderBy('name')->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'activo' => $user->activo,
                'sede_id' => $user->sede_id,
                'role_name' => $user->roles->first()?->name ?? 'Sin Rol'
            ];
        });

        $roles = Role::all()->pluck('name');
        $sedes = Sede::all();

        return Inertia::render('Admin/Usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'sedes' => $sedes
        ]);
    }

    // Crear un nuevo usuario desde el panel
    public function usuariosStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|exists:roles,name',
            'sede_id' => 'nullable|integer'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'sede_id' => $data['sede_id'] ?? null,
            'activo' => 1
        ]);

        $user->assignRole($data['role']);

        return back()->with('success', 'Personal deportivo registrado con éxito ⚡');
    }

    // Actualizar Rol o Sede de un usuario existente
    public function usuarioUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
            'sede_id' => 'nullable',
            'activo' => 'required|boolean'
        ]);

        $user->update([
            'sede_id' => $data['sede_id'] ?: null,
            'activo' => $data['activo']
        ]);

        $user->syncRoles([$data['role']]);

        return back()->with('success', 'Permisos y adscripción actualizados ✓');
    }

    // Listar y registrar Sedes del club
    public function sedesIndex()
    {
        $sedes = \DB::table('sedes')->get();

        return Inertia::render('Admin/Sedes', [
            'sedes' => $sedes
        ]);
    }

    public function sedesStore(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string'
        ]);

        \DB::table('sedes')->insert([
            'nombre' => $data['nombre'],
            'direccion' => $data['direccion'],
            'telefono' => $data['telefono'],
            'activa' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Nueva sede guardada con éxito 🏢');
    }
}