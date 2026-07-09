<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Models\Evaluacion;

class Jugador extends Model 
{
    use HasFactory;

    protected $table = 'jugadores'; 

    protected $fillable = [
        'sede_id', 
        'categoria_id', 
        'nombres', 
        'apellidos', 
        'fecha_nacimiento', 
        'sexo', 
        'foto',
        'posicion', 
        'estado', 
        'observaciones_medicas', 
        'rep_nombres', 
        'rep_apellidos',
        'rep_relacion', 
        'rep_telefono', 
        'rep_correo', 
        'rep_direccion', 
        'rep_cedula',
        'datos_facturacion', 
        'padre_user_id', 
        'activo'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
        'datos_facturacion' => 'array',
    ];

    // Se agregó 'año_nacimiento_calculado' a los appends
    protected $appends = ['nombre_completo', 'año_nacimiento_calculado'];

    public function categoria(): BelongsTo 
    { 
        return $this->belongsTo(Categoria::class)->withDefault();
    }

    public function sede(): BelongsTo 
    { 
        return $this->belongsTo(Sede::class)->withDefault();
    }

    // Accessor corregido y seguro
    public function getEdadAttribute(): int 
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : 0;
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute(): string 
    {
        return trim(($this->nombres ?? '') . ' ' . ($this->apellidos ?? ''));
    }

    // Nuevo Accessor para calcular el año de nacimiento
    public function getAñoNacimientoCalculadoAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->format('Y') : 'N/A';
    }

    // Scope para campeonatos
    public function scopeHabilitados($query, $añoCategoria, $sexo) 
    {
        return $query->whereHas('categoria', function($q) use ($añoCategoria, $sexo) {
                $q->where('año_nacimiento', '<=', $añoCategoria)
                  ->whereIn('sexo', [$sexo, 'Mixto']);
            })
            ->where('sexo', $sexo)
            ->where('activo', 1);
    }

    public function scopeDeSede($query, $sedeId) 
    {
        return $query->where('sede_id', $sedeId);
    }

    public function scopeActivos($query) 
    {
        return $query->where('activo', 1);
    }

    public function asistencias() {
        return $this->hasMany(AsistenciaEntrenamiento::class, 'jugador_id');
    }
    
    public function evaluaciones()
{
    return $this->hasMany(Evaluacion::class, 'jugador_id');
}
}