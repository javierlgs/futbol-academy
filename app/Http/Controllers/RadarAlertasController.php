<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Jugador;
use App\Models\ChecklistConductual;
use App\Models\AutoevaluacionJugador;
use App\Models\TestFisico; 

class RadarAlertasController extends Controller
{
    /**
     * Procesa y despliega la consola global de alertas preventivas.
     */
    public function index()
    {
        $alertasEmitidas = [];
        $jugadores = Jugador::orderBy('apellidos')->get();

        foreach ($jugadores as $jugador) {
            
            // 1. Condición Conductual: 3 o más alertas negativas registradas por profesores esta semana
            $alertasConductaSemanal = ChecklistConductual::where('jugador_id', $jugador->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->where(function($query) {
                    $query->where('aislamiento_social', true)
                          ->orWhere('frustracion_extrema', true)
                          ->orWhere('agresividad', true);
                })->count();

            // 2. Condición Emocional Infantil: Auto-reporte consecutivo en Rojo (Mal)
            $ultimasDosEncuestas = AutoevaluacionJugador::where('jugador_id', $jugador->id)
                ->latest()
                ->take(2)
                ->get();
                
            $bajoAnimoConsecutivo = $ultimasDosEncuestas->count() == 2 && 
                $ultimasDosEncuestas->where('animo_entrenamiento', 1)->count() == 2;

            // Si cumple cualquiera de los parámetros de alerta suave, lo agregamos a la consola
            if ($alertasConductaSemanal >= 3 || $bajoAnimoConsecutivo) {
                $alertasEmitidas[] = [
                    'jugador_id' => $jugador->id,
                    'nombre_completo' => $jugador->apellidos . ' ' . $jugador->nombres,
                    'motivo_alerta' => $alertasConductaSemanal >= 3 
                        ? 'Registra variaciones repetidas de aislamiento o frustración en prácticas esta semana.' 
                        : 'El alumno ha reportado bajo ánimo deportivo de forma consecutiva.',
                    'sugerencia_coordinador' => 'Se sugiere contactar cordialmente al representante para retroalimentar el bienestar integral del menor.'
                ];
            }
        }

        return Inertia::render('Radar/Index', [
            'alertas' => $alertasEmitidas
        ]);
    }

    /**
     * Almacena la autoevaluación táctil del niño.
     */
    public function storeAutoevaluacion(Request $request)
    {
        $validated = $request->validate([
            'jugador_id' => 'required|exists:jugadores,id',
            'entrenamiento_id' => 'required|exists:entrenamientos,id',
            'animo_entrenamiento' => 'required|integer|between:1,3',
            'calidad_sueno' => 'required|integer|between:1,3',
            'siente_dolor' => 'required|boolean',
            'diversion' => 'required|integer|between:1,3',
            'cansancio_extremo' => 'required|integer|between:1,3',
        ]);

        AutoevaluacionJugador::create($validated);

        return back()->with('success', 'Respuestas guardadas correctamente.');
    }

    /**
     * Despliega la pantalla limpia tipo tótem para los niños en cancha (SOLO PRESENTES).
     */
    public function estacionAutoevaluacion(\App\Models\Entrenamiento $entrenamiento)
    {
        // 1. Buscamos los IDs de los jugadores que marcaron PRESENTE (1) hoy
        $idsPresentes = \DB::table('asistencia_entrenamientos')
            ->where('entrenamiento_id', $entrenamiento->id)
            ->where('presente', 1)
            ->pluck('jugador_id');

        // 2. Traemos las fichas de los alumnos que asistieron a la cancha
        $jugadores = \App\Models\Jugador::whereIn('id', $idsPresentes)
            ->orderBy('apellidos')
            ->get(['id', 'nombres', 'apellidos', 'foto', 'posicion']);

        // 3. Buscamos quiénes de los presentes ya respondieron el semáforo para bloquearlos en la fila
        $respondieronHoy = \App\Models\AutoevaluacionJugador::where('entrenamiento_id', $entrenamiento->id)
            ->pluck('jugador_id')
            ->toArray();

        return \Inertia\Inertia::render('Radar/Estacion', [
            'entrenamiento' => $entrenamiento->load('categoria'),
            'jugadores' => $jugadores,
            'respondieronHoy' => $respondieronHoy
        ]);
    }

    /**
     * Procesa y guarda la autoevaluación directa desde el tótem.
     */
    public function guardarDesdeEstacion(Request $request, \App\Models\Entrenamiento $entrenamiento)
    {
        $validated = $request->validate([
            'jugador_id' => 'required|exists:jugadores,id',
            'animo_entrenamiento' => 'required|integer|between:1,3',
            'calidad_sueno' => 'required|integer|between:1,3',
            'siente_dolor' => 'required|boolean',
            'diversion' => 'required|integer|between:1,3',
            'cansancio_extremo' => 'required|integer|between:1,3',
        ]);

        $validated['entrenamiento_id'] = $entrenamiento->id;

        \App\Models\AutoevaluacionJugador::create($validated);

        return back()->with('success', '¡Encuesta registrada con éxito!');
    }

    /**
     * Muestra la planilla de captura de marcas de tests físicos para los presentes.
     */
    public function formularioTests(\App\Models\Entrenamiento $entrenamiento)
    {
        // 1. Buscamos los IDs de los jugadores que marcaron PRESENTE (1) en la asistencia de este entrenamiento
        $idsPresentes = \DB::table('asistencia_entrenamientos')
            ->where('entrenamiento_id', $entrenamiento->id)
            ->where('presente', 1)
            ->pluck('jugador_id');

        // 2. Traemos la información de esos jugadores específicos
        $jugadores = \App\Models\Jugador::whereIn('id', $idsPresentes)
            ->orderBy('apellidos')
            ->get(['id', 'nombres', 'apellidos', 'foto']);

        // 3. Buscamos si ya existen marcas registradas previamente para este día
        $testsExistentes = \App\Models\TestFisico::where('fecha', $entrenamiento->fecha)
            ->whereIn('jugador_id', $jugadores->pluck('id'))
            ->get()
            ->keyBy('jugador_id');

        return \Inertia\Inertia::render('Radar/TestsFisicos', [
            'entrenamiento' => $entrenamiento->load('categoria'),
            'jugadores' => $jugadores,
            'testsExistentes' => $testsExistentes
        ]);
    }

    /**
     * Guarda en lote (bulk) las marcas físicas tomadas en la cancha.
     */
    public function guardarTests(Request $request, \App\Models\Entrenamiento $entrenamiento)
    {
        $request->validate([
            'marcas' => 'required|array',
            'marcas.*.jugador_id' => 'required|exists:jugadores,id',
        ]);

        foreach ($request->marcas as $marca) {
            \App\Models\TestFisico::updateOrCreate(
                [
                    'jugador_id' => $marca['jugador_id'],
                    'fecha' => $entrenamiento->fecha
                ],
                [
                    'velocidad_segundos' => $marca['velocidad_segundos'] ?? null,
                    'coordinacion_puntos' => $marca['coordinacion_puntos'] ?? null,
                    'reaccion_segundos' => $marca['reaccion_segundos'] ?? null,
                    'equilibrio_segundos' => $marca['equilibrio_segundos'] ?? null,
                    'resistencia_vueltas' => $marca['resistencia_vueltas'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Marcas físicas guardadas correctamente.');
    }
}