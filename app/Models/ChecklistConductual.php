<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistConductual extends Model
{
    use HasFactory;

    protected $table = 'checklists_conductuales';

    protected $fillable = [
        'entrenamiento_id',
        'jugador_id',
        'entrenador_id',
        'aislamiento_social',
        'frustracion_extrema',
        'agresividad',
        'derivado_automatico'
    ];

    protected $casts = [
        'aislamiento_social' => 'boolean',
        'frustracion_extrema' => 'boolean',
        'agresividad' => 'boolean',
        'derivado_automatico' => 'boolean'
    ];

    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'jugador_id');
    }

    public function entrenamiento(): BelongsTo
    {
        return $this->belongsTo(Entrenamiento::class, 'entrenamiento_id');
    }
}