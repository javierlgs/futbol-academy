<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\GastoCategoria;
use App\Models\Proveedor;
use App\Models\IvaTarifa;
use App\Models\CajaMovimiento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Gasto::with(['categoria', 'proveedor', 'user'])
            ->orderBy('fecha', 'desc');

        if ($request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->desde) {
            $query->where('fecha', '>=', $request->desde);
        }

        if ($request->hasta) {
            $query->where('fecha', '<=', $request->hasta);
        }

        $gastos = $query->paginate(20)->withQueryString();

        $totales = [
            'total' => Gasto::sum('total'),
            'mes_actual' => Gasto::whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('total'),
            'iva_total' => Gasto::sum('iva')
        ];

        return Inertia::render('Gastos/Index', [
            'gastos' => $gastos,
            'categorias' => GastoCategoria::all(),
            'totales' => $totales,
            'filters' => $request->only(['categoria_id', 'desde', 'hasta'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Gastos/Create', [
            'categorias' => GastoCategoria::all(),
            'proveedores' => Proveedor::all(),
            'ivaActual' => IvaTarifa::getPorDefecto()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id' => 'required|exists:gasto_categorias,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'numero_factura' => 'nullable|string|max:50',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tiene_iva' => 'required|boolean',
            'forma_pago' => 'required|in:EFECTIVO,TRANSFERENCIA,TARJETA,CHEQUE',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $ivaTarifa = IvaTarifa::getPorDefecto();
        $iva = $data['tiene_iva'] ? $data['subtotal'] * ($ivaTarifa->porcentaje / 100) : 0;
        
        $gasto = Gasto::create([
            ...$data,
            'iva' => $iva,
            'total' => $data['subtotal'] + $iva,
            'user_id' => auth()->id()
        ]);

        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('gastos', 'public');
            $gasto->update(['comprobante' => $path]);
        }

        // Registrar egreso en caja si es efectivo
        if ($data['forma_pago'] === 'EFECTIVO') {
            $saldoAnterior = CajaMovimiento::getSaldoActual();
            
            CajaMovimiento::create([
                'fecha' => $gasto->fecha,
                'tipo' => 'EGRESO',
                'concepto' => $gasto->descripcion,
                'monto' => $gasto->total,
                'saldo' => $saldoAnterior - $gasto->total,
                'metodo' => 'EFECTIVO',
                'gasto_id' => $gasto->id,
                'user_id' => auth()->id()
            ]);
        }

        return redirect()->route('gastos.index')->with('success', 'Gasto registrado');
    }

    public function destroy(Gasto $gasto)
    {
        if ($gasto->comprobante) {
            Storage::disk('public')->delete($gasto->comprobante);
        }
        
        $gasto->delete();
        return back()->with('success', 'Gasto eliminado');
    }
}