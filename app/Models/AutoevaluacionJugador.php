<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoevaluacionJugador extends Model
{
    use HasFactory;

    protected $table = 'autoevaluaciones_jugadores';

    protected $fillable = [
        'jugador_id', 
        'entrenamiento_id', 
        'animo_entrenamiento', 
        'calidad_sueno', 
        'siente_dolor', 
        'diversion', 
        'cansancio_extremo'
    ];

    public function jugador() 
    {
        return $this->belongsTo(Jugador::class, 'jugador_id');
    }

    public function entrenamiento() 
    {
        return $this->belongsTo(Entrenamiento::class, 'entrenamiento_id');
    }
}