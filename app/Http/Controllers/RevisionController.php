<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\DetalleTramite;
use App\Models\Sector;
use App\Models\ActividadSolicitante;
use App\Models\RepresentanteLegal;
use App\Models\InstrumentoNotarial;
use App\Http\Controllers\Formularios\DocumentosController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\AccionistasController;
use DateTime;

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
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_MX.UTF-8', 'Spanish_Spain', 'Spanish_Mexico');

        $monthNames = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        $tramite = Tramite::with([
            'solicitante',
            'solicitante.usuario',
            'detalleTramite',
            'detalleTramite.direccion.asentamiento.localidad.municipio.estado',
            'detalleTramite.contacto',
            'detalleTramite.representanteLegal.instrumentoNotarial.estado',
        ])
            ->whereHas('solicitante', function ($query) use ($rfc) {
                $query->where('rfc', $rfc);
            })
            ->firstOrFail();

        $sectores = Sector::all();

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

        $domicilioController = new DomicilioController();
        $addressData = $domicilioController->getAddressData($tramite);

        $constitucionController = new ConstitucionController();
        $incorporationData = $constitucionController->getIncorporationData($tramite);

        $accionistasController = new AccionistasController();
        $accionistas = $accionistasController->getShareholdersData($tramite);
        $documentosController = new DocumentosController();
        $documentosResponse = $documentosController->get(new Request(['tramiteId' => $tramite->id]), $tramite->id);

        $documentos = [];
        if ($documentosResponse->getStatusCode() === 200) {
            $documentos = $documentosResponse->getData(true)['documentos'];
        }

        $legalRepresentativeData = [];
        if ($tramite->detalleTramite && $tramite->detalleTramite->representanteLegal) {
            $representanteLegal = $tramite->detalleTramite->representanteLegal;
            $instrumentoNotarial = $representanteLegal->instrumentoNotarial;

            if (setlocale(LC_TIME, 'es_ES.UTF-8', 'es_MX.UTF-8', 'Spanish_Spain', 'Spanish_Mexico')) {
                $fechaEscritura = $instrumentoNotarial->fecha
                    ? (new DateTime($instrumentoNotarial->fecha))->format('j \d\e F \d\e Y')
                    : 'No disponible';
                $fechaInscripcion = $instrumentoNotarial->fecha_registro
                    ? (new DateTime($instrumentoNotarial->fecha_registro))->format('j \d\e F \d\e Y')
                    : 'No disponible';
            } else {
                $fechaEscritura = $instrumentoNotarial->fecha
                    ? (new DateTime($instrumentoNotarial->fecha))->format('j \d\e ' . $monthNames[(new DateTime($instrumentoNotarial->fecha))->format('n')] . ' \d\e Y')
                    : 'No disponible';
                $fechaInscripcion = $instrumentoNotarial->fecha_registro
                    ? (new DateTime($instrumentoNotarial->fecha_registro))->format('j \d\e ' . $monthNames[(new DateTime($instrumentoNotarial->fecha_registro))->format('n')] . ' \d\e Y')
                    : 'No disponible';
            }

            $legalRepresentativeData = [
                'nombre_apoderado' => $representanteLegal->nombre ?? 'No disponible',
                'numero_escritura_apoderado' => $instrumentoNotarial->numero_escritura ?? 'No disponible',
                'fecha_escritura_apoderado' => $fechaEscritura,
                'nombre_notario_apoderado' => $instrumentoNotarial->nombre_notario ?? 'No disponible',
                'numero_notario_apoderado' => $instrumentoNotarial->numero_notario ?? 'No disponible',
                'entidad_federativa_apoderado' => $instrumentoNotarial->estado ? $instrumentoNotarial->estado->nombre : 'No disponible',
                'numero_registro_apoderado' => $instrumentoNotarial->registro_mercantil ?? 'No disponible',
                'fecha_inscripcion_apoderado' => $fechaInscripcion,
            ];
        } else {
            $legalRepresentativeData = [
                'nombre_apoderado' => 'No disponible',
                'numero_escritura_apoderado' => 'No disponible',
                'fecha_escritura_apoderado' => 'No disponible',
                'nombre_notario_apoderado' => 'No disponible',
                'numero_notario_apoderado' => 'No disponible',
                'entidad_federativa_apoderado' => 'No disponible',
                'numero_registro_apoderado' => 'No disponible',
                'fecha_inscripcion_apoderado' => 'No disponible',
            ];
        }

        $datosPrevios = array_merge([
            'razon_social' => $tramite->detalleTramite?->razon_social ?? 'No disponible',
            'email' => $tramite->detalleTramite?->email ?? 'No disponible',
            'telefono' => $tramite->detalleTramite?->telefono ?? 'No disponible',
            'sitio_web' => $tramite->detalleTramite?->sitio_web ?? 'No disponible',
            'rfc' => $tramite->solicitante->rfc ?? 'No disponible',
            'curp' => $tramite->solicitante->curp ?? 'No disponible',
            'objeto_social' => $tramite->solicitante->objeto_social ?? 'No disponible',
            'contacto_telefono' => $tramite->detalleTramite?->telefono ?? 'No disponible',
            'contacto_web' => $tramite->detalleTramite?->sitio_web ?? 'No disponible',
            'contacto_nombre' => $tramite->detalleTramite?->contacto?->nombre ?? 'No disponible',
            'contacto_cargo' => $tramite->detalleTramite?->contacto?->puesto ?? 'No disponible',
            'contacto_correo' => $tramite->detalleTramite?->contacto?->email ?? 'No disponible',
            'contacto_telefono_2' => $tramite->detalleTramite?->contacto?->telefono ?? 'No disponible',
        ], $addressData, $incorporationData, $legalRepresentativeData);

        return view('revision.iniciar_revision', [
    'solicitante' => $tramite->solicitante,
    'accionistas' => $accionistas,
    'componentParams' => [
        'action' => route('revision.procesar', $tramite->id),
        'method' => 'POST',
        'tipoPersona' => $tramite->solicitante->tipo_persona,
        'datosPrevios' => $datosPrevios,
        'sectores' => $sectores,
        'isRevisor' => true,
        'mostrarCurp' => $tramite->solicitante->tipo_persona === 'Física',
        'seccion' => 1,
        'totalSecciones' => 6, // Updated to include document section
        'isConfirmationSection' => false,
        'actividadesSeleccionadas' => $actividadesSeleccionadas,
        'isEditable' => false,
        'showPdfUpload' => false,
        'documentos' => $documentos, // Add documents to componentParams
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
