<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model {
    protected $table = 'evaluaciones';
    protected $fillable = ['entrenamiento_id', 'jugador_id', 'habilidad_id', 'evaluador_id','puntaje', 'comentario'];

    public function habilidad() { return $this->belongsTo(Habilidad::class); }
    public function jugador() { return $this->belongsTo(Jugador::class); }
}