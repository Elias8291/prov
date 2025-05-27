<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\RepresentanteLegal;
use App\Models\InstrumentoNotarial;
use Illuminate\Support\Facades\DB;
use DateTime;

class ApoderadoLegalController extends Controller
{
    /**
     * Guarda los datos del apoderado legal para un trámite específico
     *
     * @param Request $request La solicitud con los datos del apoderado legal
     * @param Tramite $tramite El trámite asociado
     * @return bool Indica si la operación fue exitosa
     * @throws \Illuminate\Validation\ValidationException|\Exception
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $this->validateRequest($request);

        return DB::transaction(function () use ($request, $tramite) {
            $detalleTramite = $this->getOrCreateDetalleTramite($tramite);
            $instrumentoNotarial = $this->guardarInstrumentoNotarial($request, $detalleTramite->representanteLegal?->instrumento_notarial_id);
            $representanteLegal = $this->guardarRepresentanteLegal($request, $instrumentoNotarial->id, $detalleTramite->representante_legal_id);

            $detalleTramite->representante_legal_id = $representanteLegal->id;
            $detalleTramite->save();

            return true;
        });
    }

    /**
     * Valida los datos recibidos en la solicitud
     *
     * @param Request $request La solicitud a validar
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request)
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

        $request->validate($rules, $messages);
    }

    /**
     * Obtiene o crea un registro de DetalleTramite para el trámite especificado
     *
     * @param Tramite $tramite El trámite asociado
     * @return DetalleTramite El registro de DetalleTramite
     */
    private function getOrCreateDetalleTramite(Tramite $tramite): DetalleTramite
    {
        return DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
    }

    /**
     * Crea o actualiza un registro de instrumento notarial con los datos proporcionados
     *
     * @param Request $request La solicitud con los datos del instrumento notarial
     * @param int|null $instrumentoId El ID del instrumento notarial existente, si aplica
     * @return InstrumentoNotarial El registro del instrumento notarial creado o actualizado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function guardarInstrumentoNotarial(Request $request, ?int $instrumentoId): InstrumentoNotarial
    {
        $instrumentoNotarial = $instrumentoId ? InstrumentoNotarial::findOrFail($instrumentoId) : new InstrumentoNotarial();

        $instrumentoNotarial->numero_escritura = $request->input('numero-escritura');
        $instrumentoNotarial->fecha = $request->input('fecha-escritura');
        $instrumentoNotarial->nombre_notario = $request->input('nombre-notario');
        $instrumentoNotarial->numero_notario = $request->input('numero-notario');
        $instrumentoNotarial->estado_id = $request->input('entidad-federativa');
        $instrumentoNotarial->registro_mercantil = $request->input('numero-registro');
        $instrumentoNotarial->fecha_registro = $request->input('fecha-inscripcion');
        $instrumentoNotarial->save();

        return $instrumentoNotarial;
    }

    /**
     * Crea o actualiza un registro de representante legal con los datos proporcionados
     *
     * @param Request $request La solicitud con los datos del representante legal
     * @param int $instrumentoNotarialId El ID del instrumento notarial asociado
     * @param int|null $representanteId El ID del representante legal existente, si aplica
     * @return RepresentanteLegal El registro del representante legal creado o actualizado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function guardarRepresentanteLegal(Request $request, int $instrumentoNotarialId, ?int $representanteId): RepresentanteLegal
    {
        $representanteLegal = $representanteId ? RepresentanteLegal::findOrFail($representanteId) : new RepresentanteLegal();

        $representanteLegal->nombre = $request->input('nombre-apoderado');
        $representanteLegal->instrumento_notarial_id = $instrumentoNotarialId;
        $representanteLegal->save();

        return $representanteLegal;
    }
    
    /**
     * Obtiene y formatea los datos del apoderado legal para un trámite
     *
     * @param Tramite $tramite El trámite del cual obtener los datos del apoderado legal
     * @return array Los datos del apoderado legal formateados
     */
    public function getDatosApoderadoLegal(Tramite $tramite): array
    {
        // Inicializar con datos por defecto
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

        // Verificar si el trámite tiene detalle y representante legal
        if (!$tramite->detalleTramite || !$tramite->detalleTramite->representanteLegal) {
            return $legalRepresentativeData;
        }
        
        $representanteLegal = $tramite->detalleTramite->representanteLegal;
        $instrumentoNotarial = $representanteLegal->instrumentoNotarial;
        
        if (!$instrumentoNotarial) {
            return $legalRepresentativeData;
        }

        // Definir los nombres de los meses en español
        $monthNames = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];
        
        // Formatear fechas en español
        $formatFecha = function($fecha) use ($monthNames) {
            if (!$fecha) return 'No disponible';
            
            // Intentar usar strftime si está disponible (PHP < 8.1)
            if (function_exists('strftime') && setlocale(LC_TIME, 'es_ES.UTF-8', 'es_MX.UTF-8', 'Spanish_Spain', 'Spanish_Mexico')) {
                return strftime('%d \d\e %B \d\e %Y', strtotime($fecha));
            } else {
                // Alternativa usando DateTime
                $date = new DateTime($fecha);
                return $date->format('j \d\e ') . $monthNames[(int)$date->format('n')] . $date->format(' \d\e Y');
            }
        };

        // Llenar los datos con la información disponible
        return [
            'nombre_apoderado' => $this->safeString($representanteLegal->nombre, 'No disponible'),
            'numero_escritura_apoderado' => $this->safeString($instrumentoNotarial->numero_escritura, 'No disponible'),
            'fecha_escritura_apoderado' => $formatFecha($instrumentoNotarial->fecha),
            'nombre_notario_apoderado' => $this->safeString($instrumentoNotarial->nombre_notario, 'No disponible'),
            'numero_notario_apoderado' => $this->safeString($instrumentoNotarial->numero_notario, 'No disponible'),
            'entidad_federativa_apoderado' => $instrumentoNotarial->estado ? $instrumentoNotarial->estado->nombre : 'No disponible',
            'numero_registro_apoderado' => $this->safeString($instrumentoNotarial->registro_mercantil, 'No disponible'),
            'fecha_inscripcion_apoderado' => $formatFecha($instrumentoNotarial->fecha_registro),
        ];
    }
    
    /**
     * Convierte un valor a string seguro, manejando casos no válidos
     *
     * @param mixed $value El valor a convertir
     * @param string $default El valor por defecto si no es válido
     * @return string
     */
    private function safeString($value, string $default): string
    {
        if (is_string($value) || is_numeric($value)) {
            return (string)$value;
        }
        return is_array($value) ? json_encode($value) : $default;
    }
}