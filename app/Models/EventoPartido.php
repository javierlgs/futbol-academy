<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EvaluacionPartido extends Model {
    protected $table = 'evaluaciones_partido';
    
    protected $fillable = [
        'entrenamiento_id', 'partido_id', 'jugador_id', 
        'rendimiento_general', 'tecnica', 'tactica', 
        'fisico', 'mental', 'comentario'
    ];

    public function jugador() { return $this->belongsTo(Jugador::class); }
    public function entrenamiento() { return $this->belongsTo(Entrenamiento::class); }
}