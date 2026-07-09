<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alineacion extends Model
{
    protected $table = 'alineaciones';
    protected $fillable = [
        'jugador_id', 'partido_id', 'tipo', 
        'minuto_entrada', 'minuto_salida', 'posicion_partido'
    ];

    public function jugador() { return $this->belongsTo(Jugador::class); }
    public function partido() { return $this->belongsTo(Partido::class); }
}