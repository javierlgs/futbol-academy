<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Jugador;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    /**
     * Muestra la lista de pagos y las estadísticas generales.
     */
    public function index(Request $request)
    {
        $pagos = Pago::with('jugador')->latest()->paginate(10);
        
        // Estadísticas para las tarjetas (Bento Grid superiores)
        $stats = [
            'total' => Pago::sum('monto'),
            'puntuales' => Pago::where('estado', 'procesado')->count(),
            'atrasados' => Pago::where('estado', 'pendiente')->where('fecha_pago', '<', now())->count(),
        ];

        return Inertia::render('Finanzas/Pagos', [
            'pagos' => $pagos,
            'stats' => $stats
        ]);
    }

    /**
     * Registra un nuevo pago en el sistema.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jugador_id' => 'required|exists:jugadores,id',
            'monto' => 'required|numeric|min:0',
            'concepto' => 'required|string',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'comprobante' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $pago = new Pago($validated);
            
            if ($request->hasFile('comprobante')) {
                $path = $request->file('comprobante')->store('comprobantes', 'public');
                $pago->comprobante_path = $path;
            }

            $pago->estado = 'procesado';
            $pago->save();
        });

        return redirect()->back()->with('message', 'Pago registrado exitosamente.');
    }

    /**
     * Genera un recibo PDF para un pago específico.
     */
    public function generarRecibo(Pago $pago)
    {
        $pago->load('jugador');
        
        $pdf = Pdf::loadView('pdf.recibo', ['pago' => $pago]);
        
        return $pdf->download('recibo_pago_' . $pago->id . '.pdf');
    }

    /**
     * Elimina un registro de pago.
     */
    public function destroy(Pago $pago)
    {
        $pago->delete();
        return redirect()->back()->with('message', 'Registro eliminado correctamente.');
    }

    /**
     * Busca jugadores por nombre o apellido para el selector.
     */
    public function buscarJugador(Request $request)
    {
        $query = $request->get('q');
        return \App\Models\Jugador::where('nombres', 'like', "%{$query}%")
            ->orWhere('apellidos', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'nombres', 'apellidos']);
    }
}