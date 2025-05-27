<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\Accionista;
use App\Models\AccionistaSolicitante;
use Illuminate\Support\Facades\DB;

class AccionistasController extends Controller
{
    /**
     * Guarda los datos de los accionistas para un trámite específico
     *
     * @param Request $request La solicitud con los datos de los accionistas
     * @param Tramite $tramite El trámite asociado
     * @return bool Indica si la operación fue exitosa
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $this->validateRequest($request);

        return DB::transaction(function () use ($request, $tramite) {
            $accionistasData = $this->parseAccionistasData($request);
            $this->deleteExistingAccionistas($tramite);
            $totalPorcentaje = $this->saveAccionistas($tramite, $accionistasData);
            $this->validateTotalPorcentaje($totalPorcentaje, $tramite);

            return true;
        });
    }

    /**
     * Valida los datos de la solicitud de accionistas
     *
     * @param Request $request La solicitud a validar
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request)
    {
        $rules = [
            'accionistas' => 'required',
            'accionistas.*.nombre' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
            'accionistas.*.apellido_paterno' => 'required|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
            'accionistas.*.apellido_materno' => 'nullable|string|max:100|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\.]+$/',
            'accionistas.*.porcentaje' => 'required|numeric|min:0|max:100',
        ];

        $messages = [
            'accionistas.required' => 'Los datos de los accionistas son obligatorios.',
            'accionistas.*.nombre.required' => 'El nombre del accionista es obligatorio.',
            'accionistas.*.nombre.regex' => 'El nombre del accionista debe contener solo letras, espacios y puntos.',
            'accionistas.*.apellido_paterno.required' => 'El apellido paterno del accionista es obligatorio.',
            'accionistas.*.apellido_paterno.regex' => 'El apellido paterno debe contener solo letras, espacios y puntos.',
            'accionistas.*.apellido_materno.regex' => 'El apellido materno debe contener solo letras, espacios y puntos.',
            'accionistas.*.porcentaje.required' => 'El porcentaje de participación es obligatorio.',
            'accionistas.*.porcentaje.numeric' => 'El porcentaje debe ser un valor numérico.',
            'accionistas.*.porcentaje.min' => 'El porcentaje no puede ser negativo.',
            'accionistas.*.porcentaje.max' => 'El porcentaje no puede exceder el 100%.',
        ];

        $request->validate($rules, $messages);
    }

    /**
     * Procesa y normaliza los datos de los accionistas recibidos
     *
     * @param Request $request La solicitud con los datos
     * @return array Los datos normalizados de los accionistas
     */
    private function parseAccionistasData(Request $request): array
    {
        $accionistasData = $request->input('accionistas');
        Log::info('Processing shareholders data:', $request->all());

        if (!is_array($accionistasData)) {
            $accionistasData = json_decode($accionistasData, true) ?: [];
        }

        return $accionistasData;
    }

    /**
     * Elimina los registros de accionistas existentes para el trámite
     *
     * @param Tramite $tramite El trámite asociado
     * @return void
     */
    private function deleteExistingAccionistas(Tramite $tramite)
    {
        AccionistaSolicitante::where('tramite_id', $tramite->id)->delete();
        Log::info('Previous shareholders removed for tramite: ' . $tramite->id);
    }

    /**
     * Guarda los datos de los accionistas y calcula el porcentaje total
     *
     * @param Tramite $tramite El trámite asociado
     * @param array $accionistasData Los datos de los accionistas
     * @return float El porcentaje total de participación
     */
    private function saveAccionistas(Tramite $tramite, array $accionistasData): float
    {
        $totalPorcentaje = 0;

        foreach ($accionistasData as $accionistaData) {
            if (
                empty($accionistaData['nombre']) ||
                empty($accionistaData['apellido_paterno']) ||
                !isset($accionistaData['porcentaje'])
            ) {
                continue;
            }

            $accionista = Accionista::firstOrCreate([
                'nombre' => $accionistaData['nombre'],
                'apellido_paterno' => $accionistaData['apellido_paterno'],
                'apellido_materno' => $accionistaData['apellido_materno'] ?? '',
            ]);

            $porcentaje = floatval($accionistaData['porcentaje']);
            $totalPorcentaje += $porcentaje;

            AccionistaSolicitante::create([
                'tramite_id' => $tramite->id,
                'accionista_id' => $accionista->id,
                'porcentaje_participacion' => $porcentaje,
            ]);

            Log::info('Shareholder added: ' . $accionista->id . ' with ' . $porcentaje . '% participation');
        }

        return $totalPorcentaje;
    }

    /**
     * Valida que el porcentaje total de participación sea aproximadamente 100%
     *
     * @param float $totalPorcentaje El porcentaje total calculado
     * @param Tramite $tramite El trámite asociado
     * @return void
     */
    private function validateTotalPorcentaje(float $totalPorcentaje, Tramite $tramite)
    {
        if (abs($totalPorcentaje - 100) > 0.1) {
            Log::warning('Total shareholder percentage is not 100% for tramite ' . $tramite->id . ': ' . $totalPorcentaje);
        } else {
            Log::info('Total shareholder percentage for tramite ' . $tramite->id . ': ' . $totalPorcentaje . '%');
        }
    }

    /**
     * Obtiene los datos de los accionistas asociados a un trámite
     *
     * @param Tramite $tramite El trámite del cual obtener los accionistas
     * @return array Los datos de los accionistas
     */
    public function getShareholdersData(Tramite $tramite)
    {
        try {
            Log::info('Fetching shareholders data for tramite: ' . $tramite->id);

            $accionistas = AccionistaSolicitante::where('tramite_id', $tramite->id)
                ->with('accionista')
                ->get()
                ->map(function ($accionistaSolicitante) {
                    return $this->formatAccionistaData($accionistaSolicitante);
                })
                ->toArray();

            Log::info('Shareholders data retrieved: ', $accionistas);

            return $accionistas;
        } catch (\Exception $e) {
            Log::error('Error fetching shareholders data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return [];
        }
    }

    /**
     * Formatea los datos de un accionista para la respuesta
     *
     * @param AccionistaSolicitante $accionistaSolicitante El registro del accionista
     * @return array Los datos formateados del accionista
     */
    private function formatAccionistaData(AccionistaSolicitante $accionistaSolicitante): array
    {
        return [
            'id' => $accionistaSolicitante->accionista->id ?? 0,
            'nombre' => $this->safeString($accionistaSolicitante->accionista->nombre, 'No disponible'),
            'apellido_paterno' => $this->safeString($accionistaSolicitante->accionista->apellido_paterno, 'No disponible'),
            'apellido_materno' => $this->safeString($accionistaSolicitante->accionista->apellido_materno, ''),
            'porcentaje_participacion' => $this->safeNumeric($accionistaSolicitante->porcentaje_participacion, 0),
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
        if (is_string($value)) {
            return $value;
        }
        return is_array($value) ? json_encode($value) : $default;
    }

    /**
     * Convierte un valor a numérico seguro, manejando casos no válidos
     *
     * @param mixed $value El valor a convertir
     * @param float $default El valor por defecto si no es válido
     * @return float
     */
    private function safeNumeric($value, float $default): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }
        return is_array($value) ? floatval(json_encode($value)) : $default;
    }
}