<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionPsicologica extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_psicologicas';

    protected $fillable = [
        'jugador_id',
        'psicologo_id',
        'observaciones_clinicas',
        'puntaje_ansiedad',
        'puntaje_depresion',
        'puntaje_autoestima',
        'recomendaciones_entrenador',
        'recomendaciones_padres',
        'semaforo_riesgo',
        'alerta_coordinacion',
    ];

    protected $casts = [
        'alerta_coordinacion' => 'boolean',
        'puntaje_ansiedad' => 'integer',
        'puntaje_depresion' => 'integer',
        'puntaje_autoestima' => 'integer',
    ];

    /**
     * Relación con el jugador evaluado.
     */
    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'jugador_id');
    }

    /**
     * Relación con el psicólogo que realizó la evaluación.
     */
    public function psicologo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psicologo_id');
    }
}