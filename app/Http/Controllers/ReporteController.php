<?php
namespace App\Http\Controllers;
use App\Models\Jugador;
use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller {
    public function jugadorPDF(Jugador $jugador) {
        $jugador->load(['categoria', 'evaluaciones.habilidad', 'evaluacionesPsicologicas']);
        
        $pdf = Pdf::loadView('pdf.reporte-jugador', compact('jugador'));
        return $pdf->download("reporte-{$jugador->nombre_completo}.pdf");
    }

    public function facturaPDF(Factura $factura) {
        $factura->load(['jugador', 'sede']);
        $pdf = Pdf::loadView('pdf.factura', compact('factura'));
        return $pdf->download("factura-{$factura->numero_factura}.pdf");
    }

    public function whatsappReporte(Jugador $jugador) {
        $mensaje = "Hola {$jugador->rep_nombres}, aquí está el reporte de {$jugador->nombre_completo}. ".
                   "Promedio técnico: {$jugador->promedio_tecnico}/5. ".
                   "Descarga: " . route('reporte.jugador.pdf', $jugador);
        
        $telefono = preg_replace('/[^0-9]/', '', $jugador->rep_telefono);
        $url = "https://wa.me/593{$telefono}?text=" . urlencode($mensaje);
        
        return redirect()->away($url);
    }
}