<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Gasto;
use App\Models\CajaMovimiento;
use App\Models\Jugador;
use App\Models\GastoCategoria;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->get('filtro', 'mes_actual');
        $fechaInicio = now()->startOfMonth();
        $fechaFin = now()->endOfMonth();

        if ($filtro == 'mes_anterior') {
            $fechaInicio = now()->subMonth()->startOfMonth();
            $fechaFin = now()->subMonth()->endOfMonth();
        } elseif ($filtro == 'ultimos_3') {
            $fechaInicio = now()->subMonths(3)->startOfMonth();
            $fechaFin = now()->endOfMonth();
        }

        // Métricas
        $ingresos = Factura::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->where('estado_sri', 'AUTORIZADA')
            ->sum('total');

        $gastos = Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('total');
        
        $utilidad = $ingresos - $gastos;

        $saldoCaja = CajaMovimiento::whereDate('fecha', now())->latest('id')->value('saldo') ?? 0;

        $clientesActivos = Jugador::where('activo', 1)
            ->distinct('rep_cedula')
            ->count('rep_cedula');

        $facturasMes = Factura::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])->count();

        $metricas = [
            'ingresos_mes' => $ingresos,
            'gastos_mes' => $gastos,
            'utilidad_mes' => $utilidad,
            'saldo_caja' => $saldoCaja,
            'clientes_activos' => $clientesActivos,
            'facturas_mes' => $facturasMes,
            'filtro_actual' => $filtro
        ];

        // Top 5 Jugadores con Eloquent y Join
        $topJugadores = Jugador::join('categorias', 'jugadores.categoria_id', '=', 'categorias.id')
            ->leftJoin('evaluaciones_partido', function($join) use ($fechaInicio, $fechaFin) {
                $join->on('jugadores.id', '=', 'evaluaciones_partido.jugador_id')
                     ->whereBetween('evaluaciones_partido.created_at', [$fechaInicio, $fechaFin]);
            })
            ->where('jugadores.activo', 1)
            ->select(
                'jugadores.id',
                DB::raw("CONCAT(jugadores.nombres, ' ', jugadores.apellidos) as nombre"),
                DB::raw("CONCAT(categorias.año_nacimiento, ' - ', categorias.sexo) as categoria"),
                DB::raw('TIMESTAMPDIFF(YEAR, jugadores.fecha_nacimiento, CURDATE()) as edad'),
                DB::raw('COALESCE(ROUND(AVG(evaluaciones_partido.rendimiento_general), 1), 0) as promedio')
            )
            ->groupBy('jugadores.id', 'jugadores.nombres', 'jugadores.apellidos', 'categorias.año_nacimiento', 'categorias.sexo', 'jugadores.fecha_nacimiento')
            ->orderByDesc('promedio')
            ->limit(5)
            ->get();

        // Top Clientes con filtro
        $topClientes = DB::table('facturas')
            ->join('jugadores', 'facturas.jugador_id', '=', 'jugadores.id')
            ->select(
                DB::raw("CONCAT(jugadores.rep_nombres, ' ', jugadores.rep_apellidos) as nombre"),
                DB::raw('SUM(facturas.total) as total')
            )
            ->whereBetween('facturas.fecha_emision', [$fechaInicio, $fechaFin])
            ->where('facturas.estado_sri', 'AUTORIZADA')
            ->groupBy('jugadores.rep_cedula', 'jugadores.rep_nombres', 'jugadores.rep_apellidos')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $movimientosRecientes = CajaMovimiento::whereDate('fecha', now())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'metricas' => $metricas,
            'topJugadores' => $topJugadores,
            'topClientes' => $topClientes,
            'movimientosRecientes' => $movimientosRecientes,
        ]);
    }
}