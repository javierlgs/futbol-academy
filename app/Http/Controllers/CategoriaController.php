<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Sede;
use App\Models\Jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::with('sede')->withCount('jugadores')->where('activo', true);

        if ($request->filled('sede_id')) {
            $query->where('sede_id', $request->sede_id);
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        $categorias = $query->orderBy('año_nacimiento', 'desc')
            ->orderByRaw("FIELD(sexo, 'M', 'F', 'Mixto')")
            ->paginate(20)
            ->withQueryString();
            
        $sedes = Sede::where('activa', 1)->get();

        return Inertia::render('Categorias/Index', [
            'categorias' => $categorias,
            'sedes' => $sedes,
            'filtros' => $request->only(['sede_id', 'sexo'])
        ]);
    }

    public function create()
    {
        $sedes = Sede::where('activa', 1)->get();
        return Inertia::render('Categorias/Create', compact('sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'año_nacimiento' => 'required|integer|min:2000|max:'.date('Y'),
            'sexo' => 'required|in:M,F,Mixto',
            'sede_id' => 'required|exists:sedes,id',
            'activo' => 'boolean',
        ], [
            'año_nacimiento.unique' => 'Ya existe esa categoría para ese año/sexo/sede'
        ]);

        Categoria::create($validated);
        return redirect()->route('categorias.index')->with('success', 'Categoría creada');
    }

    public function edit(Categoria $categoria)
    {
        $categoria->loadCount('jugadores');
        $sedes = Sede::where('activa', 1)->get();
        return Inertia::render('Categorias/Edit', compact('categoria', 'sedes'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'año_nacimiento' => 'required|integer|min:2000|max:' . date('Y'),
            'sexo' => 'required|in:M,F,Mixto',
            'sede_id' => 'required|exists:sedes,id',
            'activo' => 'boolean',
        ]);

        DB::transaction(function () use ($categoria, $validated) {
            $categoria->update($validated);
            
            // Reasignar jugadores que ya no calcen con la nueva categoría
            Jugador::where('categoria_id', $categoria->id)->get()->each(function($jugador) {
                $año = date('Y', strtotime($jugador->fecha_nacimiento));
                $nuevaCat = Categoria::where('año_nacimiento', $año)
                    ->where('sede_id', $jugador->sede_id)
                    ->whereIn('sexo', [$jugador->sexo, 'Mixto'])
                    ->orderByRaw("FIELD(sexo, ?, 'Mixto')", [$jugador->sexo])
                    ->first();
                
                if ($nuevaCat && $nuevaCat->id != $jugador->categoria_id) {
                    $jugador->update(['categoria_id' => $nuevaCat->id]);
                }
            });
        });

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada');
    }
}