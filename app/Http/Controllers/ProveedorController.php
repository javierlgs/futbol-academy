<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'ruc' => 'required|string|size:13|unique:proveedores,ruc',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string'
        ]);

        $proveedor = Proveedor::create($data);
        
        return response()->json($proveedor);
    }
}