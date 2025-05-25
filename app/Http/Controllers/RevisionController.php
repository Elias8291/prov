<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\DetalleTramite;
use App\Models\Sector;
use App\Models\ActividadSolicitante;
use Illuminate\Support\Facades\Auth;

class RevisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Tramite::with(['solicitante', 'solicitante.usuario', 'detalleTramite'])
            ->whereBetween('progreso_tramite', [0, 7])
            ->whereNull('fecha_finalizacion')
            ->whereNull('revisado_por')
            ->whereNull('fecha_revision')
            ->whereIn('estado', ['Pendiente', 'Rechazado', 'Por Cotejar']);

        if ($request->has('progreso') && !empty($request->progreso)) {
            $query->where('progreso_tramite', $request->progreso);
        }

        if ($request->has('estado_tramite') && !empty($request->estado_tramite)) {
            $query->where('estado', $request->estado_tramite);
        }

        $solicitudesPendientes = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('revision.index', [
            'solicitudes' => $solicitudesPendientes,
        ]);
    }

   public function iniciarRevision($rfc)
{
    $tramite = Tramite::with([
        'solicitante',
        'solicitante.usuario',
        'detalleTramite',
        'detalleTramite.direccion.asentamiento.localidad.municipio.estado',
        'detalleTramite.contacto',
    ])
    ->whereHas('solicitante', function ($query) use ($rfc) {
        $query->where('rfc', $rfc);
    })
    ->firstOrFail();

    $sectores = Sector::all();

    // Fetch selected activities with full details
    $actividadesSeleccionadas = ActividadSolicitante::where('tramite_id', $tramite->id)
        ->with('actividad.sector')
        ->get()
        ->map(function ($actividadSolicitante) {
            return [
                'id' => $actividadSolicitante->actividad_id,
                'nombre' => $actividadSolicitante->actividad->nombre,
                'sector_id' => $actividadSolicitante->actividad->sector_id,
            ];
        })
        ->toArray();

    $datosPrevios = [
        'razon_social' => $tramite->detalleTramite?->razon_social,
        'email' => $tramite->detalleTramite?->email,
        'telefono' => $tramite->detalleTramite?->telefono,
        'sitio_web' => $tramite->detalleTramite?->sitio_web,
        'rfc' => $tramite->solicitante->rfc,
        'curp' => $tramite->solicitante->curp,
        'objeto_social' => $tramite->solicitante->objeto_social,
        'contacto_telefono' => $tramite->detalleTramite?->telefono,
        'contacto_web' => $tramite->detalleTramite?->sitio_web,
        'contacto_nombre' => $tramite->detalleTramite?->contacto?->nombre,
        'contacto_cargo' => $tramite->detalleTramite?->contacto?->puesto,
        'contacto_correo' => $tramite->detalleTramite?->contacto?->email,
        'contacto_telefono_2' => $tramite->detalleTramite?->contacto?->telefono,
        // Add other fields as needed
    ];

    return view('revision.iniciar_revision', [
        'solicitante' => $tramite->solicitante,
        'componentParams' => [
            'action' => route('revision.procesar', $tramite->id),
            'method' => 'POST',
            'tipoPersona' => $tramite->solicitante->tipo_persona,
            'datosPrevios' => $datosPrevios,
            'sectores' => $sectores,
            'isRevisor' => true,
            'mostrarCurp' => $tramite->solicitante->tipo_persona === 'Física',
            'seccion' => 1,
            'totalSecciones' => 3,
            'isConfirmationSection' => false,
            'actividadesSeleccionadas' => $actividadesSeleccionadas,
        ],
    ]);
}

    public function procesar(Request $request, $tramiteId)
    {
        $tramite = Tramite::findOrFail($tramiteId);
        $tramite->estado = $request->input('estado', 'En Revision');
        $tramite->revisado_por = Auth::id();
        $tramite->fecha_revision = now();
        $tramite->observaciones = $request->input('observaciones');
        $tramite->save();

        return redirect()->route('revision.index')->with('success', 'Revisión procesada correctamente.');
    }
}