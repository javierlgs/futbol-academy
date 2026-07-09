<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_escuela', 
        'logo', 
        'color_primario', 
        'color_secundario',
        'direccion_principal', 
        'telefono', 
        'correo', 
        'iva', 
        'ruc', 
        'plantillas_whatsapp',
        'modulo_facturas_habilitado' // Nuevo campo para control de flujo
    ];

    protected $casts = [
        'plantillas_whatsapp' => 'array',
        'modulo_facturas_habilitado' => 'boolean',
    ];

    /**
     * Verifica si el sistema debe emitir Factura Electrónica
     * o Recibo Simple según configuración.
     */
    public function esFacturaElectronica(): bool
    {
        return (bool) $this->modulo_facturas_habilitado;
    }
}