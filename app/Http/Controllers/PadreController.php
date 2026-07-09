<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PadreController extends Controller
{
    public function index()
    {
        // Por ahora, solo retornamos una vista vacía o el dashboard
        return Inertia::render('Padre/Dashboard');
    }
}