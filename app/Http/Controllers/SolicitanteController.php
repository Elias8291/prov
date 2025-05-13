<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tramite;

class SolicitanteController extends Controller
{
    public function mostrarTerminosYCondiciones()
    {
        $usuario = Auth::user();

        // If user is a revisor, redirect directly to inscripcion.formulario
        if ($usuario->hasRole('revisor')) {
            return redirect()->route('inscripcion.formulario');
        }

        // Ensure the user has a solicitante record
        $solicitante = $usuario->solicitante;
        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        $tipoPersona = $solicitante->tipo_persona ?? 'No definido';

        // Verificar si existe un trámite pendiente
        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->first();

        // Si existe un trámite y progreso_tramite >= 1, redirigir al formulario
        if ($tramite && $tramite->progreso_tramite >= 1) {
            return redirect()->route('inscripcion.formulario');
        }

        return view('inscripcion.terminos_y_condiciones', compact('tipoPersona'));
    }

    public function aceptarTerminos(Request $request)
    {
        $request->validate([
            'terms' => 'accepted',
        ]);

        $usuario = Auth::user();

        // If user is a revisor, redirect directly to inscripcion.formulario
        if ($usuario->hasRole('revisor')) {
            return redirect()->route('inscripcion.formulario');
        }

        // Ensure the user has a solicitante record
        $solicitante = $usuario->solicitante;
        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        // Crear o buscar trámite pendiente, inicia en progreso_tramite 0 (términos)
        $tramite = Tramite::firstOrCreate(
            ['solicitante_id' => $solicitante->id, 'estado' => 'Pendiente'],
            ['progreso_tramite' => 0, 'tipo_tramite' => 'Inscripcion']
        );

        // Avanzar a la sección 1 (Datos generales)
        $tramite->progreso_tramite = 1;
        $tramite->save();

        return redirect()->route('inscripcion.formulario');
    }
}