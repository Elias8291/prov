<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\DatoConstitutivo;
use App\Models\InstrumentoNotarial;

class ConstitucionController extends Controller
{
    public function guardar(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Processing incorporation data for section 3:', $request->all());

            // Validate the form data
            $this->validarDatos($request);

            // Get the detalle_tramite first
            $detalleTramite = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
            
            // Create a new instrumento_notarial
            $instrumentoNotarial = new InstrumentoNotarial();

            // If we already have an existing instrument linked to this tramite, use that one instead
            if ($detalleTramite->dato_constitutivo_id) {
                $datoConstitutivo = DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);
                if ($datoConstitutivo && $datoConstitutivo->instrumento_notarial_id) {
                    $existingInstrument = InstrumentoNotarial::find($datoConstitutivo->instrumento_notarial_id);
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
                $datoConstitutivo = DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);
            }
            
            if (empty($datoConstitutivo)) {
                $datoConstitutivo = new DatoConstitutivo();
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

    private function validarDatos(Request $request)
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
}