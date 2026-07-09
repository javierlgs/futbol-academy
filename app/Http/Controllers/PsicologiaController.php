<?php
namespace App\Http\Controllers;
use App\Models\Jugador;
use App\Models\EvaluacionPsicologica;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PsicologiaController extends Controller {
    public function index() {
        $evaluaciones = EvaluacionPsicologica::with(['jugador', 'psicologo'])
            ->latest('fecha')
            ->paginate(20);

        return Inertia::render('Psicologia/Index', compact('evaluaciones'));
    }

    public function create(Jugador $jugador) {
        $preguntas = $this->getPreguntasTest();
        return Inertia::render('Psicologia/Test', compact('jugador', 'preguntas'));
    }

    public function store(Request $request, Jugador $jugador) {
        $data = $request->validate([
            'tipo' => 'required|in:motivacion,concentracion,autoconfianza,manejo_presion,trabajo_equipo',
            'respuestas' => 'required|array',
            'observaciones' => 'nullable|string'
        ]);

        // Calcular puntaje automático
        $puntaje = $this->calcularPuntaje($data['tipo'], $data['respuestas']);
        $nivel = $puntaje >= 8 ? 'alto' : ($puntaje >= 5 ? 'medio' : 'bajo');
        $recomendaciones = $this->generarRecomendaciones($data['tipo'], $nivel);

        EvaluacionPsicologica::create([
            'jugador_id' => $jugador->id,
            'psicologo_id' => auth()->id(),
            'fecha' => now(),
            'tipo' => $data['tipo'],
            'puntaje' => $puntaje,
            'respuestas' => $data['respuestas'],
            'observaciones' => $data['observaciones'],
            'recomendaciones' => $recomendaciones,
            'nivel' => $nivel
        ]);

        return redirect()->route('jugadores.show', $jugador)->with('success', 'Evaluación guardada');
    }

    private function getPreguntasTest() {
        return [
            'motivacion' => [
                '¿Te sientes emocionado antes de entrenar?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Das lo mejor aunque el equipo vaya perdiendo?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Te esfuerzas por mejorar cada día?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Te gusta competir y ganar?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Sueñas con ser futbolista profesional?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
            ],
            'concentracion' => [
                '¿Mantienes la atención todo el partido?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Ignoras distracciones de la grada?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Recuerdas las instrucciones del profe?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Te concentras aunque estés cansado?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
                '¿Analizas jugadas durante el partido?' => [1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'],
            ],
            // Agrega más tipos igual
        ];
    }

    private function calcularPuntaje($tipo, $respuestas) {
        $total = array_sum($respuestas);
        $maximo = count($respuestas) * 3;
        return round(($total / $maximo) * 10);
    }

    private function generarRecomendaciones($tipo, $nivel) {
        $recs = [
            'motivacion' => [
                'bajo' => 'Trabajar metas a corto plazo. Reconocer pequeños logros. Charlas motivacionales.',
                'medio' => 'Mantener rutinas de visualización. Videos de ídolos.',
                'alto' => 'Canalizar energía. Evitar sobrecarga. Liderazgo positivo.'
            ],
            'concentracion' => [
                'bajo' => 'Ejercicios de atención plena 5min diarios. Reducir estímulos externos.',
                'medio' => 'Técnicas de respiración pre-partido. Focalización en objetivos.',
                'alto' => 'Mantener rutinas. Enseñar a compañeros técnicas de foco.'
            ]
        ];
        return $recs[$tipo][$nivel] ?? 'Seguimiento mensual recomendado.';
    }
}