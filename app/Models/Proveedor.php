<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    
    protected $fillable = [
        'ruc', 'razon_social', 'nombre_comercial', 
        'telefono', 'email', 'direccion'
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
}