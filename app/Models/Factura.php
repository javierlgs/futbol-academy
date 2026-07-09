<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model {
    protected $fillable = [
        'jugador_id', // Ajustado a jugador_id por consistencia
        'numero_factura', 'clave_acceso', 'numero_autorizacion',
        'fecha_autorizacion', 'ambiente', 'estado_sri', 
        'cliente_nombres', 'cliente_apellidos', // Separados para el accessor
        'cliente_identificacion', 'cliente_tipo_identificacion', 'cliente_direccion',
        'cliente_telefono', 'cliente_email', 'subtotal_0', 'subtotal_gravado',
        'iva_valor', 'iva_porcentaje_usado', 'iva_codigo_sri', 'total', 
        'estado_pago', 'metodo_pago', 'concepto', 'mes_pension', 
        'observaciones_sri', 'xml_generado', 'xml_autorizado'
    ];

    protected $casts = [
        'fecha_autorizacion' => 'datetime',
        'subtotal_0' => 'decimal:2',
        'subtotal_gravado' => 'decimal:2',
        'iva_valor' => 'decimal:2',
        'iva_porcentaje_usado' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'jugador_id');
    }

    // Accessor para nombre del cliente/representante
    public function getClienteNombreAttribute()
    {
        return $this->cliente_nombres . ' ' . $this->cliente_apellidos;
    }

    public function notasCredito() {
        return $this->hasMany(NotaCredito::class);
    }
}