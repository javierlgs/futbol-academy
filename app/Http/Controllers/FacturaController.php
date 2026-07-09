<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Jugador;
use App\Models\Setting;
use App\Models\Banco;
use App\Models\IvaTarifa;
use App\Models\CajaMovimiento;
use App\Services\SRIService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller {
    
    public function create() {
        $jugadores = Jugador::select(
            'id', 
            'nombres', 
            'apellidos', 
            'rep_nombres', 
            'rep_apellidos',
            'rep_cedula',
            'rep_telefono',
            'rep_correo',
            'rep_direccion'
        )->where('activo', true)->get();

        $ivaActual = IvaTarifa::getVigente();
        $bancos = Banco::where('activo', true)->get();

        return Inertia::render('Facturas/Create', [
            'jugadores' => $jugadores,
            'iva_tarifa' => $ivaActual,
            'bancos' => $bancos,
            'facturacionActiva' => setting('facturacion_electronica_activa') == '1'
        ]);
    }

    public function store(Request $request, Jugador $alumno) {
        // Se expande la validación para aceptar los datos editados o enviados del representante
        $data = $request->validate([
            'concepto' => 'required|string',
            'mes_pension' => 'nullable|string',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,deposito',
            'subtotal' => 'required|numeric|min:0',
            'cliente_nombres' => 'required|string',
            'cliente_apellidos' => 'required|string',
            'cliente_cedula' => 'required|string',
            'cliente_telefono' => 'nullable|string',
            'cliente_email' => 'nullable|email',
            'cliente_direccion' => 'required|string',
            'banco_id' => 'nullable|exists:bancos,id'
        ]);

        DB::beginTransaction();
        try {
            $ivaTarifa = IvaTarifa::getPorDefecto();
            if (!$ivaTarifa) {
                throw new \Exception('No hay tarifa de IVA configurada. Configúrala en Settings.');
            }

            $ivaPorcentaje = (float) $ivaTarifa->porcentaje;
            $ivaValor = round($data['subtotal'] * ($ivaPorcentaje / 100), 2);
            $total = round($data['subtotal'] + $ivaValor, 2);

            // CORRECCIÓN CRÍTICA: Se cambia 'alumno_id' por 'jugador_id' acorde a tus relaciones e index
            $factura = Factura::create([
                'jugador_id' => $alumno->id,
                'numero_factura' => $this->generarNumero(),
                'subtotal_gravado' => $data['subtotal'],
                'iva_valor' => $ivaValor,
                'iva_porcentaje_usado' => $ivaPorcentaje,
                'iva_codigo_sri' => $ivaTarifa->codigo_sri,
                'total' => $total,
                'metodo_pago' => $data['metodo_pago'],
                'concepto' => $data['concepto'],
                'mes_pension' => $data['mes_pension'],
                'estado_pago' => 'pagada',
                'cliente_nombre' => $data['cliente_nombres'] . ' ' . $data['cliente_apellidos'],
                'cliente_identificacion' => $data['cliente_cedula'],
                'cliente_tipo_identificacion' => '05',
                'cliente_direccion' => $data['cliente_direccion'],
                'cliente_telefono' => $data['cliente_telefono'],
                'cliente_email' => $data['cliente_email'],
                'clave_acceso' => '0000000000000',
                'ambiente' => setting('ambiente_sri'),
                'estado_sri' => 'PENDIENTE',
                'banco_id' => $data['banco_id']
            ]);

            if ($request->metodo_pago === 'efectivo') {
                $saldoAnterior = CajaMovimiento::getSaldoActual();
                
                CajaMovimiento::create([
                    'fecha' => now(),
                    'tipo' => 'INGRESO',
                    'concepto' => "Factura #{$factura->numero_factura}",
                    'monto' => $factura->total,
                    'saldo' => $saldoAnterior + $factura->total,
                    'metodo' => 'EFECTIVO',
                    'factura_id' => $factura->id,
                    'user_id' => auth()->id()
                ]);
            }

            Setting::set('secuencial_factura', (int)setting('secuencial_factura') + 1);

            if (setting('facturacion_electronica_activa') == '1') {
                try {
                    $sri = new SRIService();
                    $sri->generarFactura($factura);
                } catch (\Exception $e) {
                    $factura->update(['observaciones_sri' => $e->getMessage(), 'estado_sri' => 'ERROR']);
                }
            }

            DB::commit();
            return redirect()->route('facturas.show', $factura->id)->with('success', 'Factura creada con IVA ' . $ivaPorcentaje . '%');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function generarNumero() {
        $secuencial = str_pad(setting('secuencial_factura'), 9, '0', STR_PAD_LEFT);
        return setting('codigo_establecimiento') . '-' . setting('codigo_punto_emision') . '-' . $secuencial;
    }

    public function show(Factura $factura) {
        return Inertia::render('Facturas/Show', [
            'factura' => $factura->load(['jugador', 'sede']),
            'settings' => Setting::all()->pluck('value', 'key'),
            'bancos' => Banco::where('activo', true)->get()
        ]);
    }

    public function pdf(Factura $factura) {
        $factura->load(['jugador', 'sede']);
        $settings = Setting::all()->pluck('value', 'key');
        $bancos = Banco::where('activo', true)->get();
        
        $pdf = Pdf::loadView('pdf.ride', compact('factura', 'settings', 'bancos'));
        return $pdf->stream("factura-{$factura->numero_factura}.pdf");
    }

    public function index(Request $request) {
        // Traemos jugador con sus datos correspondientes para el listado y alertas
        $query = Factura::with(['jugador.categoria.sede'])
            ->when($request->estado_sri, fn($q, $estado) => $q->where('estado_sri', $estado))
            ->when($request->mes, fn($q, $mes) => $q->whereMonth('created_at', \Carbon\Carbon::parse($mes)->month))
            ->when($request->buscar, function($q, $buscar) {
                $q->where('numero_factura', 'like', "%{$buscar}%")
                  ->orWhere('cliente_nombre', 'like', "%{$buscar}%")
                  ->orWhere('cliente_identificacion', 'like', "%{$buscar}%");
            })
            ->latest();

        $facturas = $query->paginate(20)->withQueryString();

        return Inertia::render('Facturas/Index', [
            'facturas' => $facturas,
            'filtros' => $request->only(['estado_sri', 'mes', 'buscar'])
        ]);
    }

    public function reenviarSRI(Factura $factura) {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403);
        }

        try {
            $sri = new \App\Services\SRIService();
            $sri->generarFactura($factura);
            return back()->with('success', 'Factura reenviada al SRI');
        } catch (\Exception $e) {
            return back()->with('error', 'Error SRI: ' . $e->getMessage());
        }
    }

    public function anular(Request $request, Factura $factura) {
        if ($factura->estado_sri !== 'AUTORIZADO') {
            return back()->with('error', 'Solo puedes anular facturas autorizadas');
        }

        $data = $request->validate([
            'motivo' => 'required|in:01,02,03,04',
            'detalle' => 'required|string|max:300'
        ]);

        try {
            $sri = new SRIService();
            $nota = $sri->generarNotaCredito($factura, $data['motivo'], $data['detalle']);
            
            return redirect()->route('facturas.show', $factura->id)
                ->with('success', 'Nota de crédito generada y enviada al SRI');
        } catch (\Exception $e) {
            return back()->with('error', 'Error SRI: ' . $e->getMessage());
        }
    }

    public function reporteMensual(Request $request) {
        $mes = $request->mes ?? now()->month;
        $año = $request->año ?? now()->year;
        
        $facturas = Factura::where('estado_sri', 'AUTORIZADO')
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $año)
            ->with('jugador')
            ->get();
        
        $totalFacturado = $facturas->sum('total');
        $totalIva = $facturas->sum('iva_valor');
        
        $pdf = Pdf::loadView('pdf.reporte-facturas', compact('facturas', 'mes', 'año', 'totalFacturado', 'totalIva'));
        
        return $pdf->stream("reporte-facturas-{$mes}-{$año}.pdf");
    }

    public function reporteVista()
    {
        return Inertia::render('Facturas/Reporte', [
            'facturas' => Factura::whereMonth('created_at', now()->month)->get()
        ]);
    }
}