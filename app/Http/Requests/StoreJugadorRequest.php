<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Categoria;

class StoreJugadorRequest extends FormRequest
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
            'rep_nombres' => 'required|string|max:255', // ← Cambiado a required
            'rep_apellidos' => 'required|string|max:255', // ← AGREGADO
            'rep_cedula' => 'required|string|max:20',
            'rep_relacion' => 'required|string|max:50', // ← AGREGADO
            'rep_telefono' => 'required|string|max:20', // ← Cambiado a required
            'rep_correo' => 'nullable|email|max:255',
            'rep_direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $año = date('Y', strtotime($this->fecha_nacimiento));
        
        $categoria = Categoria::where('año_nacimiento', $año)
            ->where('sede_id', $this->sede_id)
            ->where(function($q) {
                $q->where('sexo', $this->sexo)->orWhere('sexo', 'Mixto');
            })
            ->orderByRaw("FIELD(sexo, ?, 'Mixto')", [$this->sexo]) // Prioriza M/F sobre Mixto
            ->first();

        $this->merge(['categoria_id' => $categoria?->id]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->categoria_id) {
                $año = date('Y', strtotime($this->fecha_nacimiento));
                $validator->errors()->add('categoria_id', "No existe categoría {$año} - {$this->sexo} para esta sede. Créala primero.");
            }
        });
    }
}