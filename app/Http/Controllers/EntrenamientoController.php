<?php

namespace App\Http\Controllers;

use App\Models\Entrenamiento;
use App\Models\Categoria;
use App\Models\Jugador;
use App\Models\Habilidad;
use App\Models\AsistenciaEntrenamiento;
use App\Models\Evaluacion;
use App\Models\ChecklistConductual;
use App\Models\FotografiaEntrenamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class EntrenamientoController extends Controller 
{
    public function index(Request $request) 
    {
        $user = auth()->user();

        // Iniciamos la consulta cargando la relación de categoría
        $query = Entrenamiento::with(['categoria'])
            ->withCount([
                'asistencias as total_presentes' => function($q) {
                    $q->where('presente', 1);
                },
                'asistencias as total_ausentes' => function($q) {
                    $q->where('presente', 0);
                },
                'evaluaciones as total_calificaciones'
            ]);

        // 🔒 REGLA DE PRIVACIDAD: Si es rol 'entrenador', filtrar solo sus asignaciones
        if ($user->role === 'entrenador') {
            $query->where('user_id', $user->id);
        }

        $entrenamientos = $query->latest('fecha')->paginate(10);

        return Inertia::render('Entrenamientos/Index', [
            'entrenamientos' => $entrenamientos
        ]);
    }

    public function create() 
    {
        $categorias = Categoria::where('activo', 1)->get();
        $sedes = DB::table('sedes')->get();

        return Inertia::render('Entrenamientos/Create', [
            'categorias' => $categorias,
            'sedes' => $sedes
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'fecha'        => 'required|date',
            'hora_inicio'  => 'required',
            'hora_fin'     => 'nullable',
            'objetivo'     => 'nullable|string',
            'sede_id'      => 'nullable|exists:sedes,id'
        ]);

        $categoria = Categoria::find($data['categoria_id']);
        $sedeId = $data['sede_id'] ?? ($categoria?->sede_id ?? auth()->user()->sede_id);

        if (!$sedeId) {
            return back()->withErrors(['sede_id' => 'Debe especificar una sede válida para este entrenamiento.']);
        }

        $entrenamiento = Entrenamiento::create([
            'categoria_id' => $data['categoria_id'],
            'user_id'      => auth()->id(),
            'sede_id'      => $sedeId,
            'fecha'        => $data['fecha'],
            'hora_inicio'  => $data['hora_inicio'],
            'hora_fin'     => $data['hora_fin'] ?? null,
            'objetivo'     => $data['objetivo'],
            'estado'       => 'planificado'
        ]);

        return redirect()->route('entrenamientos.asistencia', $entrenamiento->id);
    }

    public function asistencia(Entrenamiento $entrenamiento) 
    {
        $categoria = $entrenamiento->categoria;

        $jugadores = Jugador::whereYear('fecha_nacimiento', $categoria->año_nacimiento)
            ->where('activo', 1)
            ->orderBy('apellidos')
            ->get();

        $asistencias = AsistenciaEntrenamiento::where('entrenamiento_id', $entrenamiento->id)
            ->pluck('presente', 'jugador_id');

        return Inertia::render('Entrenamientos/Asistencia', [
            'entrenamiento' => $entrenamiento->load('categoria'),
            'jugadores' => $jugadores,
            'asistencias' => $asistencias
        ]);
    }

    public function guardarAsistencia(Request $request, Entrenamiento $entrenamiento) 
    {
        $request->validate([
            'asistencias' => 'required|array'
        ]);

        foreach ($request->asistencias as $jugador_id => $presente) {
            AsistenciaEntrenamiento::updateOrCreate(
                ['entrenamiento_id' => $entrenamiento->id, 'jugador_id' => $jugador_id],
                [
                    'presente' => $presente,
                    'registrado_por' => auth()->id()
                ]
            );
        }

        return redirect()->route('entrenamientos.evaluar', $entrenamiento->id);
    }

    public function evaluar(Entrenamiento $entrenamiento) 
    {
        $categoria = $entrenamiento->categoria;

        $jugadoresPresentes = Jugador::whereYear('fecha_nacimiento', $categoria->año_nacimiento)
            ->whereHas('asistencias', function($q) use ($entrenamiento) {
                $q->where('entrenamiento_id', $entrenamiento->id)
                  ->where('presente', 1);
            })
            ->with(['evaluaciones' => function($q) use ($entrenamiento) {
                $q->where('entrenamiento_id', $entrenamiento->id);
            }])
            ->orderBy('apellidos')
            ->get();

        $habilidades = Habilidad::where('activa', true)->orderBy('orden')->get();

        $checklistsExistentes = ChecklistConductual::where('entrenamiento_id', $entrenamiento->id)
            ->get()
            ->keyBy('jugador_id');

        $fotosExistentes = DB::table('fotos_entrenamientos')
            ->where('entrenamiento_id', $entrenamiento->id)
            ->get();

        return Inertia::render('Entrenamientos/Evaluar', [
            'entrenamiento' => $entrenamiento->load('categoria'),
            'jugadores' => $jugadoresPresentes,
            'habilities' => $habilidades,
            'checklistsExistentes' => $checklistsExistentes,
            'fotosExistentes' => $fotosExistentes
        ]);
    }

    public function guardarEvaluacion(Request $request, Entrenamiento $entrenamiento) 
    {
        DB::transaction(function() use ($request, $entrenamiento) {
            foreach ($request->evaluaciones as $jugador_id => $habs) {
                foreach ($habs as $habilidad_id => $puntaje) {
                    if ($puntaje > 0) {
                        Evaluacion::updateOrCreate(
                            [
                                'entrenamiento_id' => $entrenamiento->id,
                                'jugador_id' => $jugador_id,
                                'habilidad_id' => $habilidad_id
                            ],
                            [
                                'puntaje' => $puntaje,
                                'comentario' => $request->comentarios[$jugador_id] ?? null,
                                'evaluador_id' => auth()->id(),
                            ]
                        );
                    }
                }
            }

            if ($request->has('conductas')) {
                foreach ($request->conductas as $jugador_id => $conducta) {
                    $aislamiento = isset($conducta['aislamiento_social']) && $conducta['aislamiento_social'];
                    $frustracion = isset($conducta['frustracion_extrema']) && $conducta['frustracion_extrema'];
                    $agresividad = isset($conducta['agresividad']) && $conducta['agresividad'];

                    $tieneAlertaActual = ($aislamiento || $frustracion || $agresividad);
                    $derivadoAutomatico = false;

                    if ($tieneAlertaActual) {
                        $entrenamientoAnterior = Entrenamiento::where('categoria_id', $entrenamiento->categoria_id)
                            ->where('id', '<', $entrenamiento->id)
                            ->where('estado', 'completado')
                            ->latest('fecha')
                            ->first();

                        if ($entrenamientoAnterior) {
                            $alertaPrevia = ChecklistConductual::where('entrenamiento_id', $entrenamientoAnterior->id)
                                ->where('jugador_id', $jugador_id)
                                ->where(function($q) {
                                    $q->where('aislamiento_social', true)
                                      ->orWhere('frustracion_extrema', true)
                                      ->orWhere('agresividad', true);
                                })->exists();

                            if ($alertaPrevia) {
                                $derivadoAutomatico = true;
                            }
                        }
                    }

                    ChecklistConductual::updateOrCreate(
                        [
                            'entrenamiento_id' => $entrenamiento->id,
                            'jugador_id' => $jugador_id,
                        ],
                        [
                            'entrenador_id' => auth()->id(),
                            'aislamiento_social' => $aislamiento,
                            'frustracion_extrema' => $frustracion,
                            'agresividad' => $agresividad,
                            'derivado_automatico' => $derivadoAutomatico
                        ]
                    );
                }
            }

            $entrenamiento->update(['estado' => 'realizado']);
        });

        return redirect()->route('dashboard')->with('success', 'Evaluación y checklist conductual guardados correctamente ⚡');
    }

    public function subirFotos(Request $request, Entrenamiento $entrenamiento)
    {
        $request->validate([
            'fotos' => 'required|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $archivo) {
                $nombreArchivo = $archivo->store('fotos_entrenamientos', 'public');
                $rutaRelativa = '/storage/' . $nombreArchivo;

                DB::table('fotos_entrenamientos')->insert([
                    'entrenamiento_id' => $entrenamiento->id,
                    'ruta' => $rutaRelativa,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('success', '¡Fotografías publicadas con éxito!');
    }

    public function eliminarFoto($fotoId)
    {
        $foto = DB::table('fotos_entrenamientos')->where('id', $fotoId)->first();

        if ($foto) {
            $rutaReal = str_replace('/storage/', '', $foto->ruta);
            Storage::disk('public')->delete($rutaReal);
            DB::table('fotos_entrenamientos')->where('id', $fotoId)->delete();
        }
        
        return back();
    }
}