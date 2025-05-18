<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Tramite;
use App\Models\Sector;
use App\Models\Actividad;
use App\Models\ActividadSolicitante;
use App\Models\ContactoSolicitante;
use App\Models\DetalleTramite;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\DatoConstitutivo;
use App\Models\AccionistaSolicitante;
use App\Models\Accionista;
use App\Models\Estado;

class InscripcionController extends Controller
{
    // Muestra la sección actual del formulario de inscripción
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
        $datosPrevios = [];
        $porcentaje = 0;
        $seccionesCompletadas = 0;
        $isConfirmationSection = false;
        $direccion = null; // No dirección para revisores sin solicitante
        $estados = $this->cargarEstados(); // Cargar estados para todas las secciones
    } else {
        // Ensure solicitante exists for non-revisors
        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        $tipoPersona = $solicitante->tipo_persona ?? 'No definido';

        // Buscar trámite pendiente
        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->with('detalleTramite.direccion') // Cargar detalle_tramite y dirección
            ->first();

        // Si no existe trámite, redirigir a términos y condiciones (except for revisors)
        if (!$tramite && !$isRevisor) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        // Si el trámite está en progreso_tramite 0, enviar a términos (except for revisors)
        if ($tramite && $tramite->progreso_tramite == 0 && !$isRevisor) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        // Permitir retroceder si el usuario da clic en "Anterior"
        if ($request->has('retroceder') && $tramite && $tramite->progreso_tramite > 1) {
            $tramite->decrement('progreso_tramite');
        }

        $seccion = $tramite ? $tramite->progreso_tramite : 1;
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

        // Cargar datos previos para autocompletar
        $datosPrevios = $tramite && method_exists($tramite, 'getDatosPorSeccion') 
            ? $tramite->getDatosPorSeccion($seccion)
            : [];

        // Añadir CURP a datosPrevios para solicitante con tipo_persona Física en sección 1
        if ($tipoPersona == 'Física' && $seccion == 1 && $user->hasRole('solicitante')) {
            $datosPrevios['curp'] = $solicitante->curp ?? 'No disponible';
        }

        // Cargar datos de dirección para la sección 2
        if ($seccion == 2 && $tramite && $tramite->detalleTramite && $tramite->detalleTramite->direccion) {
            $direccion = $tramite->detalleTramite->direccion;
            $datosPrevios['codigo_postal'] = $direccion->codigo_postal ?? '';
            $datosPrevios['calle'] = $direccion->calle ?? '';
            $datosPrevios['numero_exterior'] = $direccion->numero_exterior ?? '';
            $datosPrevios['numero_interior'] = $direccion->numero_interior ?? '';
            $datosPrevios['entre_calle_1'] = $direccion->entre_calle_1 ?? '';
            $datosPrevios['entre_calle_2'] = $direccion->entre_calle_2 ?? '';
            // Cargar datos de asentamiento, municipio y estado si es necesario
            if ($direccion->asentamiento) {
                $datosPrevios['colonia'] = $direccion->asentamiento->nombre ?? '';
                $datosPrevios['municipio'] = $direccion->asentamiento->localidad->municipio->nombre ?? '';
                $datosPrevios['estado'] = $direccion->asentamiento->localidad->municipio->estado->nombre ?? '';
            }
        } else {
            $direccion = null;
        }

        // Cargar datos de apoderado legal para la sección 5
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

        // Cargar estados para el formulario (para todas las secciones que lo necesiten)
        $estados = $this->cargarEstados();
    }

    // Cargar sectores para el formulario
    $sectores = Sector::all()->map(function ($sector) {
        return [
            'id' => $sector->id,
            'nombre' => $sector->nombre,
        ];
    })->toArray();

    // Cargar documentos para la sección 6
    $documentos = [
        'common' => [],
        'specific' => [],
    ];

    if ($seccion == 6 || ($tipoPersona == 'Física' && $seccion == 3)) {
        // Documentos comunes (tipo_persona = 'Ambas')
        $documentos['common'] = Documento::where('tipo_persona', 'Ambas')
            ->where('es_visible', true)
            ->get(['id', 'nombre', 'descripcion', 'tipo'])
            ->toArray();

        // Documentos específicos (tipo_persona = Física o Moral)
        $documentos['specific'] = Documento::where('tipo_persona', $tipoPersona)
            ->where('es_visible', true)
            ->get(['id', 'nombre', 'descripcion', 'tipo'])
            ->toArray();
    }

    // Determinar el nombre del partial de la sección
    $seccionPartial = $this->obtenerSeccionPartial($seccion, $tipoPersona);

    // Pass $tramite to the view
    return view('inscripcion.formularios', [
        'seccion' => $seccion,
        'seccionPartial' => $seccionPartial,
        'totalSecciones' => $totalSecciones,
        'porcentaje' => $porcentaje,
        'tipoPersona' => $tipoPersona,
        'seccionesInfo' => $this->obtenerSecciones($tipoPersona),
        'datosPrevios' => $datosPrevios,
        'isConfirmationSection' => $isConfirmationSection,
        'mostrarCurp' => ($tipoPersona == 'Física' && $user->hasRole('solicitante') && $seccion == 1),
        'sectores' => $sectores,
        'isRevisor' => $isRevisor,
        'direccion' => $direccion,
        'estados' => $estados, // Ahora disponible para todas las secciones
        'tramite' => $tramite, // Explicitly pass $tramite
        'documentos' => $documentos, // Pass documents to the view
    ]);
}

    private function cargarEstados()
    {
        // Usar el FQCN (Fully Qualified Class Name) para evitar ambigüedad
        return \App\Models\Estado::orderBy('nombre', 'asc')->get(['id', 'nombre'])->toArray();
    }

    // Obtener actividades por sector (AJAX)
    public function obtenerActividades(Request $request)
    {
        $sectorId = $request->input('sector_id');

        if (!$sectorId) {
            return response()->json(['error' => 'Sector no especificado'], 400);
        }

        $actividades = Actividad::where('sector_id', $sectorId)->get()->map(function ($actividad) {
            return [
                'id' => $actividad->id,
                'nombre' => $actividad->nombre,
            ];
        })->toArray();

        return response()->json(['actividades' => $actividades]);
    }

    private function procesarApoderadoLegal(Request $request, $tramite)
    {
        try {
            Log::info('Processing legal representative data for section 5:', $request->all());

            // Get or create detalle_tramite
            $detalleTramite = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

            // Create a new instrumento_notarial for the legal representative
            $instrumentoNotarial = new \App\Models\InstrumentoNotarial();
            
            // If we already have a representante_legal, use its instrumento_notarial
            if ($detalleTramite->representante_legal_id) {
                $representanteLegal = \App\Models\RepresentanteLegal::find($detalleTramite->representante_legal_id);
                if ($representanteLegal) {
                    $existingInstrument = \App\Models\InstrumentoNotarial::find($representanteLegal->instrumento_notarial_id);
                    if ($existingInstrument) {
                        $instrumentoNotarial = $existingInstrument;
                    }
                }
            }

            // Fill the instrumento_notarial data
            $instrumentoNotarial->numero_escritura = $request->input('numero-escritura');
            $instrumentoNotarial->fecha = $request->input('fecha-escritura');
            $instrumentoNotarial->nombre_notario = $request->input('nombre-notario');
            $instrumentoNotarial->numero_notario = $request->input('numero-notario');
            $instrumentoNotarial->estado_id = $request->input('entidad-federativa');
            $instrumentoNotarial->registro_mercantil = $request->input('numero-registro');
            $instrumentoNotarial->fecha_registro = $request->input('fecha-inscripcion');
            $instrumentoNotarial->save();
            
            Log::info('InstrumentoNotarial saved with ID: ' . $instrumentoNotarial->id);

            // Get or create representante_legal record
            if ($detalleTramite->representante_legal_id) {
                $representanteLegal = \App\Models\RepresentanteLegal::find($detalleTramite->representante_legal_id);
            }
            
            if (empty($representanteLegal)) {
                $representanteLegal = new \App\Models\RepresentanteLegal();
            }

            // Fill with data
            $representanteLegal->nombre = $request->input('nombre-apoderado');
            $representanteLegal->instrumento_notarial_id = $instrumentoNotarial->id;
            $representanteLegal->save();
            
            Log::info('RepresentanteLegal saved with ID: ' . $representanteLegal->id);

            // Update the detalleTramite with the representante_legal_id
            $detalleTramite->representante_legal_id = $representanteLegal->id;
            $detalleTramite->save();
            
            Log::info('DetalleTramite updated with representante_legal_id: ' . $representanteLegal->id);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error processing legal representative data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    // Página de éxito
    public function exito()
    {
        return view('inscripcion.exito');
    }

    public function procesarSeccion(Request $request)
    {
        $user = Auth::user();
        
        // Prevent revisors from submitting the form
        if ($user->hasRole('revisor')) {
            return redirect()->route('inscripcion.formulario')->with('info', 'Los revisores no pueden enviar formularios.');
        }

        $solicitante = $user->solicitante;

        // Ensure solicitante exists
        if (!$solicitante) {
            return redirect()->route('dashboard')->withErrors(['error' => 'No tienes un perfil de solicitante asociado.']);
        }

        // Find the pending tramite
        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->first();

        if (!$tramite) {
            return redirect()->route('inscripcion.terminos_y_condiciones');
        }

        // Get current section and tipo_persona
        $seccion = $tramite->progreso_tramite;
        $tipoPersona = $solicitante->tipo_persona ?? 'No definido';
        
        // Debug the current section being processed
        Log::info('Processing section: ' . $seccion);
        Log::info('Form data received: ', $request->all());

        // Validar datos para la sección actual (excepto sección 6 Documentos)
        if (!($seccion == 6 && $tipoPersona == 'Moral')) {
            $this->validarDatosSeccion($request, $seccion, $tipoPersona);
        }

        try {
            DB::beginTransaction();

            if ($seccion == 1) {
                $this->procesarDatosGenerales($request, $tramite);
            }
            elseif ($seccion == 2) {
                $this->procesarDatosLegales($request, $tramite);
            }
            elseif ($seccion == 3) {
                if ($tipoPersona == 'Moral') {
                    $this->procesarDatosConstitucion($request, $tramite);
                } else if ($tipoPersona == 'Física') {
                    $this->procesarAccionistas($request, $tramite);
                }
            }
            elseif ($seccion == 4 && $tipoPersona == 'Moral') {
                $this->procesarAccionistas($request, $tramite);
            }
            elseif ($seccion == 5 && $tipoPersona == 'Moral') {
                $this->procesarApoderadoLegal($request, $tramite);
            }
            // OMITIDO: sección 6 (Documentos) - no se procesa nada

            DB::commit();

            // Increment the progreso_tramite to move to the next section
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

    private function procesarDatosGenerales(Request $request, $tramite)
    {
        $solicitante = Auth::user()->solicitante;

        // Get or create detalle_tramite
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

        // Update detalle_tramite with phone and website
        $detalle->telefono = $request->input('contacto_telefono');
        $detalle->sitio_web = $request->input('contacto_web');

        // If admin or revisor is creating, set razon_social and email
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('revisor')) {
            $detalle->razon_social = $request->input('razon_social');
            $detalle->email = $request->input('correo_electronico');
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

        // Handle constancia document upload (for admin)
        // OMITIDO: No carga de documentos
    }

    public function guardarDatosConstitucion(Request $request, Tramite $tramite)
    {
        try {
            DB::beginTransaction();

            // Validar los datos del formulario
            $request->validate([
                'numero_escritura' => 'required|numeric|max:9999999999',
                'nombre_notario' => 'required|string|max:100|regex:/^[A-Za-z\s]+$/',
                'entidad_federativa' => 'required|exists:estado,id',
                'fecha_constitucion' => 'required|date',
                'numero_notario' => 'required|numeric|max:9999999999',
                'numero_registro' => 'required|numeric|max:9999999999',
                'fecha_inscripcion' => 'required|date',
            ]);

            // Obtener o crear el instrumento notarial
            $instrumentoNotarial = \App\Models\InstrumentoNotarial::updateOrCreate(
                [
                    'tramite_id' => $tramite->id,
                ],
                [
                    'numero_escritura' => $request->input('numero_escritura'),
                    'fecha' => $request->input('fecha_constitucion'),
                    'nombre_notario' => $request->input('nombre_notario'),
                    'numero_notario' => $request->input('numero_notario'),
                    'estado_id' => $request->input('entidad_federativa'),
                    'registro_mercantil' => $request->input('numero_registro'),
                    'fecha_registro' => $request->input('fecha_inscripcion'),
                ]
            );

            // Actualizar el progreso del trámite
            $tramite->increment('progreso_tramite');

            DB::commit();

            return redirect()->route('inscripcion.formulario')
                ->with('success', 'Datos de Constitución guardados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error guardando datos de Constitución: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Ocurrió un error al guardar los datos: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Process incorporation data from section 3
     */
    private function procesarDatosConstitucion(Request $request, $tramite)
    {
        try {
            Log::info('Processing incorporation data for section 3:', $request->all());

            // Validate the form data
            $this->validarDatosSeccion($request, 3, 'Moral');

            // Get the detalle_tramite first
            $detalleTramite = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
            
            // Create a new instrumento_notarial
            $instrumentoNotarial = new \App\Models\InstrumentoNotarial();

            // If we already have an existing instrument linked to this tramite, use that one instead
            if ($detalleTramite->dato_constitutivo_id) {
                $datoConstitutivo = \App\Models\DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);
                if ($datoConstitutivo && $datoConstitutivo->instrumento_notarial_id) {
                    $existingInstrument = \App\Models\InstrumentoNotarial::find($datoConstitutivo->instrumento_notarial_id);
                    if ($existingInstrument) {
                        $instrumentoNotarial = $existingInstrument;
                    }
                }
            }

            // Fill the instrumento_notarial data
            $instrumentoNotarial->numero_escritura = $request->input('numero_escritura');
            $instrumentoNotarial->fecha = $request->input('fecha_constitucion');
            $instrumentoNotarial->nombre_notario = $request->input('nombre_notario');
            $instrumentoNotarial->numero_notario = $request->input('numero_notario');
            $instrumentoNotarial->estado_id = $request->input('entidad_federativa');
            $instrumentoNotarial->registro_mercantil = $request->input('numero_registro');
            $instrumentoNotarial->fecha_registro = $request->input('fecha_inscripcion');
            $instrumentoNotarial->save();

            Log::info('InstrumentoNotarial saved with ID: ' . $instrumentoNotarial->id);

            // Get or create datos_constitutivo record
            if ($detalleTramite->dato_constitutivo_id) {
                $datoConstitutivo = \App\Models\DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);
            }
            
            if (empty($datoConstitutivo)) {
                $datoConstitutivo = new \App\Models\DatoConstitutivo();
            }

            // Fill with data - using objeto_social from solicitante if available
            $datoConstitutivo->instrumento_notarial_id = $instrumentoNotarial->id;
            $datoConstitutivo->objeto_social = Auth::user()->solicitante->objeto_social ?? '';
            $datoConstitutivo->save();

            Log::info('DatoConstitutivo saved with ID: ' . $datoConstitutivo->id);

            // Update the detalleTramite with the datos_constitutivo_id
            $detalleTramite->dato_constitutivo_id = $datoConstitutivo->id;
            $detalleTramite->save();

            Log::info('DetalleTramite updated with dato_constitutivo_id: ' . $datoConstitutivo->id);

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing incorporation data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Validate incorporation data from section 3
     */
    private function validarDatosConstitucion(Request $request)
    {
        $rules = [
            'numero_escritura' => 'required|numeric|max:9999999999',
            'nombre_notario' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
            'entidad_federativa' => 'required|exists:estado,id',
            'fecha_constitucion' => 'required|date|before_or_equal:today',
            'numero_notario' => 'required|numeric|max:9999999999',
            'numero_registro' => 'required|string|max:20',
            'fecha_inscripcion' => 'required|date|after_or_equal:fecha_constitucion|before_or_equal:today',
        ];

        $messages = [
            'numero_escritura.required' => 'El número de escritura es obligatorio.',
            'numero_escritura.numeric' => 'El número de escritura debe ser numérico.',
            'nombre_notario.required' => 'El nombre del notario es obligatorio.',
            'nombre_notario.regex' => 'El nombre del notario debe contener solo letras, espacios y puntos.',
            'entidad_federativa.required' => 'La entidad federativa es obligatoria.',
            'entidad_federativa.exists' => 'La entidad federativa seleccionada no es válida.',
            'fecha_constitucion.required' => 'La fecha de constitución es obligatoria.',
            'fecha_constitucion.date' => 'La fecha de constitución debe ser una fecha válida.',
            'fecha_constitucion.before_or_equal' => 'La fecha de constitución no puede ser futura.',
            'numero_notario.required' => 'El número de notario es obligatorio.',
            'numero_notario.numeric' => 'El número de notario debe ser numérico.',
            'numero_registro.required' => 'El número de registro es obligatorio.',
            'fecha_inscripcion.required' => 'La fecha de inscripción es obligatoria.',
            'fecha_inscripcion.date' => 'La fecha de inscripción debe ser una fecha válida.',
            'fecha_inscripcion.after_or_equal' => 'La fecha de inscripción no puede ser anterior a la fecha de constitución.',
            'fecha_inscripcion.before_or_equal' => 'La fecha de inscripción no puede ser futura.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Process legal data from section 2
     */
    private function procesarDatosLegales(Request $request, $tramite)
    {
        try {
            Log::info('Input data for procesarDatosLegales:', $request->all());

            // Obtener o crear el detalle_tramite
            $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

            // Crear o actualizar la dirección
            if ($detalle->direccion_id) {
                $direccion = \App\Models\Direccion::find($detalle->direccion_id);
                Log::info('Updating existing address with ID: ' . $detalle->direccion_id);
            } else {
                $direccion = new \App\Models\Direccion();
                Log::info('Creating new address');
            }
            
            // Normalizar el código postal a 5 dígitos
            $codigoPostal = str_pad($request->input('codigo_postal'), 5, '0', STR_PAD_LEFT);
            
            // Obtener el asentamiento basado en el código postal y colonia seleccionada
            $coloniaValue = $request->input('colonia');
            Log::info("Looking for asentamiento with CP: $codigoPostal and colonia: $coloniaValue");
            
            $asentamiento = \App\Models\Asentamiento::where('codigo_postal', $codigoPostal)
                ->where('nombre', $coloniaValue)
                ->first();
                
            if (!$asentamiento) {
                Log::warning("Asentamiento not found for codigo_postal: $codigoPostal and colonia: $coloniaValue");
            } else {
                Log::info("Asentamiento found with ID: " . $asentamiento->id);
            }

            $direccion->codigo_postal = $codigoPostal;
            $direccion->asentamiento_id = $asentamiento ? $asentamiento->id : null;
            $direccion->calle = $request->input('calle');
            $direccion->numero_exterior = $request->input('numero_exterior');
            $direccion->numero_interior = $request->input('numero_interior');
            $direccion->entre_calle_1 = $request->input('entre_calle_1');
            $direccion->entre_calle_2 = $request->input('entre_calle_2');
            $direccion->save();
            
            Log::info('Address saved with ID: ' . $direccion->id);

            // Actualizar el detalle_tramite con el direccion_id
            $detalle->direccion_id = $direccion->id;
            $detalle->save();
            
            Log::info('DetalleTramite updated with direccion_id: ' . $direccion->id);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error procesando datos legales: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    private function procesarAccionistas(Request $request, $tramite)
    {
        try {
            Log::info('Processing shareholders data for section 4:', $request->all());

            // Validar los datos del formulario
            $this->validarDatosSeccion($request, 4, 'Moral');

            // Obtener el array JSON de accionistas
            $accionistasData = $request->input('accionistas');
            
            if (!is_array($accionistasData)) {
                // Si los datos vienen como string JSON, convertirlos a array
                $accionistasData = json_decode($accionistasData, true) ?: [];
            }

            // Eliminar accionistas anteriores de este trámite antes de guardar los nuevos
            $existingIds = AccionistaSolicitante::where('tramite_id', $tramite->id)->pluck('accionista_id')->toArray();
            AccionistaSolicitante::where('tramite_id', $tramite->id)->delete();
            
            Log::info('Previous shareholders removed for tramite: ' . $tramite->id);
            
            // Total de porcentaje para validación
            $totalPorcentaje = 0;
            
            // Procesar cada accionista
            foreach ($accionistasData as $accionistaData) {
                // Validar datos mínimos requeridos
                if (
                    empty($accionistaData['nombre']) || 
                    empty($accionistaData['apellido_paterno']) || 
                    !isset($accionistaData['porcentaje'])
                ) {
                    continue; // Saltar registros inválidos
                }
                
                // Crear o actualizar el accionista
                $accionista = Accionista::firstOrCreate([
                    'nombre' => $accionistaData['nombre'],
                    'apellido_paterno' => $accionistaData['apellido_paterno'],
                    'apellido_materno' => $accionistaData['apellido_materno'] ?? '',
                ]);
                
                // Sumar al total
                $porcentaje = floatval($accionistaData['porcentaje']);
                $totalPorcentaje += $porcentaje;
                
                // Crear la relación con el trámite
                AccionistaSolicitante::create([
                    'tramite_id' => $tramite->id,
                    'accionista_id' => $accionista->id,
                    'porcentaje_participacion' => $porcentaje,
                ]);
                
                Log::info('Shareholder added: ' . $accionista->id . ' with ' . $porcentaje . '% participation');
            }
            
            // Verificar si el total es aproximadamente 100% (con un margen de error pequeño)
            if (abs($totalPorcentaje - 100) > 0.1) {
                Log::warning('Total shareholder percentage is not 100%: ' . $totalPorcentaje);
            } else {
                Log::info('Total shareholder percentage: ' . $totalPorcentaje . '%');
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error processing shareholders data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    // Helpers
    private function obtenerTotalSecciones($tipoPersona)
    {
        return $tipoPersona == 'Física' ? 3 : ($tipoPersona == 'Moral' ? 6 : 5);
    }

    private function obtenerSecciones($tipoPersona)
    {
        if ($tipoPersona == 'Física') {
            return [
                1 => 'Datos Generales',
                2 => 'Iomucilio',
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

    /**
     * Determina el nombre del partial de la sección basado en el número de sección y el tipo de persona
     */
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
        if ($seccion == 1) {
            $rules = [
                'contacto_nombre' => 'required|string|max:40|regex:/^[A-Za-z\s]+$/',
                'contacto_correo' => 'required|email',
                'contacto_telefono' => 'required|regex:/^[0-9]{10}$/',
                'contacto_cargo' => 'required|string|max:50|regex:/^[A-Za-z\s]+$/',
                'contacto_telefono_2' => 'required|regex:/^[0-9]{10}$/',
                'objeto_social' => 'required_if:tipo_persona,Moral|string|max:500|nullable',
            ];

            if (!$request->user()->hasRole('solicitante')) {
                $rules['tipo_persona'] = 'required|in:Física,Moral';
                $rules['rfc'] = 'required|regex:/^[A-Z0-9]{12,13}$/';
                $rules['razon_social'] = 'required_if:tipo_persona,Moral|string|max:100|regex:/^[A-Za-z\s&.,0-9]+$/|nullable';
                $rules['correo_electronico'] = 'required_if:tipo_persona,Moral|email|nullable';
            }

            // OMITIDO: validación de constancia_upload

            $request->validate($rules, [
                'objeto_social.required_if' => 'El objeto social es obligatorio para proveedores Morales.',
                'objeto_social.max' => 'El objeto social no puede exceder los 500 caracteres.',
                'razon_social.required_if' => 'La razón social es obligatoria para proveedores Morales.',
                'correo_electronico.required_if' => 'El correo electrónico es obligatorio para proveedores Morales.',
            ]);
        } elseif ($seccion == 3 && $tipoPersona == 'Moral') {
            $rules = [
                'numero_escritura' => 'required|numeric|max:9999999999',
                'nombre_notario' => 'required|string|max:100',
                'entidad_federativa' => 'required|exists:estado,id',
                'fecha_constitucion' => 'required|date',
                'numero_notario' => 'required|numeric|max:9999999999',
                'numero_registro' => 'required|string|max:20',
                'fecha_inscripcion' => 'required|date',
            ];

            $request->validate($rules);
        } 
        elseif ($seccion == 4 || ($seccion == 3 && $tipoPersona == 'Física')) {
            // Validación para la sección de accionistas (sección 4 para Morales, sección 3 para Físicas)
            $accionistas = $request->input('accionistas');
            
            // Verifica que exista un input de accionistas
            if (empty($accionistas)) {
                return $request->validate([
                    'accionistas' => 'required',
                ], [
                    'accionistas.required' => 'Debe proporcionar al menos un accionista.',
                ]);
            }
            
            // Si es una cadena JSON, intentamos decodificarla
            if (is_string($accionistas)) {
                $accionistasArray = json_decode($accionistas, true);
                
                // Si no es un JSON válido o está vacío
                if (json_last_error() !== JSON_ERROR_NONE || empty($accionistasArray)) {
                    return $request->validate([
                        'accionistas' => 'required|json',
                    ], [
                        'accionistas.json' => 'El formato de datos de los accionistas no es válido.',
                    ]);
                }
                
                // Verifica que sea un array después de decodificar
                if (!is_array($accionistasArray)) {
                    return $request->validate([
                        'accionistas' => 'required',
                    ], [
                        'accionistas.required' => 'Los datos de accionistas deben ser una lista válida.',
                    ]);
                }
            }
            
            // Validar que al menos haya un accionista
            if (is_array($accionistas) && empty($accionistas)) {
                return $request->validate([
                    'accionistas' => 'required',
                ], [
                    'accionistas.required' => 'Debe proporcionar al menos un accionista.',
                ]);
            }
        }
        elseif ($seccion == 5 && $tipoPersona == 'Moral') {
            $rules = [
                'nombre-apoderado' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
                'numero-escritura' => 'required|numeric|max:9999999999',
                'nombre-notario' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
                'numero-notario' => 'required|numeric|max:9999999999',
                'entidad-federativa' => 'required|exists:estado,id',
                'fecha-escritura' => 'required|date|before_or_equal:today',
                'numero-registro' => 'required|string|max:20',
                'fecha-inscripcion' => 'required|date|after_or_equal:fecha-escritura|before_or_equal:today',
            ];

            $messages = [
                'nombre-apoderado.required' => 'El nombre del apoderado legal es obligatorio.',
                'nombre-apoderado.regex' => 'El nombre del apoderado debe contener solo letras, espacios y puntos.',
                'numero-escritura.required' => 'El número de escritura es obligatorio.',
                'numero-escritura.numeric' => 'El número de escritura debe ser numérico.',
                'nombre-notario.required' => 'El nombre del notario es obligatorio.',
                'nombre-notario.regex' => 'El nombre del notario debe contener solo letras, espacios y puntos.',
                'entidad-federativa.required' => 'La entidad federativa es obligatoria.',
                'entidad-federativa.exists' => 'La entidad federativa seleccionada no es válida.',
                'fecha-escritura.required' => 'La fecha de escritura es obligatoria.',
                'fecha-escritura.date' => 'La fecha de escritura debe ser una fecha válida.',
                'fecha-escritura.before_or_equal' => 'La fecha de escritura no puede ser futura.',
                'numero-notario.required' => 'El número de notario es obligatorio.',
                'numero-notario.numeric' => 'El número de notario debe ser numérico.',
                'numero-registro.required' => 'El número de registro es obligatorio.',
                'fecha-inscripcion.required' => 'La fecha de inscripción es obligatoria.',
                'fecha-inscripcion.date' => 'La fecha de inscripción debe ser una fecha válida.',
                'fecha-inscripcion.after_or_equal' => 'La fecha de inscripción no puede ser anterior a la fecha de escritura.',
                'fecha-inscripcion.before_or_equal' => 'La fecha de inscripción no puede ser futura.',
            ];

            return $request->validate($rules, $messages);
        }
    }

    public function obtenerDatosDireccion(Request $request)
    {
        $codigoPostal = $request->input('codigo_postal');

        // Validate postal code
        if (!$codigoPostal || !preg_match('/^\d{4,5}$/', $codigoPostal)) {
            return response()->json([
                'success' => false,
                'message' => 'Código postal inválido. Debe contener 4 o 5 dígitos.'
            ], 400);
        }

        try {
            // Normalize postal code to 5 digits
            $codigoPostal = str_pad($codigoPostal, 5, '0', STR_PAD_LEFT);

            // Query asentamientos with related localidad, municipio, and estado
            $asentamientos = \App\Models\Asentamiento::where('codigo_postal', $codigoPostal)
                ->with(['localidad.municipio.estado'])
                ->get();

            if ($asentamientos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos para el código postal proporcionado.'
                ], 404);
            }

            // Extract estado and municipio from the first asentamiento
            $primerAsentamiento = $asentamientos->first();
            $estado = $primerAsentamiento->localidad->municipio->estado->nombre ?? '';
            $municipio = $primerAsentamiento->localidad->municipio->nombre ?? '';

            // Prepare asentamientos list for the dropdown
            $asentamientosList = $asentamientos->map(function ($asentamiento) {
                return [
                    'id' => $asentamiento->id,
                    'nombre' => $asentamiento->nombre
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'estado' => $estado,
                'municipio' => $municipio,
                'asentamientos' => $asentamientosList
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error obteniendo datos de dirección: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud. Por favor, intenta de nuevo.'
            ], 500);
        }
    }
}