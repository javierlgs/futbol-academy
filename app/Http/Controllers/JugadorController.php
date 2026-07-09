<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Sede;
use App\Models\Categoria;
use App\Http\Requests\StoreJugadorRequest;
use App\Http\Requests\UpdateJugadorRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Importante para manejar archivos

class JugadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Jugador::with(['categoria.sede', 'sede'])
            ->where('activo', true);

        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        if ($request->filled('año_nacimiento')) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->where('año_nacimiento', $request->año_nacimiento);
            });
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('rep_cedula', 'like', "%{$search}%");
            });
        }

        $jugadores = $query->orderBy('apellidos')->orderBy('nombres')->paginate(20);
        $sedes = Sede::where('activa', 1)->get();
        $años = Categoria::distinct()->pluck('año_nacimiento')->sortDesc();

        return Inertia::render('Jugadores/Index', compact('jugadores', 'sedes', 'años'));
    }

    public function create()
    {
        $sedes = Sede::where('activa', 1)->get();
        return Inertia::render('Jugadores/Create', compact('sedes'));
    }

    public function store(StoreJugadorRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fotos_jugadores', 'public');
        }

        Jugador::create($data);
        return redirect()->route('jugadores.index')->with('success', 'Jugador creado correctamente');
    }

    public function show(Jugador $jugador)
    {
        $jugador->load(['categoria.sede', 'sede']);
        return Inertia::render('Jugadores/Show', compact('jugador'));
    }

    public function edit($id)
    {
        $jugador = Jugador::with('categoria')->findOrFail($id);
        $sedes = Sede::where('activa', 1)->get();
        
        return Inertia::render('Jugadores/Edit', [
            'jugador' => $jugador,
            'sedes' => $sedes
        ]);
    }

    public function update(UpdateJugadorRequest $request, $id)
    {
        $jugador = Jugador::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            // Eliminar foto antigua si existe
            if ($jugador->foto) {
                Storage::disk('public')->delete($jugador->foto);
            }
            $data['foto'] = $request->file('foto')->store('fotos_jugadores', 'public');
        }

        $jugador->update($data);
        
        return redirect()->route('jugadores.index')->with('success', 'Actualizado correctamente');
    }

    public function destroy(Jugador $jugador)
    {
        $jugador->update(['activo' => false]);
        return redirect()->route('jugadores.index')->with('success', 'Jugador desactivado');
    }

    public function reportePorCategoria()
    {
        $categorias = Categoria::with('sede')
            ->withCount(['jugadores' => function($q) {
                $q->where('activo', true);
            }])
            ->orderBy('año_nacimiento', 'desc')
            ->orderBy('sexo')
            ->get();
            
        return Inertia::render('Jugadores/ReporteCategoria', compact('categorias'));
    }

    public function estadisticas()
    {
        $totalJugadores = Jugador::where('activo', true)->count();
        
        $porAño = Jugador::select('categorias.año_nacimiento', DB::raw('count(*) as total'))
            ->join('categorias', 'jugadores.categoria_id', '=', 'categorias.id')
            ->where('jugadores.activo', true)
            ->groupBy('categorias.año_nacimiento')
            ->orderBy('categorias.año_nacimiento', 'desc')
            ->get();
            
        $porSexo = Jugador::select('sexo', DB::raw('count(*) as total'))
            ->where('activo', true)
            ->groupBy('sexo')
            ->get();
            
        return Inertia::render('Jugadores/Estadisticas', compact('totalJugadores', 'porAño', 'porSexo'));
    }

    public function habilitadosCampeonato($añoCategoria, $sexo)
    {
        return Jugador::whereHas('categoria', function($q) use ($añoCategoria, $sexo) {
            $q->where('año_nacimiento', '<=', $añoCategoria)
              ->whereIn('sexo', [$sexo, 'Mixto']);
        })
        ->where('jugadores.sexo', $sexo)
        ->where('activo', true)
        ->with('categoria')
        ->get();
    }
}