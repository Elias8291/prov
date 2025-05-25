<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\Sector;
use App\Models\Documento;
use App\Models\Estado;
use App\Models\DocumentoSolicitante;
use App\Services\TramiteService;
use App\Http\Controllers\Formularios\DatosGeneralesController;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\AccionistasController;
use App\Http\Controllers\Formularios\ApoderadoLegalController;

class InscripcionController extends Controller
{
    protected $tramiteService;
    protected $datosGeneralesController;
    protected $domicilioController;
    protected $constitucionController;
    protected $accionistasController;
    protected $apoderadoLegalController;

    public function __construct(
        TramiteService $tramiteService,
        DatosGeneralesController $datosGeneralesController,
        DomicilioController $domicilioController,
        ConstitucionController $constitucionController,
        AccionistasController $accionistasController,
        ApoderadoLegalController $apoderadoLegalController
    ) {
        $this->tramiteService = $tramiteService;
        $this->datosGeneralesController = $datosGeneralesController;
        $this->domicilioController = $domicilioController;
        $this->constitucionController = $constitucionController;
        $this->accionistasController = $accionistasController;
        $this->apoderadoLegalController = $apoderadoLegalController;
    }


    public function index()
    {
        return view('inscripcion.index');
    }
    public function mostrarFormulario(Request $request)
    {
        $user = Auth::user();
        $solicitante = $user->solicitante;
        $isRevisor = $user->hasRole('revisor');

        if ($isRevisor && !$solicitante) {
            $tipoPersona = 'Física';
            $tramite = null;
            $seccion = 1;
            $totalSecciones = $this->obtenerTotalSecciones($tipoPersona);
            $datosPrevios = [
                'rfc' => '',
                'razon_social' => '',
                'objeto_social' => '',
                'contacto_telefono' => '',
                'contacto_web' => '',
                'contacto_nombre' => '',
                'contacto_cargo' => '',
                'contacto_correo' => '',
                'correo_electronico' => '',
            ];
            $actividadesSeleccionadas = [];
            $porcentaje = 0;
            $seccionesCompletadas = 0;
            $isConfirmationSection = false;
            $direccion = null;
            $estados = $this->cargarEstados();
        } else {
            if (!$solicitante) {
                return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
            }

            $tipoPersona = $solicitante->tipo_persona ?? 'Física';
            $tramite = Tramite::where('solicitante_id', $solicitante->id)
                ->where('estado', 'Pendiente')
                ->with(['detalleTramite.direccion.asentamiento.localidad.municipio.estado', 'detalleTramite.contacto'])
                ->first();

            if (!$tramite && !$isRevisor) {
                return redirect()->route('inscripcion.terminos_y_condiciones');
            }

            if ($tramite && $tramite->progreso_tramite == 0 && !$isRevisor) {
                return redirect()->route('inscripcion.terminos_y_condiciones');
            }

            if ($request->has('retroceder') && $tramite && $tramite->progreso_tramite > 1) {
                $tramite->decrement('progreso_tramite');
                $tramite->save();
            }

            $seccion = $tramite ? $tramite->progreso_tramite : 1;
            $totalSecciones = $this->obtenerTotalSecciones($tipoPersona);

            $isConfirmationSection = ($tipoPersona == 'Física' && $seccion == 4) || ($tipoPersona == 'Moral' && $seccion == 7);

            if ($isConfirmationSection) {
                $porcentaje = 100;
                $seccionesCompletadas = $totalSecciones;
            } else {
                $seccionesCompletadas = max(0, $seccion - 1);
                $porcentaje = $totalSecciones > 0 ? round(($seccionesCompletadas / $totalSecciones) * 100) : 0;
            }

            // Fetch section-specific data using TramiteService
            $sectionData = $this->tramiteService->obtenerDatosSeccion($tramite, $seccion, $tipoPersona, $isRevisor);
            $datosPrevios = $sectionData['datosPrevios'] ?? [];
            $actividadesSeleccionadas = $sectionData['actividadesSeleccionadas'] ?? [];

            // Ensure section 1 data is fully populated
            if ($seccion == 1 && $tramite) {
                $datosPrevios = array_merge([
                    'rfc' => $solicitante->rfc ?? 'No disponible',
                    'curp' => $tipoPersona == 'Física' ? ($solicitante->curp ?? 'No disponible') : null,
                    'razon_social' => $solicitante->razon_social ?? '',
                    'objeto_social' => $tramite->detalleTramite->objeto_social ?? '',
                    'contacto_telefono' => $tramite->detalleTramite->contacto->telefono ?? '',
                    'contacto_web' => $tramite->detalleTramite->contacto->pagina_web ?? '',
                    'contacto_nombre' => $tramite->detalleTramite->contacto->nombre ?? '',
                    'contacto_cargo' => $tramite->detalleTramite->contacto->cargo ?? '',
                    'contacto_correo' => $tramite->detalleTramite->contacto->correo_electronico ?? '',
                    'correo_electronico' => $solicitante->correo_electronico ?? '',
                ], $datosPrevios);
            }

            if ($seccion == 2 && $tramite && $tramite->detalleTramite && $tramite->detalleTramite->direccion) {
                $direccion = $tramite->detalleTramite->direccion;
                $datosPrevios['codigo_postal'] = $direccion->codigo_postal ?? '';
                $datosPrevios['calle'] = $direccion->calle ?? '';
                $datosPrevios['numero_exterior'] = $direccion->numero_exterior ?? '';
                $datosPrevios['numero_interior'] = $direccion->numero_interior ?? '';
                $datosPrevios['entre_calle_1'] = $direccion->entre_calle_1 ?? '';
                $datosPrevios['entre_calle_2'] = $direccion->entre_calle_2 ?? '';
                if ($direccion->asentamiento) {
                    $datosPrevios['colonia'] = $direccion->asentamiento->nombre ?? '';
                    $datosPrevios['municipio'] = $direccion->asentamiento->localidad->municipio->nombre ?? '';
                    $datosPrevios['estado'] = $direccion->asentamiento->localidad->municipio->estado->nombre ?? '';
                }
            } else {
                $direccion = null;
            }

            if ($seccion == 5 && $tramite && $tramite->detalleTramite && $tramite->detalleTramite->apoderadoLegal) {
                $apoderadoLegal = $tramite->detalleTramite->apoderadoLegal;
                $datosPrevios['nombre-apoderado'] = $apoderadoLegal->nombre ?? '';
                $datosPrevios['numero-escritura'] = $apoderadoLegal->instrumento_notarial->numero_escritura ?? '';
                $datosPrevios['nombre-notario'] = $apoderadoLegal->instrumento_notarial->nombre_notario ?? '';
                $datosPrevios['numero-notario'] = $apoderadoLegal->instrumento_notarial->numero_notario ?? '';
                $datosPrevios['entidad-federativa'] = $apoderadoLegal->instrumento_notarial->estado_id ?? '';
                $datosPrevios['fecha-escritura'] = $apoderadoLegal->instrumento_notarial->fecha ?? '';
                $datosPrevios['numero-registro'] = $apoderadoLegal->instrumento_notarial->registro_mercantil ?? '';
                $datosPrevios['fecha-inscripcion'] = $apoderadoLegal->instrumento_notarial->fecha_registro ?? '';
            }

            $estados = $this->cargarEstados();
        }

        $sectores = Sector::all()->map(function ($sector) {
            return [
                'id' => $sector->id,
                'nombre' => $sector->nombre,
            ];
        })->toArray();

        $documentos = [
            'common' => [],
            'specific' => [],
        ];

        if ($seccion == 6 || ($tipoPersona == 'Física' && $seccion == 3)) {
            $documentos['common'] = Documento::where('tipo_persona', 'Ambas')
                ->where('es_visible', true)
                ->get(['id', 'nombre', 'descripcion', 'tipo'])
                ->toArray();

            $documentos['specific'] = Documento::where('tipo_persona', $tipoPersona)
                ->where('es_visible', true)
                ->get(['id', 'nombre', 'descripcion', 'tipo'])
                ->toArray();
        }

        $documentosSubidos = [];
        if (!empty($tramite)) {
            $documentosSubidos = DocumentoSolicitante::where('tramite_id', $tramite->id)
                ->get()
                ->keyBy('documento_id');
        }

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
            'mostrarCurp' => $tipoPersona == 'Física' && $user->hasRole('solicitante') && $seccion == 1,
            'sectores' => $sectores,
            'actividadesSeleccionadas' => $actividadesSeleccionadas,
            'isRevisor' => $isRevisor,
            'direccion' => $direccion,
            'estados' => $estados,
            'tramite' => $tramite,
            'documentos' => $documentos,
            'documentosSubidos' => $documentosSubidos,
        ]);
    }
    public function procesarSeccion(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('revisor')) {
            return redirect()->route('inscripcion.formulario')->with('info', 'Los revisores no pueden enviar formularios.');
        }

        $solicitante = $user->solicitante;
        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->first();

        if (!$tramite) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        if ($request->input('action') === 'previous') {
            \Log::info("Procesando acción 'previous'. Progreso actual: {$tramite->progreso_tramite}");
            if ($tramite->progreso_tramite > 1) {
                \Log::info("Reduciendo progreso_tramite de {$tramite->progreso_tramite} a " . ($tramite->progreso_tramite - 1));
                $tramite->decrement('progreso_tramite');
                $tramite->save();
            } else {
                \Log::info("progreso_tramite ya está en el mínimo: {$tramite->progreso_tramite}");
            }
            return redirect()->route('inscripcion.formulario');
        }

        $seccion = $tramite->progreso_tramite;
        $tipoPersona = $solicitante->tipo_persona ?? 'No definido';

        // Validación de documentos para las secciones correspondientes
        if (($tipoPersona == 'Moral' && $seccion == 6) || ($tipoPersona == 'Física' && $seccion == 3)) {
            $documentosRequeridos = Documento::where(function ($q) use ($tipoPersona) {
                $q->where('tipo_persona', $tipoPersona)
                    ->orWhere('tipo_persona', 'Ambas');
            })
                ->where('es_visible', true)
                ->pluck('id')
                ->toArray();

            $docsSubidos = DocumentoSolicitante::where('tramite_id', $tramite->id)
                ->whereIn('documento_id', $documentosRequeridos)
                ->count();

            if ($docsSubidos < count($documentosRequeridos)) {
                return redirect()->route('inscripcion.formulario')
                    ->withErrors(['error' => 'Debe subir todos los documentos requeridos antes de continuar.']);
            }

            $tramite->progreso_tramite = ($tipoPersona == 'Moral') ? 7 : 4;
            $tramite->save();

            return redirect()->route('inscripcion.formulario');
        }

        // Procesamiento para el resto de secciones
        try {
            DB::beginTransaction();

            $resultado = false;

            if ($seccion == 1) {
                $resultado = $this->datosGeneralesController->guardar($request, $tramite);
            } elseif ($seccion == 2) {
                $resultado = $this->domicilioController->guardar($request, $tramite);
            } elseif ($seccion == 3) {
                if ($tipoPersona == 'Moral') {
                    $resultado = $this->constitucionController->guardar($request, $tramite);
                } else if ($tipoPersona == 'Física') {
                    $resultado = $this->accionistasController->guardar($request, $tramite);
                }
            } elseif ($seccion == 4 && $tipoPersona == 'Moral') {
                $resultado = $this->accionistasController->guardar($request, $tramite);
            } elseif ($seccion == 5 && $tipoPersona == 'Moral') {
                $resultado = $this->apoderadoLegalController->guardar($request, $tramite);
            }

            if (!$resultado) {
                throw new \Exception('No se pudo procesar la sección correctamente.');
            }

            DB::commit();

            if ($seccion < $this->obtenerTotalSecciones($tipoPersona)) {
                $tramite->increment('progreso_tramite');
            }

            return redirect()->route('inscripcion.formulario');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing section ' . $seccion . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Ocurrió un error al guardar los datos: ' . $e->getMessage()])->withInput();
        }
    }
    public function exito()
    {
        return view('inscripcion.exito');
    }

    private function cargarEstados()
    {
        return Estado::orderBy('nombre', 'asc')->get(['id', 'nombre'])->toArray();
    }

    private function obtenerTotalSecciones($tipoPersona)
    {
        return $tipoPersona == 'Física' ? 3 : ($tipoPersona == 'Moral' ? 6 : 5);
    }

    private function obtenerSecciones($tipoPersona)
    {
        if ($tipoPersona == 'Física') {
            return [
                1 => 'Datos Generales',
                2 => 'Domicilio',
                3 => 'Documentos',
            ];
        } elseif ($tipoPersona == 'Moral') {
            return [
                1 => 'Datos Generales',
                2 => 'Domicilio',
                3 => 'Datos de Constitución',
                4 => 'Accionistas',
                5 => 'Apoderado Legal',
                6 => 'Documentos',
            ];
        }
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
}
