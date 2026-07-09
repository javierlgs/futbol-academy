<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AsistenciaEntrenamiento extends Model {
    protected $table = 'asistencia_entrenamientos';
    
    protected $fillable = ['jugador_id', 'entrenamiento_id', 'presente', 'estado', 'observacion', 'registrado_por'];
    
    protected $casts = ['presente' => 'boolean'];

    public function jugador() { return $this->belongsTo(Jugador::class); }
    public function entrenamiento() { return $this->belongsTo(Entrenamiento::class); }
}