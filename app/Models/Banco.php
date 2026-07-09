<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banco extends Model {
    protected $fillable = ['nombre', 'tipo', 'numero_cuenta', 'titular', 'activo', 'principal'];

    protected $casts = [
        'activo' => 'boolean',
        'principal' => 'boolean',
    ];

    public function scopeActivos($query) {
        return $query->where('activo', true);
    }
}