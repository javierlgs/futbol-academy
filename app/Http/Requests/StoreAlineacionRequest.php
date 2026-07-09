<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlineacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'alineacion' => 'required|array',
            'alineacion.*.jugador_id' => 'required|exists:jugadores,id',
            'alineacion.*.tipo' => 'required|in:titular,suplente',
            'alineacion.*.minuto_entrada' => 'integer|min:0',
            'alineacion.*.minuto_salida' => 'integer|min:0',
        ];
    }
}