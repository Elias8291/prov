<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class FormularioController extends Controller
{
    public function mostrarFormularios()
    {
        $usuario = Auth::user();
        $solicitante = $usuario->solicitante;
        $tramite = $solicitante->tramites()->first();

        // Verificar que el usuario haya aceptado los términos
        if (!$tramite || $tramite->progreso_tramite < 1) {
            return redirect()->route('terminos')->with('error', 'Debes aceptar los términos primero');
        }

        $tipoPersona = $solicitante->tipo_persona;
        
        return view('formularios', [
            'tipoPersona' => $tipoPersona
        ]);
    }
}