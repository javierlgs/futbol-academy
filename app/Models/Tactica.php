<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tactica extends Model
{
    protected $fillable = ['partido_id', 'dibujo_data'];

    // Relación inversa para acceder al partido desde la táctica
    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }
}