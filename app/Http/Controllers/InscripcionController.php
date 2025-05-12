<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tramite;

class InscripcionController extends Controller
{
    // Muestra la sección actual del formulario de inscripción
    public function mostrarFormulario(Request $request)
    {
        $user = Auth::user();
        $solicitante = $user->solicitante;
        $tipoPersona = $solicitante->tipo_persona ?? 'No definido';

        // Buscar trámite pendiente o crear uno nuevo
        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->first();

        // Si no existe trámite, redirigir a términos y condiciones
        if (!$tramite) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        // Si el trámite está en progreso_tramite 0, enviar a términos
        if ($tramite->progreso_tramite == 0) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        // Permitir retroceder si el usuario da clic en "Anterior"
        if ($request->has('retroceder') && $tramite->progreso_tramite > 1) {
            $tramite->decrement('progreso_tramite');
        }

        $seccion = $tramite->progreso_tramite;
        $totalSecciones = $this->obtenerTotalSecciones($tipoPersona);
        
        // Calcular progreso basado en secciones completadas (progreso_tramite - 1)
        $seccionesCompletadas = max(0, $seccion - 1); // Resta 1 para reflejar secciones completadas
        $porcentaje = round(($seccionesCompletadas / $totalSecciones) * 100);

        // Cargar datos previos para autocompletar (según tu modelo)
        $datosPrevios = method_exists($tramite, 'getDatosPorSeccion') 
            ? $tramite->getDatosPorSeccion($seccion)
            : [];

        return view('inscripcion.formularios', [
            'seccion' => $seccion,
            'totalSecciones' => $totalSecciones,
            'porcentaje' => $porcentaje,
            'tipoPersona' => $tipoPersona,
            'seccionesInfo' => $this->obtenerSecciones($tipoPersona),
            'datosPrevios' => $datosPrevios,
        ]);
    }

    // Página de éxito
    public function exito()
    {
        return view('inscripcion.exito');
    }

    // Helpers
    private function obtenerTotalSecciones($tipoPersona)
    {
        return $tipoPersona == 'Física' ? 4 :
               ($tipoPersona == 'Moral' ? 7 : 6);
    }

    private function obtenerSecciones($tipoPersona)
    {
        if ($tipoPersona == 'Física') {
            return [
                1 => 'Datos Generales',      // Original section 1
                2 => 'Información Legal',    // Original section 2
                3 => 'Accionistas',          // Original section 6
                4 => 'Información Adicional', // Original section 7
            ];
        } elseif ($tipoPersona == 'Moral') {
            return [
                1 => 'Datos Generales',
                2 => 'Información Legal',
                3 => 'Documentos',
                4 => 'Información Financiera',
                5 => 'Información Técnica',
                6 => 'Accionistas',
                7 => 'Información Adicional',
            ];
        }
        // Default
        return [
            1 => 'Datos Generales',
            2 => 'Información Legal',
            3 => 'Documentos',
            4 => 'Información Financiera',
            5 => 'Información Técnica',
            6 => 'Información Adicional',
        ];
    }

    private function validarDatosSeccion(Request $request, $seccion, $tipoPersona)
    {
        // Ejemplo para la sección 1
        if ($seccion == 1) {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email',
            ]);
        }
        // Ejemplo para sección 2 (personaliza según tu lógica)
        if ($seccion == 2) {
            $request->validate([
                // 'campo_legal' => 'required',
                // ...
            ]);
        }
        // Agrega validaciones para cada sección según tus necesidades
    }
}