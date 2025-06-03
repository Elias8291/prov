<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiasInhabiles;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\DetalleTramite;
use App\Models\Sector;
use App\Models\ActividadSolicitante;
use App\Http\Controllers\Formularios\DocumentosController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\AccionistasController;
use App\Http\Controllers\Formularios\ApoderadoLegalController;
use App\Http\Controllers\Formularios\DatosGeneralesController;

class RevisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Tramite::with(['solicitante', 'solicitante.usuario', 'detalleTramite'])
            ->select('tramite.*')
            ->selectRaw('
                CASE 
                    WHEN tipo_tramite = "Inscripción" THEN DATE_ADD(fecha_finalizacion, INTERVAL 72 HOUR)
                    WHEN tipo_tramite = "Renovación" THEN DATE_ADD(fecha_finalizacion, INTERVAL 48 HOUR)
                    WHEN tipo_tramite = "Actualización" THEN DATE_ADD(fecha_finalizacion, INTERVAL 72 HOUR)
                    ELSE DATE_ADD(fecha_finalizacion, INTERVAL 72 HOUR)
                END as limite_revision,
                TIMESTAMPDIFF(MINUTE, NOW(), 
                    CASE 
                        WHEN tipo_tramite = "Inscripción" THEN DATE_ADD(fecha_finalizacion, INTERVAL 72 HOUR)
                        WHEN tipo_tramite = "Renovación" THEN DATE_ADD(fecha_finalizacion, INTERVAL 48 HOUR)
                        WHEN tipo_tramite = "Actualización" THEN DATE_ADD(fecha_finalizacion, INTERVAL 72 HOUR)
                        ELSE DATE_ADD(fecha_finalizacion, INTERVAL 72 HOUR)
                    END
                ) as minutos_restantes
            ');

        // Por defecto muestra los trámites terminados
        $estado_finalizacion = $request->get('estado_finalizacion', 'terminado');
        $prioridad = $request->get('prioridad');
        
        if ($estado_finalizacion === 'terminado') {
            $query->whereNotNull('fecha_finalizacion');
        } else {
            $query->whereNull('fecha_finalizacion');
        }

        // Filtrar por prioridad basado en las horas restantes
        if ($prioridad) {
            switch ($prioridad) {
                case 'critica':
                    $query->having('minutos_restantes', '<=', 12 * 60)
                          ->having('minutos_restantes', '>', 0);
                    break;
                case 'alta':
                    $query->having('minutos_restantes', '<=', 24 * 60)
                          ->having('minutos_restantes', '>', 12 * 60);
                    break;
                case 'media':
                    $query->having('minutos_restantes', '<=', 48 * 60)
                          ->having('minutos_restantes', '>', 24 * 60);
                    break;
                case 'normal':
                    $query->having('minutos_restantes', '>', 48 * 60);
                    break;
                case 'vencido':
                    $query->having('minutos_restantes', '<=', 0);
                    break;
            }
        }

        // Ordenar por tiempo restante (los más urgentes primero)
        $query->orderBy('minutos_restantes', 'ASC');

        if ($request->has('estado_tramite') && !empty($request->estado_tramite)) {
            $query->where('estado', $request->estado_tramite);
        }

        $solicitudes = $query->paginate(15)->appends($request->query());

        // Calcular tiempo restante para cada solicitud con más detalle
        foreach ($solicitudes as $solicitud) {
            if ($solicitud->fecha_finalizacion) {
                $now = \Carbon\Carbon::now();
                $tiempoLimite = $solicitud->tipo_tramite === 'Renovación' ? 48 : 72;
                $fechaLimite = \Carbon\Carbon::parse($solicitud->fecha_finalizacion)->addHours($tiempoLimite);
                
                // Calcular la diferencia total en minutos
                $minutosRestantes = $now->diffInMinutes($fechaLimite, false);
                
                // Convertir a horas y minutos
                $horasRestantes = floor(max(0, $minutosRestantes) / 60);
                $minutosRestantesFinal = max(0, $minutosRestantes % 60);
                
                $solicitud->tiempo_restante = [
                    'vencido' => $minutosRestantes <= 0,
                    'horas' => $horasRestantes,
                    'minutos' => $minutosRestantesFinal,
                    'limite_horas' => $tiempoLimite,
                    'fecha_limite' => $fechaLimite->format('Y-m-d H:i:s'),
                    'minutos_totales' => $minutosRestantes // Útil para debugging
                ];
            }
        }

        return view('revision.index', [
            'solicitudes' => $solicitudes,
            'prioridad' => $prioridad
        ]);
    }

    public function iniciarRevision($tramiteId)
    {
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_MX.UTF-8', 'Spanish_Spain', 'Spanish_Mexico');

        // Asegurar que obtenemos un objeto único de Tramite y no una colección
        $tramite = Tramite::with([
            'solicitante',
            'solicitante.usuario',
            'detalleTramite',
            'detalleTramite.direccion.asentamiento.localidad.municipio.estado',
            'detalleTramite.contacto',
            'detalleTramite.representanteLegal.instrumentoNotarial.estado',
        ])->findOrFail($tramiteId);

        $sectores = Sector::all();

        // Obtener actividades seleccionadas
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

        // Reutilizar controladores existentes para obtener datos
        $domicilioController = new DomicilioController();
        $addressData = $domicilioController->getAddressData($tramite);

        // Verificar duplicidad de direcciones
        $duplicateAddressWarning = $this->checkDuplicateAddress($tramite);

        $constitucionController = new ConstitucionController();
        $incorporationData = $constitucionController->getIncorporationData($tramite);

        $accionistasController = new AccionistasController();
        $accionistas = $accionistasController->getShareholdersData($tramite);

        // Usar ApoderadoLegalController para obtener datos del representante legal
        $apoderadoController = new ApoderadoLegalController();
        $legalRepresentativeData = $apoderadoController->getDatosApoderadoLegal($tramite);

        $documentosController = new DocumentosController();
        $documentosResponse = $documentosController->get(new Request(['tramiteId' => $tramite->id]), $tramite->id);
        $documentos = [];
        if ($documentosResponse->getStatusCode() === 200) {
            $documentos = $documentosResponse->getData(true)['documentos'];
        }

        // Construir datosPrevios usando los datos del trámite directamente
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
            'tipo_tramite' => $tramite->tipo_tramite ?? 'Desconocido',
            'duplicate_address_warning' => $duplicateAddressWarning,
            'tramite' => $tramite,
            'componentParams' => [
                'action' => route('revision.procesar', $tramite->id),
                'method' => 'POST',
                'tipoPersona' => $tramite->solicitante->tipo_persona,
                'datosPrevios' => $datosPrevios,
                'sectores' => $sectores,
                'isRevisor' => true,
                'mostrarCurp' => $tramite->solicitante->tipo_persona === 'Física',
                'seccion' => 1,
                'totalSecciones' => 6,
                'isConfirmationSection' => false,
                'actividadesSeleccionadas' => $actividadesSeleccionadas,
                'isEditable' => false,
                'showPdfUpload' => false,
                'documentos' => $documentos,
                'tipo_tramite' => $tramite->tipo_tramite ?? 'Desconocido',
            ],
        ]);
    }

    /**
     * Verifica si existe una dirección duplicada para un trámite aprobado
     *
     * @param Tramite $tramite El trámite a verificar
     * @return string|null Mensaje de advertencia o null si no hay duplicados
     */
   private function checkDuplicateAddress(Tramite $tramite): ?string
    {
        if ($tramite->detalleTramite && $tramite->detalleTramite->direccion) {
            $currentAddress = $tramite->detalleTramite->direccion;

            // Coincidencias exactas
            $exactMatches = Tramite::where('estado', 'Aprobado')
                ->where('id', '!=', $tramite->id)
                ->whereHas('detalleTramite.direccion', function ($query) use ($currentAddress) {
                    $query->where('codigo_postal', $currentAddress->codigo_postal)
                        ->where('calle', $currentAddress->calle)
                        ->where('numero_exterior', $currentAddress->numero_exterior)
                        ->where(function ($q) use ($currentAddress) {
                            $q->where('numero_interior', $currentAddress->numero_interior)
                              ->orWhereNull('numero_interior');
                        })
                        ->where('asentamiento_id', $currentAddress->asentamiento_id);
                })
                ->with(['solicitante', 'detalleTramite.direccion'])
                ->get();

            // Coincidencias similares (mismo código postal, calle y asentamiento)
            $similarMatches = Tramite::where('estado', 'Aprobado')
                ->where('id', '!=', $tramite->id)
                ->whereHas('detalleTramite.direccion', function ($query) use ($currentAddress) {
                    $query->where('codigo_postal', $currentAddress->codigo_postal)
                        ->where('calle', $currentAddress->calle)
                        ->where('asentamiento_id', $currentAddress->asentamiento_id);
                })
                ->with(['solicitante', 'detalleTramite.direccion'])
                ->get();

            $messages = [];

            // Procesar coincidencias exactas
            if ($exactMatches->isNotEmpty()) {
                $count = $exactMatches->count();
                $solicitanteNames = $exactMatches->map(function ($t) {
                    $name = $t->detalleTramite->razon_social ?? ($t->solicitante->usuario->nombre ?? 'Sin Nombre');
                    return "- Proveedor: $name (Dirección: {$t->detalleTramite->direccion->calle} {$t->detalleTramite->direccion->numero_exterior} " . 
                           ($t->detalleTramite->direccion->numero_interior ?? '') . ")";
                })->implode("\n");
                $messages[] = $count > 1 
                    ? "Coincidencia exacta: Esta dirección está registrada en $count trámites aprobados:\n$solicitanteNames"
                    : "Coincidencia exacta: Esta dirección está registrada en un trámite aprobado:\n$solicitanteNames";
            }

            // Procesar coincidencias similares (excluyendo exactas)
            $similarMatches = $similarMatches->filter(function ($t) use ($exactMatches) {
                return !$exactMatches->contains('id', $t->id);
            });

            // Umbral para considerar una dirección "frecuente"
            $frequencyThreshold = 3;
            if ($similarMatches->isNotEmpty() || $exactMatches->count() >= $frequencyThreshold) {
                $totalMatches = $similarMatches->count() + $exactMatches->count();
                if ($totalMatches >= $frequencyThreshold) {
                    $solicitanteNames = $similarMatches->map(function ($t) {
                        $name = $t->detalleTramite->razon_social ?? ($t->solicitante->usuario->nombre ?? 'Sin Nombre');
                        return "- Proveedor: $name (Dirección: {$t->detalleTramite->direccion->calle} {$t->detalleTramite->direccion->numero_exterior} " . 
                               ($t->detalleTramite->direccion->numero_interior ?? '') . ")";
                    })->implode("\n");
                    $messages[] = "Advertencia de dirección frecuente: La combinación de calle, código postal y asentamiento aparece en $totalMatches trámites aprobados, lo que podría indicar un posible riesgo (ej. empresas fantasma):\n$solicitanteNames";
                }
            }

            if (!empty($messages)) {
                return implode("\n\n", $messages);
            }
        }
        
        return null;
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