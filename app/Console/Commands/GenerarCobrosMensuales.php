<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jugador;
use App\Models\Pago;
use App\Models\Configuracion;
use App\Services\WhatsAppService; // Importación del servicio
use Carbon\Carbon;

class GenerarCobrosMensuales extends Command
{
    protected $signature = 'cobros:generar';
    protected $description = 'Genera cobros usando el monto y fecha configurados';

    public function handle()
    {
        $config = Configuracion::first();
        if (!$config) {
            $this->error('No hay configuración definida.');
            return;
        }

        // Validación de fecha
        if (Carbon::now()->day != $config->dia_cobro_automatico) {
            return;
        }

        $jugadores = Jugador::where('estado', 'activo')->get();
        $notificador = new WhatsAppService();

        foreach ($jugadores as $jugador) {
            $pago = Pago::create([
                'jugador_id' => $jugador->id,
                'monto' => $config->monto_pension_default,
                'concepto' => 'Pensión ' . Carbon::now()->format('F Y'),
                'fecha_pago' => Carbon::now(),
                'estado' => 'pendiente',
                'metodo_pago' => 'pendiente'
            ]);

            // Enviar notificación después de crear el registro
            $notificador->enviarNotificacionCobro($pago);
        }

        $this->info("Cobros generados con monto de: {$config->monto_pension_default}");
    }
}