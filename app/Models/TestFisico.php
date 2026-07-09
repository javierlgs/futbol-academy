<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestFisico extends Model
{
    use HasFactory;

    protected $table = 'tests_fisicos';

    protected $fillable = [
        'jugador_id', 
        'fecha', 
        'velocidad_segundos', 
        'coordinacion_puntos', 
        'reaccion_segundos', 
        'equilibrio_segundos', 
        'resistencia_vueltas'
    ];

    public function jugador() 
    {
        return $this->belongsTo(Jugador::class, 'jugador_id');
    }
}