<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $fillable = [
        'torneo_id', 'rival', 'fecha', 'hora', 
        'resultado_gutigol', 'resultado_rival'
    ];

    public function alineaciones() { return $this->hasMany(Alineacion::class); }
    public function torneo() { return $this->belongsTo(Torneo::class); }
}