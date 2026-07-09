<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoCategoria extends Model {
    protected $fillable = ['nombre', 'color', 'icono', 'deducible_sri'];

    protected $casts = [
        'deducible_sri' => 'boolean',
    ];

    public function gastos() {
        return $this->hasMany(Gasto::class, 'categoria_id');
    }
}