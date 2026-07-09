<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Alineacion;
use App\Models\Foto;
use App\Models\Tactica; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PartidoController extends Controller
{
    /**
     * Guarda la alineación del partido.
     */
    public function storeAlineacion(Request $request, Partido $partido)
    {
        $validated = $request->validate([
            'alineaciones' => 'required|array',
            'alineaciones.*.jugador_id' => 'required|exists:jugadores,id',
            'alineaciones.*.tipo' => 'required|in:titular,suplente',
            'alineaciones.*.minuto_entrada' => 'required|integer|min:0',
            'alineaciones.*.minuto_salida' => 'nullable|integer|min:0',
            'alineaciones.*.posicion_partido' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated, $partido) {
            foreach ($validated['alineaciones'] as $item) {
                Alineacion::updateOrCreate(
                    ['partido_id' => $partido->id, 'jugador_id' => $item['jugador_id']],
                    [
                        'tipo' => $item['tipo'],
                        'minuto_entrada' => $item['minuto_entrada'],
                        'minuto_salida' => $item['minuto_salida'] ?? null,
                        'posicion_partido' => $item['posicion_partido'] ?? null
                    ]
                );
            }
        });

        return back()->with('message', 'Alineación guardada correctamente.');
    }

    /**
     * Guarda el acta final, análisis y fotos del partido.
     */
    public function guardarActa(Request $request, $partido_id)
    {
        $request->validate([
            'analisis_general' => 'nullable',
            'alineaciones' => 'required|array',
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|max:5120',
        ]);

        DB::transaction(function () use ($request, $partido_id) {
            $analisisJson = json_encode($request->analisis_general);

            Partido::where('id', $partido_id)->update(['analisis_general' => $analisisJson]);

            foreach ($request->alineaciones as $item) {
                Alineacion::updateOrCreate(
                    ['partido_id' => $partido_id, 'jugador_id' => $item['jugador_id']],
                    [
                        'tipo' => $item['asistio'] ? 'asistente' : 'ausente',
                        'calificacion' => $item['calificacion'] ?? 0,
                        'animo' => $item['animo'] ?? null
                    ]
                );
            }

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('fotos_partidos', 'public');
                    Foto::create(['partido_id' => $partido_id, 'ruta' => $path]);
                }
            }
        });

        return to_route('torneos.index')->with('message', '¡Acta guardada correctamente!');
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

    /**
     * Guarda la táctica en el historial.
     */
    public function guardarTacticas(Request $request, $partido_id)
    {
        $request->validate(['dibujo_data' => 'required']);
        
        Tactica::create([
            'partido_id' => $partido_id,
            'dibujo_data' => $request->dibujo_data,
            'nombre_tactica' => 'Táctica - ' . now()->format('d/m H:i')
        ]);
        
        return back()->with('message', 'Táctica guardada en historial.');
    }

    public function mostrarPartido($id)
    {
        $partido = Partido::with('torneo.categoria')->findOrFail($id);
        
        // Obtenemos alineaciones y historial unificados
        $alineaciones = Alineacion::where('partido_id', $id)->with('jugador')->get();
        $historial = Tactica::where('partido_id', $id)->latest()->get();

        return Inertia::render('Torneos/PartidoDetalle', [
            'partido' => [
                'id' => $partido->id,
                'rival' => $partido->rival,
                'fecha' => $partido->fecha,
                'analisis_general' => $partido->analisis_general
            ],
            'alineacionesExistentes' => $alineaciones,
            'convocados' => $alineaciones, // Por compatibilidad
            'jugadores' => \App\Models\Jugador::all(),
            'historial' => $historial 
        ]);
    }

    public function notificarWhatsApp(Partido $partido)
    {
        $mensaje = "⚽ *PARTIDO GUTIGOL*%0A%0A" .
                   "🆚 Rival: " . $partido->rival . "%0A" .
                   "📅 Fecha: " . $partido->fecha . "%0A" .
                   "🕒 Hora: " . $partido->hora . "%0A" .
                   "📍 Lugar: " . $partido->lugar . "%0A%0A" .
                   "¡Te esperamos para alentar!";

        $urlWhatsApp = "https://wa.me/?text=" . $mensaje;

        return Inertia::location($urlWhatsApp);
    }
}