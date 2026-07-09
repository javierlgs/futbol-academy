<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Partido;
use App\Models\Alineacion;
use App\Models\Jugador;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class TorneoController extends Controller
{
    public function index()
    {
        $torneos = Torneo::with('categoria')->get()->map(function ($torneo) {
            $partidos = Partido::where('torneo_id', $torneo->id)->get();
            $anioCategoria = $torneo->categoria->año_nacimiento ?? 'Sin Año';
            
            $sexoDetectado = 'Mixto';
            if (str_contains(strtolower($torneo->nombre), 'masculino') || str_contains(strtolower($torneo->nombre), 'varones')) {
                $sexoDetectado = 'Varones';
            } elseif (str_contains(strtolower($torneo->nombre), 'femenino') || str_contains(strtolower($torneo->nombre), 'mujeres')) {
                $sexoDetectado = 'Mujeres';
            }

            return [
                'id' => $torneo->id,
                'nombre' => $torneo->nombre,
                'categoria' => 'Categoría ' . $anioCategoria . ' (' . $sexoDetectado . ')',
                'estado' => $torneo->estado,
                'partidos_jugados' => $partidos->whereNotNull('resultado_gutigol')->count(),
                'partidos_pendientes' => $partidos->whereNull('resultado_gutigol')->count(),
                'proximo_rival' => $partidos->whereNull('resultado_gutigol')->first()->rival ?? 'Por definir'
            ];
        });

        return Inertia::render('Torneos/Index', [
            'torneos' => $torneos,
            'jugadores' => Jugador::all(),
            'categorias' => Categoria::select('id', 'año_nacimiento')->get()->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'nombre' => 'Categoría ' . $cat->año_nacimiento
                ];
            })
        ]);
    }

    public function mostrarPartido($id)
    {
        $partido = Partido::with('torneo.categoria')->findOrFail($id);
        
        $convocados = Alineacion::where('partido_id', $id)
            ->where('tipo', 'convocado')
            ->with('jugador')
            ->get();

        return Inertia::render('Torneos/PartidoDetalle', [
            'partido' => [
                'id' => $partido->id,
                'torneo' => $partido->torneo->nombre,
                'rival' => $partido->rival,
                'fecha' => $partido->fecha ? Carbon::parse($partido->fecha)->format('d/m/Y') : 'Por definir',
                'categoria' => 'Categoría ' . ($partido->torneo->categoria->año_nacimiento ?? '')
            ],
            'convocados' => $convocados
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required',
            'sexo' => 'required|string|in:masculino,femenino,mixto',
            'jugadores_ids' => 'nullable|array'
        ]);

        $modalidadTexto = match($validated['sexo']) {
            'masculino' => 'Masculino',
            'femenino' => 'Femenino',
            default => 'Mixto'
        };

        $torneo = Torneo::create([
            'nombre' => $validated['nombre'] . ' - ' . $modalidadTexto,
            'categoria_id' => $validated['categoria_id'],
            'estado' => 'planificado'
        ]);

        if ($request->has('jugadores_ids')) {
            $torneo->jugadores()->sync($request->jugadores_ids);
        }

        return redirect()->route('torneos.index');
    }

    public function update(Request $request, $id)
    {
        $torneo = Torneo::findOrFail($id);
        $torneo->update($request->only(['nombre', 'categoria_id']));
        
        if ($request->has('jugadores_ids')) {
            $torneo->jugadores()->sync($request->jugadores_ids);
        }
        
        return back()->with('message', 'Torneo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $torneo = Torneo::findOrFail($id);
        $torneo->delete();
        return back()->with('message', 'Torneo eliminado correctamente.');
    }

    public function fixture($id)
    {
        return $this->verFixture($id);
    }

    public function guardarPartido(Request $request, $torneo_id)
    {
        $validated = $request->validate([
            'rival' => 'required|string',
            'fecha' => 'required|date',
            'hora'  => 'required',
        ]);

        Partido::create([
            'torneo_id' => $torneo_id,
            'rival'     => $validated['rival'],
            'fecha'     => $validated['fecha'],
            'hora'      => $validated['hora'],
        ]);

        return back()->with('message', 'Encuentro agendado correctamente.');
    }

    public function guardarConvocatoria(Request $request, $partido_id)
    {
        foreach ($request->jugadores_ids as $jugador_id) {
            Alineacion::updateOrCreate(
                ['partido_id' => $partido_id, 'jugador_id' => $jugador_id],
                ['tipo' => 'convocado', 'calificacion' => 0]
            );
        }
        return back()->with('message', 'Convocatoria realizada.');
    }

    public function verFixture($torneoId)
    {
        $torneo = Torneo::with('categoria')->findOrFail($torneoId);
        $jugadores = Jugador::all(); 
        
        $partidos = Partido::where('torneo_id', $torneoId)
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(function($partido) {
                $alineaciones = Alineacion::where('partido_id', $partido->id)->get();
                
                if ($alineaciones->isEmpty()) {
                    $estado = 'CONVOCAR';
                } elseif ($alineaciones->where('calificacion', '>', 0)->isEmpty()) {
                    $estado = 'LLENAR';
                } else {
                    $estado = 'EDITAR';
                }

                return [
                    'id' => $partido->id,
                    'rival' => $partido->rival,
                    'fecha' => $partido->fecha ? Carbon::parse($partido->fecha)->format('d/m/Y') : 'Por definir',
                    'hora' => $partido->hora ? date('H:i', strtotime($partido->hora)) : 'Por definir',
                    'resultado_gutigol' => $partido->resultado_gutigol,
                    'resultado_rival' => $partido->resultado_rival,
                    'jugado' => !is_null($partido->resultado_gutigol),
                    'estado' => $estado,
                    'tiene_convocatoria' => $alineaciones->isNotEmpty(),
                    'tiene_acta' => $estado === 'EDITAR'
                ];
            });

        $anioCategoria = $torneo->categoria->año_nacimiento ?? 'Sin Año';

        return Inertia::render('Torneos/Fixture', [
            'torneo' => [
                'id' => $torneo->id,
                'nombre' => $torneo->nombre,
                'categoria' => 'Categoría ' . $anioCategoria
            ],
            'partidos' => $partidos,
            'jugadores' => $jugadores
        ]);
    }
}