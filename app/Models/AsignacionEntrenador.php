<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionEntrenador extends Model
{
    // Forzamos el nombre exacto de la tabla de la migración
    protected $table = 'asignaciones_entrenadores';

    protected $fillable = [
        'categoria_id',
        'user_id',
        'dias',
        'hora',
        'cancha',
    ];

    /**
     * Obtiene la categoría a la que pertenece esta asignación de horario.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Obtiene el usuario (profesor) responsable de este bloque.
     */
    public function entrenador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}