<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->fecha ? Carbon::parse($request->fecha) : today();
        
        $movimientos = CajaMovimiento::with(['user', 'factura', 'gasto'])
            ->whereDate('fecha', $fecha)
            ->orderBy('id', 'asc')
            ->get();

        $apertura = $movimientos->where('tipo', 'APERTURA')->first();
        $cierre = $movimientos->where('tipo', 'CIERRE')->first();
        
        $totales = [
            'ingresos' => $movimientos->where('tipo', 'INGRESO')->sum('monto'),
            'egresos' => $movimientos->where('tipo', 'EGRESO')->sum('monto'),
            'saldo_actual' => $movimientos->last()->saldo ?? 0,
            'apertura' => $apertura?->monto ?? 0,
            'cierre' => $cierre?->monto ?? null
        ];

        $cajaAbierta = $apertura &&!$cierre;

        return Inertia::render('Caja/Index', [
            'movimientos' => $movimientos,
            'totales' => $totales,
            'fecha' => $fecha->format('Y-m-d'),
            'cajaAbierta' => $cajaAbierta,
            'hayApertura' => !!$apertura
        ]);
    }

    public function abrir(Request $request)
    {
        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
            'observacion' => 'nullable|string'
        ]);

        if (CajaMovimiento::hayCajaAbierta()) {
            return back()->with('error', 'Ya existe una caja abierta hoy');
        }

        CajaMovimiento::create([
            'fecha' => today(),
            'tipo' => 'APERTURA',
            'concepto' => 'Apertura de Caja',
            'monto' => $data['monto'],
            'saldo' => $data['monto'],
            'metodo' => 'EFECTIVO',
            'observacion' => $data['observacion'],
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Caja abierta correctamente');
    }

    public function store(Request $request)
    {
        if (!CajaMovimiento::hayCajaAbierta()) {
            return back()->with('error', 'Debes abrir la caja primero');
        }

        $data = $request->validate([
            'tipo' => 'required|in:INGRESO,EGRESO',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'metodo' => 'required|in:EFECTIVO,TRANSFERENCIA,TARJETA,CHEQUE',
            'observacion' => 'nullable|string'
        ]);

        $saldoAnterior = CajaMovimiento::getSaldoActual();
        
        $nuevoSaldo = $data['tipo'] === 'INGRESO' 
            ? $saldoAnterior + $data['monto']
            : $saldoAnterior - $data['monto'];

        if ($nuevoSaldo < 0) {
            return back()->with('error', 'Saldo insuficiente en caja');
        }

        CajaMovimiento::create([
            'fecha' => today(),
            'tipo' => $data['tipo'],
            'concepto' => $data['concepto'],
            'monto' => $data['monto'],
            'saldo' => $nuevoSaldo,
            'metodo' => $data['metodo'],
            'observacion' => $data['observacion'],
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Movimiento registrado');
    }

    public function cerrar(Request $request)
    {
        $data = $request->validate([
            'monto_fisico' => 'required|numeric|min:0',
            'observacion' => 'nullable|string'
        ]);

        $saldoSistema = CajaMovimiento::getSaldoActual();
        $diferencia = $data['monto_fisico'] - $saldoSistema;

        CajaMovimiento::create([
            'fecha' => today(),
            'tipo' => 'CIERRE',
            'concepto' => 'Cierre de Caja',
            'monto' => $data['monto_fisico'],
            'saldo' => $data['monto_fisico'],
            'metodo' => 'EFECTIVO',
            'observacion' => $data['observacion'] . ($diferencia != 0 ? " | Diferencia: $" . number_format($diferencia, 2) : ''),
            'user_id' => auth()->id()
        ]);

        return back()->with('success', 'Caja cerrada. Diferencia: $' . number_format($diferencia, 2));
    }
}