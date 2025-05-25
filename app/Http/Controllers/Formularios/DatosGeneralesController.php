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
    public function guardar(Request $request, Tramite $tramite)
    {
        $solicitante = Auth::user()->solicitante;

        // Get or create detalle_tramite
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

        // Update detalle_tramite with phone and website
        $detalle->telefono = $request->input('contacto_telefono');
        $detalle->sitio_web = $request->input('contacto_web');

        // Set razon_social and email based on user role
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('revisor')) {
            $detalle->razon_social = $request->input('razon_social');
            $detalle->email = $request->input('correo_electronico');
        } elseif (Auth::user()->hasRole('solicitante')) {
            $detalle->razon_social = Auth::user()->name; // Use the user's name for razon_social
            $detalle->email = Auth::user()->email; // Optionally set email from user account
        }

        $detalle->save();

        // Create or update contacto_solicitante
        $contacto = ContactoSolicitante::firstOrNew([
            'id' => $detalle->contacto_id
        ]);

        $contacto->nombre = $request->input('contacto_nombre');
        $contacto->puesto = $request->input('contacto_cargo');
        $contacto->telefono = $request->input('contacto_telefono_2');
        $contacto->email = $request->input('contacto_correo');
        $contacto->save();

        // Update detalle_tramite with new contacto_id if it was just created
        if (!$detalle->contacto_id) {
            $detalle->contacto_id = $contacto->id;
            $detalle->save();
        }

        // Update solicitante with objeto_social for Moral providers or when admin/revisor specifies it
        if ($request->input('objeto_social') !== null) {
            $solicitante->objeto_social = $request->input('objeto_social');
            $solicitante->save();
            Log::info('Objeto social actualizado para solicitante ID: ' . $solicitante->id, ['objeto_social' => $request->input('objeto_social')]);
        }

        // Process selected activities
        $existingActivities = ActividadSolicitante::where('tramite_id', $tramite->id)
            ->pluck('actividad_id')
            ->toArray();

        $selectedActivities = $request->input('actividades_seleccionadas', []);

        if (!is_array($selectedActivities) && is_string($selectedActivities)) {
            $selectedActivities = json_decode($selectedActivities, true) ?: [];
        }

        foreach ($selectedActivities as $activityId) {
            if (!in_array($activityId, $existingActivities)) {
                ActividadSolicitante::create([
                    'tramite_id' => $tramite->id,
                    'actividad_id' => $activityId,
                ]);
            }
        }

        return true;
    }

    public function get(Request $request)
    {
        try {
            // Validar que se proporcione al menos rfc o solicitante_id
            $request->validate([
                'rfc' => 'required_without:solicitante_id|string|size:13', // Asume RFC de 13 caracteres
                'solicitante_id' => 'required_without:rfc|exists:solicitante,id',
            ]);

            // Buscar el solicitante
            $solicitante = null;
            if ($request->has('rfc')) {
                $solicitante = Solicitante::where('rfc', $request->input('rfc'))->first();
            } else {
                $solicitante = Solicitante::find($request->input('solicitante_id'));
            }

            if (!$solicitante) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Solicitante no encontrado.'
                ], 404);
            }

            // Obtener los trámites con sus relaciones
            $tramites = Tramite::with([
                'solicitante',
                'detalleTramite.contacto',
                'actividadSolicitantes.actividad.sector'
            ])
                ->where('solicitante_id', $solicitante->id)
                ->get();

            if ($tramites->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontraron trámites para este solicitante.'
                ], 404);
            }

            // Estructurar la respuesta
            $response = $tramites->map(function ($tramite) {
                return [
                    'tramite_id' => $tramite->id,
                    'tipo_tramite' => $tramite->tipo_tramite,
                    'estado' => $tramite->estado,
                    'progreso_tramite' => $tramite->progreso_tramite,
                    'fecha_inicio' => $tramite->fecha_inicio ? $tramite->fecha_inicio->toIso8601String() : null,
                    'fecha_finalizacion' => $tramite->fecha_finalizacion ? $tramite->fecha_finalizacion->toIso8601String() : null,
                    'observaciones' => $tramite->observaciones,
                    'detalle_tramite' => $tramite->detalleTramite ? [
                        'razon_social' => $tramite->detalleTramite->razon_social,
                        'email' => $tramite->detalleTramite->email,
                        'telefono' => $tramite->detalleTramite->telefono,
                        'sitio_web' => $tramite->detalleTramite->sitio_web,
                        'contacto' => $tramite->detalleTramite->contacto ? [
                            'nombre' => $tramite->detalleTramite->contacto->nombre,
                            'puesto' => $tramite->detalleTramite->contacto->puesto,
                            'telefono' => $tramite->detalleTramite->contacto->telefono,
                            'email' => $tramite->detalleTramite->contacto->email,
                        ] : null,
                    ] : null,
                    'actividades' => $tramite->actividadSolicitantes->map(function ($actividadSolicitante) {
                        return [
                            'actividad_id' => $actividadSolicitante->actividad_id,
                            'nombre_actividad' => $actividadSolicitante->actividad->nombre,
                            'sector' => $actividadSolicitante->actividad->sector ? $actividadSolicitante->actividad->sector->nombre : null,
                        ];
                    })->toArray(),
                ];
            });

            return response()->json([
                'success' => true,
                'solicitante' => [
                    'id' => $solicitante->id,
                    'rfc' => $solicitante->rfc,
                    'tipo_persona' => $solicitante->tipo_persona,
                    'objeto_social' => $solicitante->objeto_social,
                ],
                'tramites' => $response,
                'mensaje' => 'Datos generales obtenidos correctamente.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos generales: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al obtener los datos generales.'
            ], 500);
        }
    }
    
}
