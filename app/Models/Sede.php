<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model {
    protected $fillable = ['nombre', 'direccion', 'telefono', 'responsable', 'activo'];
    
    public function usuarios() {
        return $this->hasMany(User::class);
    }
}