<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SolicitanteController extends Controller
{
    public function mostrarTerminosYCondiciones()
{
    $usuario = Auth::user();

    $tipoPersona = $usuario->solicitante->tipo_persona ?? 'No definido';
    Log::info('Tipo de persona obtenido:', ['tipo_persona' => $tipoPersona]);

    return view('inscripcion.terminos_y_condiciones', compact('tipoPersona'));
}

    public function aceptarTerminos(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'terms' => 'accepted',
        ]);

        Log::info('Términos y condiciones aceptados por el usuario.', ['usuario_id' => $usuario->id]);

        return redirect()->route('inscripcion.formularios');
    }

    
}