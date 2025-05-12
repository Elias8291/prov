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
        
        // Determinar si es la sección de confirmación
        $isConfirmationSection = ($tipoPersona == 'Física' && $seccion == 4) || ($tipoPersona == 'Moral' && $seccion == 7);

        // Calcular progreso basado en secciones completadas
        if ($isConfirmationSection) {
            $porcentaje = 100;
            $seccionesCompletadas = $totalSecciones;
        } else {
            $seccionesCompletadas = max(0, $seccion - 1);
            $porcentaje = $totalSecciones > 0 ? round(($seccionesCompletadas / $totalSecciones) * 100) : 0;
        }

        // Cargar datos previos para autocompletar (según tu modelo)
        $datosPrevios = method_exists($tramite, 'getDatosPorSeccion') 
            ? $tramite->getDatosPorSeccion($seccion)
            : [];

        // Determinar el nombre del partial de la sección
        $seccionPartial = $this->obtenerSeccionPartial($seccion, $tipoPersona);

        return view('inscripcion.formularios', [
            'seccion' => $seccion,
            'seccionPartial' => $seccionPartial,
            'totalSecciones' => $totalSecciones,
            'porcentaje' => $porcentaje,
            'tipoPersona' => $tipoPersona,
            'seccionesInfo' => $this->obtenerSecciones($tipoPersona),
            'datosPrevios' => $datosPrevios,
            'isConfirmationSection' => $isConfirmationSection,
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
        // Excluir la sección de confirmación del conteo de progreso
        return $tipoPersona == 'Física' ? 3 : // 3 secciones + confirmación
               ($tipoPersona == 'Moral' ? 6 : 5); // 6 secciones + confirmación o 5 + confirmación
    }

    private function obtenerSecciones($tipoPersona)
    {
        // Solo incluir secciones del formulario, excluir confirmación
        if ($tipoPersona == 'Física') {
            return [
                1 => 'Datos Generales',      // seccion1
                2 => 'Información Legal',    // seccion2
                3 => 'Accionistas',          // seccion6
            ];
        } elseif ($tipoPersona == 'Moral') {
            return [
                1 => 'Datos Generales',       // seccion1
                2 => 'Información Legal',     // seccion2
                3 => 'Documentos',            // seccion3
                4 => 'Información Financiera',// seccion4
                5 => 'Información Técnica',   // seccion5
                6 => 'Accionistas',           // seccion6
            ];
        }
        // Default
        return [
            1 => 'Datos Generales',
            2 => 'Información Legal',
            3 => 'Documentos',
            4 => 'Información Financiera',
            5 => 'Información Técnica',
        ];
    }

    private function obtenerSeccionPartial($seccion, $tipoPersona)
    {
        if ($tipoPersona == 'Física') {
            switch ($seccion) {
                case 1:
                    return 'seccion1'; // Datos Generales
                case 2:
                    return 'seccion2'; // Información Legal
                case 3:
                    return 'seccion6'; // Accionistas
                case 4:
                    return 'seccion7'; // Confirmación (Mensaje Enviado)
                default:
                    return 'seccion' . $seccion; // Fallback
            }
        } elseif ($tipoPersona == 'Moral') {
            switch ($seccion) {
                case 1:
                    return 'seccion1';
                case 2:
                    return 'seccion2';
                case 3:
                    return 'seccion3';
                case 4:
                    return 'seccion4';
                case 5:
                    return 'seccion5';
                case 6:
                    return 'seccion6';
                case 7:
                    return 'seccion7'; // Confirmación (Mensaje Enviado)
                default:
                    return 'seccion' . $seccion; // Fallback
            }
        }
        // Default
        return 'seccion' . $seccion;
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