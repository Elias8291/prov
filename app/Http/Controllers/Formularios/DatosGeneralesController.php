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
}