<?php

namespace App\Http\Controllers;

use App\Models\IvaTarifa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IvaTarifaController extends Controller
{
    public function index()
    {
        $tarifas = IvaTarifa::orderBy('por_defecto', 'desc')->orderBy('porcentaje')->get();
        
        return Inertia::render('IvaTarifas/Index', [
            'tarifas' => $tarifas
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'porcentaje' => 'required|numeric|between:0,100',
            'codigo_sri' => 'required|string|max:1|unique:iva_tarifas,codigo_sri',
            'descripcion' => 'required|string|max:255'
        ]);

        IvaTarifa::create([
            'porcentaje' => $data['porcentaje'],
            'codigo_sri' => $data['codigo_sri'],
            'descripcion' => $data['descripcion'],
            'por_defecto' => false
        ]);

        return back()->with('success', 'Tarifa creada correctamente');
    }

    public function update(Request $request, IvaTarifa $ivaTarifa)
    {
        $data = $request->validate([
            'por_defecto' => 'required|boolean'
        ]);

        if ($data['por_defecto']) {
            // Desactivar todas las demás
            IvaTarifa::where('id', '!=', $ivaTarifa->id)->update(['por_defecto' => false]);
            $ivaTarifa->update(['por_defecto' => true]);
        }

        return back()->with('success', 'Tarifa activada como predeterminada');
    }

    public function destroy(IvaTarifa $ivaTarifa)
    {
        if ($ivaTarifa->por_defecto) {
            return back()->with('error', 'No puedes eliminar la tarifa activa');
        }

        $ivaTarifa->delete();
        return back()->with('success', 'Tarifa eliminada');
    }
}