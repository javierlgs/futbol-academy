<?php
namespace App\Http\Controllers;
use App\Models\Entrenamiento;
use App\Models\FotoEntrenamiento;
use App\Models\Jugador;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GaleriaController extends Controller {
    public function index(Entrenamiento $entrenamiento) {
        $fotos = FotoEntrenamiento::where('entrenamiento_id', $entrenamiento->id)
            ->where('visible_padres', true)
            ->with('usuario')
            ->latest()
            ->get();

        return Inertia::render('Galeria/Index', [
            'entrenamiento' => $entrenamiento->load('categoria'),
            'fotos' => $fotos
        ]);
    }

    public function store(Request $request, Entrenamiento $entrenamiento) {
        $request->validate([
            'fotos.*' => 'required|image|max:5120',
            'descripcion' => 'nullable|string'
        ]);

        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('galeria/' . $entrenamiento->id, 'public');
            FotoEntrenamiento::create([
                'entrenamiento_id' => $entrenamiento->id,
                'uploaded_by' => auth()->id(),
                'ruta' => $ruta,
                'descripcion' => $request->descripcion,
                'visible_padres' => true
            ]);
        }

        return back()->with('success', 'Fotos subidas');
    }

    public function galeriaPadre() {
        $jugadores = auth()->user()->jugadoresAsociados;
        $jugadorIds = $jugadores->pluck('id');

        $fotos = FotoEntrenamiento::whereHas('entrenamiento.jugadores', function($q) use ($jugadorIds) {
                $q->whereIn('jugador_id', $jugadorIds);
            })
            ->where('visible_padres', true)
            ->with(['entrenamiento.categoria', 'usuario'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Galeria/Padre', compact('fotos', 'jugadores'));
    }
}