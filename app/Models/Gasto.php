<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model {
    protected $fillable = [
        'categoria_id', 'proveedor_id', 'numero_factura', 'descripcion',
        'fecha', 'subtotal', 'iva', 'total', 'forma_pago', 
        'comprobante', 'tiene_iva', 'user_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'tiene_iva' => 'boolean',
    ];

    public function categoria() {
        return $this->belongsTo(GastoCategoria::class, 'categoria_id');
    }

    public function proveedor() {
        return $this->belongsTo(Proveedor::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}