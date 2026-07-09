<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model {
    protected $fillable = [
        'factura_id', 'numero_nota', 'clave_acceso', 'numero_autorizacion',
        'fecha_autorizacion', 'estado_sri', 'motivo', 'detalle_motivo',
        'valor_modificacion', 'xml_generado', 'xml_autorizado'
    ];

    protected $casts = ['fecha_autorizacion' => 'datetime'];
    public function factura() { return $this->belongsTo(Factura::class); }
}