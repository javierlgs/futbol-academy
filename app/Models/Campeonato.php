<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campeonato extends Model
{
    protected $fillable = [
        'sede_id',
        'nombre',
        'tipo',
        'año_base',
        'años_habilitados',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'años_habilitados' => 'array',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function jugadoresHabilitados()
    {
        if (!$this->años_habilitados) return collect();
        
        return Jugador::where('sede_id', $this->sede_id)
            ->where('activo', true)
            ->whereHas('categoria', function($q) {
                $q->whereIn('año_nacimiento', $this->años_habilitados);
            })
            ->with('categoria')
            ->get();
    }
}