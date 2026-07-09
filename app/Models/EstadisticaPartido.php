<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadisticaPartido extends Model
{
    protected $table = 'estadisticas_partidos';

    protected $fillable = [
        'partido_id',
        'jugador_id',
        'convocado',
        'goles',
        'asistencias',
        'minutos',
    ];

    // Casteo automático para booleanos de control en el frontend de Inertia
    protected $casts = [
        'convocado' => 'boolean',
    ];

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class);
    }

    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class);
    }
}