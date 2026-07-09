<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IvaTarifa extends Model
{
    protected $fillable = ['porcentaje', 'codigo_sri', 'descripcion', 'activo', 'por_defecto', 'fecha_inicio'];

    protected $casts = [
        'activo' => 'boolean',
        'por_defecto' => 'boolean',
        'fecha_inicio' => 'date',
    ];

    public static function getPorDefecto()
    {
        return self::where('por_defecto', true)->where('activo', true)->first();
    }
}