<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class OcultarSuperAdmin {
    public function handle(Request $request, Closure $next) {
        // Si no eres superadmin, no puedes ver ni editar superadmins
        if (!auth()->user()->hasRole('superadmin')) {
            $request->merge([
                'ocultar_superadmin' => true
            ]);
        }
        return $next($request);
    }
}