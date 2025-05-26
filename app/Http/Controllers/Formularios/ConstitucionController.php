<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\DatoConstitutivo;
use App\Models\InstrumentoNotarial;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConstitucionController extends Controller
{
    // Obtiene datos de constitución para formulario3
    public function getIncorporationData(Tramite $tramite)
    {
        try {
            Log::info('Fetching incorporation data for tramite ID: ' . $tramite->id);

            // Set Spanish locale for date formatting
            setlocale(LC_TIME, 'es_MX.UTF-8', 'es_ES.UTF-8', 'Spanish_Mexico');

            $incorporationData = [
                'numero_escritura' => 'No disponible',
                'nombre_notario' => 'No disponible',
                'entidad_federativa' => 'No disponible',
                'fecha_constitucion' => 'No disponible',
                'numero_notario' => 'No disponible',
                'numero_registro' => 'No disponible',
                'fecha_inscripcion' => 'No disponible',
            ];

            $detalleTramite = DetalleTramite::where('tramite_id', $tramite->id)->first();

            if ($detalleTramite && $detalleTramite->dato_constitutivo_id) {
                $datoConstitutivo = DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);

                if ($datoConstitutivo && $datoConstitutivo->instrumento_notarial_id) {
                    $instrumentoNotarial = InstrumentoNotarial::find($datoConstitutivo->instrumento_notarial_id);

                    if ($instrumentoNotarial) {
                        $incorporationData['numero_escritura'] = $instrumentoNotarial->numero_escritura ?? 'No disponible';
                        $incorporationData['nombre_notario'] = $instrumentoNotarial->nombre_notario ?? 'No disponible';
                        $incorporationData['numero_notario'] = $instrumentoNotarial->numero_notario ?? 'No disponible';
                        // Format dates to "d de MMMM de YYYY" (e.g., "25 de mayo de 2025")
                        $incorporationData['fecha_constitucion'] = $instrumentoNotarial->fecha
                            ? strftime('%d de %B de %Y', $instrumentoNotarial->fecha->getTimestamp())
                            : 'No disponible';
                        $incorporationData['numero_registro'] = $instrumentoNotarial->registro_mercantil ?? 'No disponible';
                        $incorporationData['fecha_inscripcion'] = $instrumentoNotarial->fecha_registro
                            ? strftime('%d de %B de %Y', $instrumentoNotarial->fecha_registro->getTimestamp())
                            : 'No disponible';

                        if ($instrumentoNotarial->estado_id) {
                            $estado = Estado::find($instrumentoNotarial->estado_id);
                            $incorporationData['entidad_federativa'] = $estado ? $estado->nombre : 'No disponible';
                        }
                    }
                }
            }

            return $incorporationData;
        } catch (\Exception $e) {
            Log::error('Error fetching incorporation data for tramite ID: ' . $tramite->id . ' - ' . $e->getMessage());
            return $incorporationData;
        }
    }

    // Guarda datos de constitución
    public function guardar(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Processing incorporation data for section 3:', $request->all());

            $this->validarDatos($request);

            $detalleTramite = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
            $instrumentoNotarial = new InstrumentoNotarial();

            if ($detalleTramite->dato_constitutivo_id) {
                $datoConstitutivo = DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);
                if ($datoConstitutivo && $datoConstitutivo->instrumento_notarial_id) {
                    $existingInstrument = InstrumentoNotarial::find($datoConstitutivo->instrumento_notarial_id);
                    if ($existingInstrument) {
                        $instrumentoNotarial = $existingInstrument;
                    }
                }
            }

            $instrumentoNotarial->numero_escritura = $request->input('numero_escritura');
            $instrumentoNotarial->fecha = $request->input('fecha_constitucion');
            $instrumentoNotarial->nombre_notario = $request->input('nombre_notario');
            $instrumentoNotarial->numero_notario = $request->input('numero_notario');
            $instrumentoNotarial->estado_id = $request->input('entidad_federativa');
            $instrumentoNotarial->registro_mercantil = $request->input('numero_registro');
            $instrumentoNotarial->fecha_registro = $request->input('fecha_inscripcion');
            $instrumentoNotarial->save();

            if ($detalleTramite->dato_constitutivo_id) {
                $datoConstitutivo = DatoConstitutivo::find($detalleTramite->dato_constitutivo_id);
            }

            if (empty($datoConstitutivo)) {
                $datoConstitutivo = new DatoConstitutivo();
            }

            $datoConstitutivo->instrumento_notarial_id = $instrumentoNotarial->id;
            $datoConstitutivo->objeto_social = Auth::user()->solicitante->objeto_social ?? '';
            $datoConstitutivo->save();

            $detalleTramite->dato_constitutivo_id = $datoConstitutivo->id;
            $detalleTramite->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing incorporation data: ' . $e->getMessage());
            throw $e;
        }
    }

    // Valida datos de entrada
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