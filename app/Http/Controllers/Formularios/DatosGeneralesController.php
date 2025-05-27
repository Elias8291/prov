<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\ContactoSolicitante;
use App\Models\ActividadSolicitante;
use App\Models\Solicitante;

class DatosGeneralesController extends Controller
{
    /**
     * Guarda los datos generales del trámite y sus relaciones
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return bool Retorna true si la operación fue exitosa
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $solicitante = Auth::user()->solicitante;

        $detalle = $this->saveDetalleTramite($request, $tramite);
        $this->saveContactoSolicitante($request, $detalle);
        $this->updateSolicitanteObjetoSocial($request, $solicitante);
        $this->syncActividades($request, $tramite);

        return true;
    }

    /**
     * Guarda o actualiza los detalles del trámite
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return DetalleTramite El modelo de detalle de trámite actualizado
     */
    private function saveDetalleTramite(Request $request, Tramite $tramite)
    {
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

        $detalle->telefono = $request->input('contacto_telefono');
        $detalle->sitio_web = $request->input('contacto_web');


        $detalle->razon_social = Auth::user()->name;
        $detalle->email = Auth::user()->email;

        $detalle->save();

        return $detalle;
    }

    /**
     * Guarda o actualiza la información del contacto del solicitante
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param DetalleTramite $detalle El modelo de detalle de trámite asociado
     * @return void
     */
    private function saveContactoSolicitante(Request $request, DetalleTramite $detalle)
    {
        $contacto = ContactoSolicitante::firstOrNew([
            'id' => $detalle->contacto_id
        ]);

        $contacto->nombre = $request->input('contacto_nombre');
        $contacto->puesto = $request->input('contacto_cargo');
        $contacto->telefono = $request->input('contacto_telefono_2');
        $contacto->email = $request->input('contacto_correo');
        $contacto->save();

        if (!$detalle->contacto_id) {
            $detalle->contacto_id = $contacto->id;
            $detalle->save();
        }
    }

    /**
     * Actualiza el objeto social del solicitante si se proporciona
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Solicitante $solicitante El modelo de solicitante asociado
     * @return void
     */
    private function updateSolicitanteObjetoSocial(Request $request, Solicitante $solicitante)
    {
        if ($request->input('objeto_social') !== null) {
            $solicitante->objeto_social = $request->input('objeto_social');
            $solicitante->save();
            Log::info('Objeto social actualizado para solicitante ID: ' . $solicitante->id, [
                'objeto_social' => $request->input('objeto_social')
            ]);
        }
    }

    /**
     * Sincroniza las actividades seleccionadas con el trámite
     *
     * @param Request $request La solicitud HTTP con los datos del formulario
     * @param Tramite $tramite El modelo de trámite asociado
     * @return void
     */
    private function syncActividades(Request $request, Tramite $tramite)
    {
        $existingActivities = ActividadSolicitante::where('tramite_id', $tramite->id)
            ->pluck('actividad_id')
            ->toArray();

        $selectedActivities = $this->parseSelectedActivities($request->input('actividades_seleccionadas', []));

        foreach ($selectedActivities as $activityId) {
            if (!in_array($activityId, $existingActivities)) {
                ActividadSolicitante::create([
                    'tramite_id' => $tramite->id,
                    'actividad_id' => $activityId,
                ]);
            }
        }
    }

    /**
     * Convierte las actividades seleccionadas a un array válido
     *
     * @param mixed $activities Las actividades seleccionadas del formulario
     * @return array Las actividades parseadas como array
     */
    private function parseSelectedActivities($activities)
    {
        if (!is_array($activities) && is_string($activities)) {
            return json_decode($activities, true) ?: [];
        }
        return is_array($activities) ? $activities : [];
    }

    /**
     * Obtiene los datos generales del trámite y el solicitante
     *
     * @param Tramite $tramite El modelo de trámite asociado
     * @return array Los datos generales formateados
     */
    public function obtenerDatos(Tramite $tramite)
    {
        $solicitante = $tramite->solicitante;
        $sectores = $tramite->sectores->pluck('id')->toArray();
        $actividades = $tramite->actividades()->pluck('id')->toArray();

        return [
            'rfc' => $solicitante->rfc ?? null,
            'tipo_persona' => $solicitante->tipo_persona ?? null,
            'razon_social' => $solicitante->razon_social ?? null,
            'correo_electronico' => $solicitante->correo_electronico ?? null,
            'contacto_telefono' => $solicitante->contacto_telefono ?? '',
            'objeto_social' => $solicitante->objeto_social ?? null,
            'sectores' => $sectores ? $sectores[0] : null,
            'actividades' => $actividades,
            'contacto_nombre' => $solicitante->contacto_nombre ?? '',
            'contacto_cargo' => $solicitante->contacto_cargo ?? '',
            'contacto_correo' => $solicitante->contacto_correo ?? '',
            'contacto_telefono_2' => $solicitante->contacto_2 ?? '',
            'contacto_web' => $solicitante->contacto_web ?? '',
        ];
    }
}
