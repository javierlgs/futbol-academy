<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJugadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date|before:today',
            'sexo' => 'required|in:M,F',
            'sede_id' => 'required|exists:sedes,id',
            'rep_cedula' => 'required|string|max:20',
            'rep_nombres' => 'required|string|max:255',
            'rep_apellidos' => 'required|string|max:255',
            'rep_relacion' => 'required|string|max:50',
            'rep_telefono' => 'required|string|max:20',
            'rep_correo' => 'nullable|email|max:255',
            'rep_direccion' => 'nullable|string|max:500',
            'estado' => 'required|string|max:50',
            'activo' => 'boolean',
        ];
    }
}