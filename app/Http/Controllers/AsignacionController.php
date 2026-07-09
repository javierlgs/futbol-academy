<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\User;
use Inertia\Inertia;

class AsignacionController extends Controller
{
    /**
     * Muestra el cuadrante completo de asignaciones semanales.
     */
    public function index()
    {
        // Traemos las asignaciones cruzando con categorías y usuarios
        $asignaciones = DB::table('asignaciones_entrenadores')
            ->join('categorias', 'asignaciones_entrenadores.categoria_id', '=', 'categorias.id')
            ->join('users', 'asignaciones_entrenadores.user_id', '=', 'users.id')
            ->select(
                'asignaciones_entrenadores.id',
                'asignaciones_entrenadores.categoria_id', // ⚡ Agregado para que no falle la edición
                'asignaciones_entrenadores.user_id',      // ⚡ Agregado para que no falle la edición
                'asignaciones_entrenadores.dias',
                'asignaciones_entrenadores.hora',
                'asignaciones_entrenadores.cancha',
                'users.name as entrenador',
                'categorias.año_nacimiento as anio_categoria'
            )
            ->get()
            ->map(function($asig) {
                return [
                    'id' => $asig->id,
                    'categoria_id' => $asig->categoria_id, // 🔥 Mapeado nativo para el formulario Vue
                    'user_id' => $asig->user_id,           // 🔥 Mapeado nativo para el formulario Vue
                    'dias' => $asig->dias,                 // 🔥 Mapeado nativo para el formulario Vue
                    'hora' => $asig->hora,                 // 🔥 Mapeado nativo para el formulario Vue
                    'entrenador' => $asig->entrenador,
                    'categoria' => 'Categoría ' . $asig->anio_categoria,
                    'horario' => $asig->dias . ' — ' . $asig->hora,
                    'cancha' => $asig->cancha
                ];
            });

        return Inertia::render('Asignaciones/Index', [
            'asignaciones' => $asignaciones,
            'categorias' => Categoria::select('id', 'año_nacimiento')->get()->map(function($cat) {
                return ['id' => $cat->id, 'nombre' => 'Categoría ' . $cat->año_nacimiento];
            }),
            // Jalamos solo los usuarios que sean entrenadores
            'entrenadores' => User::whereHas('roles', function($q) {
                $q->where('name', 'entrenador');
            })->select('id', 'name')->get() ?? User::select('id', 'name')->get()
        ]);
    }

    /**
     * Guarda una nueva asignación de entrenamiento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required',
            'user_id' => 'required',
            'dias' => 'required|string|max:255',
            'hora' => 'required|string|max:255',
            'cancha' => 'required|string|max:255',
        ]);

        DB::table('asignaciones_entrenadores')->insert([
            'categoria_id' => $validated['categoria_id'],
            'user_id' => $validated['user_id'],
            'dias' => $validated['dias'],
            'hora' => $validated['hora'],
            'cancha' => $validated['cancha'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    /**
     * Actualiza una asignación existente.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'categoria_id' => 'required',
            'user_id' => 'required',
            'dias' => 'required|string|max:255',
            'hora' => 'required|string|max:255',
            'cancha' => 'required|string|max:255',
        ]);

        DB::table('asignaciones_entrenadores')
            ->where('id', $id)
            ->update([
                'categoria_id' => $validated['categoria_id'],
                'user_id' => $validated['user_id'],
                'dias' => $validated['dias'],
                'hora' => $validated['hora'],
                'cancha' => $validated['cancha'],
                'updated_at' => now(),
            ]);

        return redirect()->back();
    }

    /**
     * Elimina una asignación del sistema.
     */
    public function destroy($id)
    {
        DB::table('asignaciones_entrenadores')
            ->where('id', $id)
            ->delete();

        return redirect()->back();
    }
}