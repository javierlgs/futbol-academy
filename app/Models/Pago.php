<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'jugador_id',
        'monto',
        'concepto',
        'fecha_pago',
        'estado',
        'metodo_pago',
        'comprobante_path'
    ];

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
    public function scopeMorosos($query) {
    return $query->where('estado', 'pendiente')
                 ->where('fecha_pago', '<', now());
}
}