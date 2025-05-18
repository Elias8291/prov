<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Tramite;
use App\Models\Accionista;
use App\Models\AccionistaSolicitante;

class AccionistasController extends Controller
{
    public function guardar(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Processing shareholders data:', $request->all());

            // Obtener el array JSON de accionistas
            $accionistasData = $request->input('accionistas');
            
            if (!is_array($accionistasData)) {
                // Si los datos vienen como string JSON, convertirlos a array
                $accionistasData = json_decode($accionistasData, true) ?: [];
            }

            // Eliminar accionistas anteriores de este trámite antes de guardar los nuevos
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
}