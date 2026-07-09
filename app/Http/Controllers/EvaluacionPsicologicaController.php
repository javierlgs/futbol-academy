<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\EvaluacionPsicologica;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EvaluacionPsicologicaController extends Controller
{
    /**
     * Listar las evaluaciones y cargar la interfaz de psicología del jugador.
     */
    public function index($jugadorId)
    {
        $user = Auth::user();
        
        // Cargar jugador con relaciones aplicando with() para optimizar consultas (evitar N+1)
        $jugador = Jugador::with(['categoria.sede', 'sede'])->findOrFail($jugadorId);

        // Validar acceso: Rol psicólogo o admin ven todo. Entrenador NO ve observaciones clínicas.
        $esEspecialista = $user->hasRole('psicologo') || $user->hasRole('admin');
        $esEntrenador = $user->hasRole('entrenador') || $user->hasRole('coordinador');

        if (!$esEspecialista && !$esEntrenador) {
            abort(403, 'No tienes permisos para acceder al área de bienestar emocional.');
        }

        // Obtener historial ordenado de evaluaciones
        $query = EvaluacionPsicologica::where('jugador_id', $jugador->id)
            ->with('psicologo:id,name')
            ->orderBy('created_at', 'asc');

        $evaluacionesCompletas = $query->get();

        // Mapear los datos del gráfico (Evolución de puntajes a lo largo del tiempo)
        $chartData = [
            'labels' => $evaluacionesCompletas->map(fn($e) => $e->created_at->format('d/m/Y')),
            'ansiedad' => $evaluacionesCompletas->map(fn($e) => $e->puntaje_ansiedad),
            'depresion' => $evaluacionesCompletas->map(fn($e) => $e->puntaje_depresion),
            'autoestima' => $evaluacionesCompletas->map(fn($e) => $e->puntaje_autoestima),
        ];

        // Si es entrenador, remover el campo sensible de la colección de datos enviada a la UI
        $evaluaciones = $evaluacionesCompletas->map(function ($evaluacion) use ($esEspecialista) {
            $data = $evaluacion->toArray();
            if (!$esEspecialista) {
                unset($data['observaciones_clinicas']); // Seguridad estricta en backend
            }
            return $data;
        });

        return Inertia::render('Jugadores/Psicologico', [
            'jugador' => $jugador,
            'evaluaciones' => $evaluaciones,
            'chartData' => $chartData,
            'puedeCrear' => $esEspecialista
        ]);
    }

    /**
     * Guardar una nueva evaluación psicológica clínica.
     */
    public function store(Request $request, $jugadorId)
    {
        // Validar que solo roles clínicos puedan registrar datos
        if (!Auth::user()->hasRole('psicologo') && !Auth::user()->hasRole('admin')) {
            abort(403, 'Acción no autorizada para su rol.');
        }

        $validated = $request->validate([
            'observaciones_clinicas' => 'required|string',
            'puntaje_ansiedad' => 'required|integer|between:1|10',
            'puntaje_depresion' => 'required|integer|between:1|10',
            'puntaje_autoestima' => 'required|integer|between:1|10',
            'recomendaciones_entrenador' => 'required|string',
            'recomendaciones_padres' => 'required|string',
            'semaforo_riesgo' => 'required|in:verde,amarillo,rojo',
            'alerta_coordinacion' => 'required|boolean',
        ]);

        $validated['jugador_id'] = $jugadorId;
        $validated['psicologo_id'] = Auth::id();

        $evaluacion = EvaluacionPsicologica::create($validated);

        // Si se marca alerta de coordinación, se disparará el proceso de notificación
        if ($evaluacion->alerta_coordinacion) {
            // NOTA: Aquí se integrarán los eventos de WhatsApp/Email del Módulo 2 subsecuente.
        }

        return redirect()->route('jugadores.psicologico.index', $jugadorId)
            ->with('success', 'Evaluación clínica registrada correctamente.');
    }
}