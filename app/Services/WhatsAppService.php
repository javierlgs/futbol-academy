<?php

namespace App\Services;

use App\Models\Pago;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía una notificación de nuevo cobro generado vía WhatsApp.
     */
    public function enviarNotificacionCobro(Pago $pago): bool
    {
        // Cargamos al jugador y a su representante
        $pago->load('jugador.representante');
        $representante = $pago->jugador->representante;

        if (!$representante || !$representante->telefono) {
            Log::warning("No se pudo enviar WhatsApp: Representante o teléfono no encontrado para el pago ID: {$pago->id}");
            return false;
        }

        $mensaje = "Hola {$representante->nombres}, le informamos que se ha generado la pensión del mes correspondiente para {$pago->jugador->nombres}. " .
                   "El valor a cancelar es de $ {$pago->monto}. " .
                   "Puede realizar su pago a través de nuestros canales oficiales.";

        // Aquí integrarías tu API de WhatsApp (ej: Twilio, Waha, o un gateway local)
        return $this->enviarMensaje($representante->telefono, $mensaje);
    }

    protected function enviarMensaje(string $telefono, string $mensaje): bool
    {
        Log::info("Enviando WhatsApp al número {$telefono}: {$mensaje}");
        
        // Simulación de envío por API
        // $response = Http::post('https://api.tu-servicio-whatsapp.com/send', [
        //     'phone' => $telefono,
        //     'message' => $mensaje
        // ]);
        
        return true;
    }
}