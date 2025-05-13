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

class InscripcionController extends Controller
{
    // Muestra la sección actual del formulario de inscripción
public function mostrarFormulario(Request $request)
{
    $user = Auth::user();
    $solicitante = $user->solicitante;
    $isRevisor = $user->hasRole('revisor');

    // If user is a revisor and has no solicitante, use default values for preview
    if ($isRevisor && !$solicitante) {
        $tipoPersona = 'Física'; // Default for preview
        $tramite = null;
        $seccion = 1; // Start at section 1
        $totalSecciones = $this->obtenerTotalSecciones($tipoPersona);
        $datosPrevios = [];
        $porcentaje = 0;
        $seccionesCompletadas = 0;
        $isConfirmationSection = false;
        $direccion = null; // No dirección para revisores sin solicitante
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
    }

    // Cargar sectores para el formulario
    $sectores = Sector::all()->map(function ($sector) {
        return [
            'id' => $sector->id,
            'nombre' => $sector->nombre,
        ];
    })->toArray();

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
        'mostrarCurp' => ($tipoPersona == 'Física' && $user->hasRole('solicitante') && $seccion == 1),
        'sectores' => $sectores,
        'isRevisor' => $isRevisor,
        'direccion' => $direccion, // Pasar la dirección al formulario
    ]);
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

    // Página de éxito
    public function exito()
    {
        return view('inscripcion.exito');
    }

    // Handle form submission for progressing to the next section
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

    // Validate data for the current section
    $this->validarDatosSeccion($request, $seccion, $tipoPersona);

    try {
        DB::beginTransaction();

        if ($seccion == 1) {
            $this->procesarDatosGenerales($request, $tramite);
        }
        elseif ($seccion == 2) {
            // Make sure this is being called for section 2
            Log::info('Processing address data for section 2');
            $this->procesarDatosLegales($request, $tramite);
        }
        // Other section handlers...

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
    /**
     * Process general data from section 1
     */
    private function procesarDatosGenerales(Request $request, $tramite)
    {
        // Get or create detalle_tramite
        $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
        
        // Update detalle_tramite with phone and website
        $detalle->telefono = $request->input('contacto_telefono');
        $detalle->sitio_web = $request->input('contacto_web');
        
        // If admin is creating, set razon_social and email
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
        
        // Process selected activities
        // First, get existing activities for this tramite to avoid duplicates
        $existingActivities = ActividadSolicitante::where('tramite_id', $tramite->id)
            ->pluck('actividad_id')
            ->toArray();
        
        // Get the selected activities from hidden input field or array
        $selectedActivities = $request->input('actividades_seleccionadas', []);
        
        // Ensure $selectedActivities is an array (decode if JSON)
        if (!is_array($selectedActivities) && is_string($selectedActivities)) {
            $selectedActivities = json_decode($selectedActivities, true) ?: [];
        }
        
        // Add new activities
        foreach ($selectedActivities as $activityId) {
            if (!in_array($activityId, $existingActivities)) {
                ActividadSolicitante::create([
                    'tramite_id' => $tramite->id,
                    'actividad_id' => $activityId,
                ]);
            }
        }
        
        // Handle constancia document upload (for admin)
        if (Auth::user()->hasRole('admin') && $request->hasFile('constancia_upload')) {
            $pdfPath = $request->file('constancia_upload')->store('documents', 'public');
            
            // Create documento if doesn't exist
            $documento = Documento::firstOrCreate(
                ['nombre' => 'Constancia SAT'],
                [
                    'tipo' => 'Certificado',
                    'descripcion' => 'Constancia del SAT subida por administrador',
                    'fecha_expiracion' => now()->addYear(),
                ]
            );
            
            // Create documento_solicitante record
            DocumentoSolicitante::create([
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => 1,
                'ruta_archivo' => $pdfPath,
            ]);
        }
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

    /**
     * Process documents from section 3
     */
    private function procesarDocumentos(Request $request, $tramite)
    {
        // Process document uploads for section 3
        // For now, this is a placeholder
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
                2 => 'Información Legal',
                3 => 'Accionistas',
            ];
        } elseif ($tipoPersona == 'Moral') {
            return [
                1 => 'Datos Generales',
                2 => 'Información Legal',
                3 => 'Documentos',
                4 => 'Información Financiera',
                5 => 'Información Técnica',
                6 => 'Accionistas',
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
        ];

        if (!$request->user()->hasRole('solicitante')) {
            $rules['tipo_persona'] = 'required|in:Física,Moral';
            $rules['rfc'] = 'required|regex:/^[A-Z0-9]{12,13}$/';
        }

        if ($request->user()->hasRole('admin')) {
            $rules['constancia_upload'] = 'required|mimes:pdf|max:5120';
        }

        $request->validate($rules);
    } elseif ($seccion == 2) {
        $rules = [
            'codigo_postal' => 'required|regex:/^[0-9]{4,5}$/', // Acepta 4 o 5 dígitos
            'colonia' => 'required|string|max:405',
            'calle' => 'required|string|max:100|regex:/^[A-Za-z0-9\s]+$/',
            'numero_exterior' => 'required|string|max:10|regex:/^[A-Za-z0-9]+$/',
            'numero_interior' => 'nullable|string|max:10|regex:/^[A-Za-z0-9]+$/',
            'entre_calle_1' => 'nullable|string|max:100|regex:/^[A-Za-z0-9\s]+$/',
            'entre_calle_2' => 'nullable|string|max:100|regex:/^[A-Za-z0-9\s]+$/',
        ];

        $request->validate($rules);
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