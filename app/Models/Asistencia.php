<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model {
    protected $fillable = ['entrenamiento_id', 'jugador_id', 'estado'];
    public function jugador() { return $this->belongsTo(Jugador::class); }
}