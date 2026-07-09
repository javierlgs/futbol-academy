<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categoria extends Model
{
    protected $table = 'categorias';
    
    protected $fillable = ['año_nacimiento', 'sexo', 'sede_id', 'activo'];
    
    protected $casts = ['activo' => 'boolean'];
    
    protected $appends = ['nombre'];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function jugadores(): HasMany
    {
        return $this->hasMany(Jugador::class);
    }

    public function getNombreAttribute(): string
    {
        return $this->año_nacimiento . ' - ' . $this->sexo;
    }
}