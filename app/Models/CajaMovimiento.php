<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaMovimiento extends Model
{
    protected $fillable = [
        'fecha', 'tipo', 'concepto', 'monto', 'saldo', 
        'metodo', 'observacion', 'factura_id', 'gasto_id', 'user_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'saldo' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class);
    }

    public static function getSaldoActual(): float
    {
        return self::whereDate('fecha', today())
            ->latest('id')
            ->value('saldo') ?? 0;
    }

    public static function hayCajaAbierta(): bool
    {
        return self::whereDate('fecha', today())
            ->where('tipo', 'APERTURA')
            ->exists();
    }
}