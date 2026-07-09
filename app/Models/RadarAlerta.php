<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadarAlerta extends Model
{
    protected $fillable = ['jugador_id', 'tipo', 'severidad', 'mensaje', 'leido'];

    public function jugador() { return $this->belongsTo(Jugador::class); }
}