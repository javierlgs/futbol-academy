<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FotoEntrenamiento extends Model {
    protected $table = 'fotos_entrenamiento';
    protected $fillable = ['entrenamiento_id', 'uploaded_by', 'ruta', 'descripcion', 'visible_padres'];

    public function entrenamiento() { return $this->belongsTo(Entrenamiento::class); }
    public function usuario() { return $this->belongsTo(User::class, 'uploaded_by'); }
}