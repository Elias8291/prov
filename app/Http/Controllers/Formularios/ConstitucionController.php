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
use Illuminate\Support\Facades\DB;

class ConstitucionController extends Controller
{
    /**
     * Obtiene los datos de constitución para un trámite, formateando fechas en español
     *
     * @param Tramite $tramite El trámite del cual obtener los datos de constitución
     * @return array Los datos de constitución formateados
     */
    public function getIncorporationData(Tramite $tramite): array
    {
        setlocale(LC_TIME, 'es_MX.UTF-8', 'es_ES.UTF-8', 'Spanish_Mexico');

        $incorporationData = $this->initializeIncorporationData();
        $detalleTramite = $this->getDetalleTramite($tramite);

        if ($detalleTramite?->datoConstitutivo?->instrumentoNotarial) {
            $this->fillIncorporationData($detalleTramite->datoConstitutivo->instrumentoNotarial, $incorporationData);
        }

        return $incorporationData;
    }

    /**
     * Guarda los datos de constitución para un trámite específico
     *
     * @param Request $request La solicitud con los datos de constitución
     * @param Tramite $tramite El trámite asociado
     * @return bool Indica si la operación fue exitosa
     * @throws \Illuminate\Validation\ValidationException|\Exception
     */
    public function guardar(Request $request, Tramite $tramite): bool
    {
        $this->validateRequest($request);

        return DB::transaction(function () use ($request, $tramite) {
            $detalleTramite = $this->getOrCreateDetalleTramite($tramite);
            $instrumentoNotarial = $this->guardarInstrumentoNotarial($request, $detalleTramite->datoConstitutivo?->instrumento_notarial_id);
            $datoConstitutivo = $this->guardarDatoConstitutivo($instrumentoNotarial->id, $detalleTramite->dato_constitutivo_id);

            $detalleTramite->dato_constitutivo_id = $datoConstitutivo->id;
            $detalleTramite->save();

            return true;
        });
    }

    /**
     * Valida los datos recibidos en la solicitud para los campos de constitución
     *
     * @param Request $request La solicitud a validar
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request): void
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

        $request->validate($rules, $messages);
    }

    /**
     * Inicializa el array de datos de constitución con valores por defecto
     *
     * @return array Los datos de constitución inicializados
     */
    private function initializeIncorporationData(): array
    {
        return [
            'numero_escritura' => 'No disponible',
            'nombre_notario' => 'No disponible',
            'entidad_federativa' => 'No disponible',
            'fecha_constitucion' => 'No disponible',
            'numero_notario' => 'No disponible',
            'numero_registro' => 'No disponible',
            'fecha_inscripcion' => 'No disponible',
        ];
    }

    /**
     * Obtiene el registro de DetalleTramite para el trámite especificado
     *
     * @param Tramite $tramite El trámite asociado
     * @return DetalleTramite|null El registro de DetalleTramite o null si no existe
     */
    private function getDetalleTramite(Tramite $tramite): ?DetalleTramite
    {
        return DetalleTramite::where('tramite_id', $tramite->id)->first();
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
     * Llena el array de datos de constitución con información del instrumento notarial
     *
     * @param InstrumentoNotarial $instrumentoNotarial El registro del instrumento notarial
     * @param array &$incorporationData El array de datos a llenar
     * @return void
     */
    private function fillIncorporationData(InstrumentoNotarial $instrumentoNotarial, array &$incorporationData): void
    {
        $incorporationData['numero_escritura'] = $this->safeString($instrumentoNotarial->numero_escritura, 'No disponible');
        $incorporationData['nombre_notario'] = $this->safeString($instrumentoNotarial->nombre_notario, 'No disponible');
        $incorporationData['numero_notario'] = $this->safeString($instrumentoNotarial->numero_notario, 'No disponible');
        $incorporationData['fecha_constitucion'] = $instrumentoNotarial->fecha
            ? strftime('%d de %B de %Y', $instrumentoNotarial->fecha->getTimestamp())
            : 'No disponible';
        $incorporationData['numero_registro'] = $this->safeString($instrumentoNotarial->registro_mercantil, 'No disponible');
        $incorporationData['fecha_inscripcion'] = $instrumentoNotarial->fecha_registro
            ? strftime('%d de %B de %Y', $instrumentoNotarial->fecha_registro->getTimestamp())
            : 'No disponible';
        $incorporationData['entidad_federativa'] = $instrumentoNotarial->estado?->nombre ?? 'No disponible';
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

        $instrumentoNotarial->numero_escritura = $request->input('numero_escritura');
        $instrumentoNotarial->fecha = $request->input('fecha_constitucion');
        $instrumentoNotarial->nombre_notario = $request->input('nombre_notario');
        $instrumentoNotarial->numero_notario = $request->input('numero_notario');
        $instrumentoNotarial->estado_id = $request->input('entidad_federativa');
        $instrumentoNotarial->registro_mercantil = $request->input('numero_registro');
        $instrumentoNotarial->fecha_registro = $request->input('fecha_inscripcion');
        $instrumentoNotarial->save();

        return $instrumentoNotarial;
    }

    /**
     * Crea o actualiza un registro de datos constitutivos con el objeto social del solicitante
     *
     * @param int $instrumentoNotarialId El ID del instrumento notarial asociado
     * @param int|null $datoConstitutivoId El ID del dato constitutivo existente, si aplica
     * @return DatoConstitutivo El registro de datos constitutivos creado o actualizado
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function guardarDatoConstitutivo(int $instrumentoNotarialId, ?int $datoConstitutivoId): DatoConstitutivo
    {
        $datoConstitutivo = $datoConstitutivoId ? DatoConstitutivo::findOrFail($datoConstitutivoId) : new DatoConstitutivo();

        $datoConstitutivo->instrumento_notarial_id = $instrumentoNotarialId;
        $datoConstitutivo->objeto_social = $this->safeString(Auth::user()->solicitante->objeto_social, '');
        $datoConstitutivo->save();

        return $datoConstitutivo;
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
        if (is_string($value)) {
            return $value;
        }
        return is_array($value) ? json_encode($value) : $default;
    }
}