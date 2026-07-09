<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Log;

class FacturacionService
{
    protected $config;

    public function __construct()
    {
        $this->config = Configuracion::first() ?? new Configuracion();
    }

    /**
     * Procesa la emisión de un comprobante según la configuración del sistema.
     * * @param Pago $pago
     * @return array
     */
    public function emitirComprobante(Pago $pago): array
    {
        if ($this->config->esFacturaElectronica()) {
            return $this->procesarFacturaElectronica($pago);
        }

        return $this->generarReciboSimple($pago);
    }

    /**
     * Lógica para Facturación Electrónica (SRI / API externa)
     */
    protected function procesarFacturaElectronica(Pago $pago): array
    {
        // Aquí se integraría la lógica con el proveedor de facturación (ej: soap/rest)
        Log::info("Emitiendo factura electrónica para el pago: " . $pago->id);
        
        return [
            'tipo' => 'electronica',
            'status' => 'success',
            'documento' => 'FAC-' . str_pad($pago->id, 9, '0', STR_PAD_LEFT)
        ];
    }

    /**
     * Lógica para Recibo Simple (PDF Interno)
     */
    protected function generarReciboSimple(Pago $pago): array
    {
        Log::info("Generando recibo simple para el pago: " . $pago->id);
        
        return [
            'tipo' => 'recibo',
            'status' => 'success',
            'documento' => 'REC-' . str_pad($pago->id, 9, '0', STR_PAD_LEFT)
        ];
    }
}