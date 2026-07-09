<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model {
    protected $fillable = ['categoria_id','hora_inicio','hora_fin','sede_id', 'user_id', 'fecha', 'hora_inicio', 'hora_fin', 'objetivo', 'estado'];

    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function entrenador() { return $this->belongsTo(User::class, 'user_id'); }
    public function asistenciaEntrenamientos() { return $this->hasMany(AsistenciaEntrenamiento::class); } // <- CAMBIO
    public function evaluacionesPartido() { return $this->hasMany(EvaluacionPartido::class); } // <- CAMBIO
    public function asistencias() {
    return $this->hasMany(AsistenciaEntrenamiento::class, 'entrenamiento_id');
}

public function evaluaciones() {
    return $this->hasMany(Evaluacion::class, 'entrenamiento_id');
}
}