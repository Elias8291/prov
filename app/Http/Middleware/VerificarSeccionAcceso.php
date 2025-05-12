<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerificarSeccionAcceso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener la sección solicitada
        $seccion = (int)$request->route('seccion') ?? 1;
        
        // Siempre permitir acceso a la primera sección
        if ($seccion <= 1) {
            return $next($request);
        }
        
        // Verificar si se han completado las secciones anteriores
        for ($i = 1; $i < $seccion; $i++) {
            if (!Session::has('formulario_seccion_' . $i)) {
                // Redirigir a la primera sección incompleta
                return redirect()->route('inscripcion.formulario', ['seccion' => $i])
                    ->with('warning', 'Debe completar las secciones en orden.');
            }
        }
        
        return $next($request);
    }
}