<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneo extends Model
{
    protected $fillable = [
        'nombre',
        'categoria_id',
        'estado',
    ];

    /**
     * Obtiene la categoría asignada al torneo.
     */
  public function categoria(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(Categoria::class, 'categoria_id');
}

    /**
     * Obtiene todos los partidos agendados en este torneo.
     */
    public function partidos(): HasMany
    {
        return $this->hasMany(Partido::class);
    }
    public function jugadores()
{
    return $this->belongsToMany(Jugador::class, 'torneo_jugador');
}
}