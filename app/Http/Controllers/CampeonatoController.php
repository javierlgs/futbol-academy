<?php

namespace App\Http\Controllers;

use App\Models\Campeonato;
use App\Models\Sede;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampeonatoController extends Controller
{
    public function index()
    {
        $campeonatos = Campeonato::with('sede')
            ->latest()
            ->paginate(20);
            
        return Inertia::render('Campeonatos/Index', compact('campeonatos'));
    }

    public function create()
    {
        $sedes = Sede::where('activa', 1)->get();
        $añosDisponibles = Categoria::distinct()
            ->pluck('año_nacimiento')
            ->sortDesc()
            ->values();

        return Inertia::render('Campeonatos/Create', [
            'sedes' => $sedes,
            'añosDisponibles' => $añosDisponibles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:liga,copa,amistoso,torneo',
            'año_base' => 'required|integer|min:2000|max:' . date('Y'),
            'sede_id' => 'required|exists:sedes,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:planificado,en_curso,finalizado',
            'años_habilitados' => 'required|array',
        ]);

        Campeonato::create($validated);

        return redirect()->route('campeonatos.index')->with('success', 'Campeonato creado');
    }
}