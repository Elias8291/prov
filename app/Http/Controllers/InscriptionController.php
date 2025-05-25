<?php

namespace App\Http\Controllers;

use App\Services\TramiteService;
use App\Services\FormDataService;
use App\Http\Controllers\Formularios\DatosGeneralesController;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Models\Tramite;
use App\Models\User;
use App\Models\Solicitante;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InscriptionController extends Controller
{
    protected $tramiteService;
    protected $formDataService;
    protected $datosGeneralesController;
    protected $domicilioController;

    public function __construct(
        TramiteService $tramiteService,
        FormDataService $formDataService,
        DatosGeneralesController $datosGeneralesController,
        DomicilioController $domicilioController
    ) {
        $this->tramiteService = $tramiteService;
        $this->formDataService = $formDataService;
        $this->datosGeneralesController = $datosGeneralesController;
        $this->domicilioController = $domicilioController;
    }

    /**
     * Muestra el formulario de inscripción.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mostrarFormulario(Request $request)
    {
        $usuario = Auth::user();
        $esRevisor = $usuario->hasRole('revisor');
        $solicitante = $usuario->solicitante;

        if ($esRevisor && !$solicitante) {
            return $this->renderizarFormulario(null, 1, 'Física', $esRevisor, []);
        }

        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        $tramite = $this->tramiteService->obtenerTramitePendiente($solicitante->id);

        if (!$tramite && !$esRevisor) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        if ($tramite && $tramite->estado !== 'Pendiente') {
            return redirect()->route('inscripcion.exito')->with('info', 'El trámite ya ha sido finalizado.');
        }

        $tipoPersona = $solicitante->tipo_persona ?? 'Física';
        $totalSecciones = $this->formDataService->obtenerTotalSecciones($tipoPersona);

        // Manejo de navegación (anterior/siguiente)
        $seccion = $tramite ? $tramite->progreso_tramite : 1;
        if ($request->has('action') && $request->input('action') === 'anterior' && $seccion > 1) {
            $tramite->decrement('progreso_tramite');
            $seccion = $tramite->progreso_tramite;
        }

        $esSeccionConfirmacion = ($tipoPersona === 'Física' && $seccion == 4) || ($tipoPersona === 'Moral' && $seccion == 7);
        $seccionesCompletadas = max(0, $seccion - 1);
        $porcentaje = $totalSecciones > 0 ? round(($seccionesCompletadas / $totalSecciones) * 100) : 0;

        $data = $this->formDataService->obtenerDatosSeccion($tramite, $seccion, $tipoPersona, $esRevisor);

        return $this->renderizarFormulario($tramite, $seccion, $tipoPersona, $esRevisor, [
            'totalSecciones' => $totalSecciones,
            'porcentaje' => $porcentaje,
            'esSeccionConfirmacion' => $esSeccionConfirmacion,
        ]);
    }

    /**
     * Renderiza el formulario con los datos proporcionados.
     *
     * @param Tramite|null $tramite
     * @param int $seccion
     * @param string $tipoPersona
     * @param bool $esRevisor
     * @param array $parametrosAdicionales
     * @return \Illuminate\View\View
     */
    protected function renderizarFormulario(?Tramite $tramite, int $seccion, string $tipoPersona, bool $esRevisor, array $parametrosAdicionales): \Illuminate\View\View
    {
        $usuario = Auth::user();
        $data = $this->formDataService->obtenerDatosSeccion($tramite, $seccion, $tipoPersona, $esRevisor);
        $componenteSeccion = $this->formDataService->obtenerComponenteSeccion($seccion, $tipoPersona);

        $documentos = [
            'comunes' => [],
            'especificos' => [],
        ];

        if ($seccion == ($tipoPersona === 'Física' ? 3 : 6)) {
            $documentos['comunes'] = Documento::where('tipo_persona', 'Ambas')->where('es_visible', true)->get(['id', 'nombre', 'descripcion', 'tipo'])->toArray();
            $documentos['especificos'] = Documento::where('tipo_persona', $tipoPersona)->where('es_visible', true)->get(['id', 'nombre', 'descripcion', 'tipo'])->toArray();
        }

        $documentosSubidos = $tramite ? DocumentoSolicitante::where('tramite_id', $tramite->id)->get()->keyBy('documento_id') : [];

        return view('inscripcion.formularios', array_merge([
            'seccion' => $seccion,
            'componenteSeccion' => $componenteSeccion,
            'tipoPersona' => $tipoPersona,
            'esRevisor' => $esRevisor,
            'mostrarCurp' => $tipoPersona === 'Física' && $usuario->hasRole('solicitante') && $seccion == 1,
            'tramite' => $tramite,
            'documentos' => $documentos,
            'documentosSubidos' => $documentosSubidos,
        ], $data, $parametrosAdicionales));
    }

    /**
     * Procesa los datos enviados de una sección del formulario.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function procesarSeccion(Request $request)
    {
        $usuario = Auth::user();
        if ($usuario->hasRole('revisor')) {
            return redirect()->route('inscripcion.formulario')->with('info', 'Los revisores no pueden enviar formularios.');
        }

        $solicitante = $usuario->solicitante;
        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        $tramite = $this->tramiteService->obtenerTramitePendiente($solicitante->id);
        if (!$tramite) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        if ($tramite->estado !== 'Pendiente') {
            return redirect()->route('inscripcion.exito')->with('info', 'El trámite ya ha sido finalizado.');
        }

        $seccion = $tramite->progreso_tramite;
        $tipoPersona = $solicitante->tipo_persona ?? 'Física';

        try {
            DB::beginTransaction();

            $resultado = false;
            if ($seccion == 1) {
                $resultado = $this->datosGeneralesController->guardar($request, $tramite);
            } elseif ($seccion == 2) {
                $resultado = $this->domicilioController->guardar($request, $tramite);
            }

            if (!$resultado) {
                throw new \Exception('No se pudo procesar la sección correctamente.');
            }

            // Validación de documentos para las secciones de documentos
            if (($tipoPersona === 'Moral' && $seccion == 6) || ($tipoPersona === 'Física' && $seccion == 3)) {
                $documentosRequeridos = Documento::where(function ($q) use ($tipoPersona) {
                    $q->where('tipo_persona', $tipoPersona)->orWhere('tipo_persona', 'Ambas');
                })->where('es_visible', true)->pluck('id')->toArray();

                $docsSubidos = DocumentoSolicitante::where('tramite_id', $tramite->id)
                    ->whereIn('documento_id', $documentosRequeridos)
                    ->count();

                if ($docsSubidos < count($documentosRequeridos)) {
                    throw new \Exception('Debes subir todos los documentos requeridos antes de continuar.');
                }

                $tramite->progreso_tramite = ($tipoPersona === 'Moral') ? 7 : 4;
            } else {
                $this->tramiteService->avanzarSeccion($tramite);
            }

            DB::commit();
            return redirect()->route('inscripcion.formulario');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al procesar la sección $seccion: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Ocurrió un error al guardar los datos: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Muestra la vista de éxito tras completar el trámite.
     *
     * @return \Illuminate\View\View
     */
    public function exito()
    {
        return view('inscripcion.exito');
    }
}