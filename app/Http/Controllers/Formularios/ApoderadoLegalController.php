<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\RepresentanteLegal;
use App\Models\InstrumentoNotarial;

class ApoderadoLegalController extends Controller
{
    public function guardar(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Processing legal representative data:', $request->all());

            // Validate incoming data
            $this->validarDatos($request);

            // Get or create detalle_tramite
            $detalleTramite = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

            // Create a new instrumento_notarial for the legal representative
            $instrumentoNotarial = new InstrumentoNotarial();
            
            // If we already have a representante_legal, use its instrumento_notarial
            if ($detalleTramite->representante_legal_id) {
                $representanteLegal = RepresentanteLegal::find($detalleTramite->representante_legal_id);
                if ($representanteLegal) {
                    $existingInstrument = InstrumentoNotarial::find($representanteLegal->instrumento_notarial_id);
                    if ($existingInstrument) {
                        $instrumentoNotarial = $existingInstrument;
                    }
                }
            }

            // Fill the instrumento_notarial data
            $instrumentoNotarial->numero_escritura = $request->input('numero-escritura');
            $instrumentoNotarial->fecha = $request->input('fecha-escritura'); // Store as raw date (YYYY-MM-DD)
            $instrumentoNotarial->nombre_notario = $request->input('nombre-notario');
            $instrumentoNotarial->numero_notario = $request->input('numero-notario');
            $instrumentoNotarial->estado_id = $request->input('entidad-federativa');
            $instrumentoNotarial->registro_mercantil = $request->input('numero-registro');
            $instrumentoNotarial->fecha_registro = $request->input('fecha-inscripcion'); // Store as raw date (YYYY-MM-DD)
            $instrumentoNotarial->save();
            
            Log::info('InstrumentoNotarial saved with ID: ' . $instrumentoNotarial->id);

            // Get or create representante_legal record
            if ($detalleTramite->representante_legal_id) {
                $representanteLegal = RepresentanteLegal::find($detalleTramite->representante_legal_id);
            }
            
            if (empty($representanteLegal)) {
                $representanteLegal = new RepresentanteLegal();
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

    private function validarDatos(Request $request)
    {
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