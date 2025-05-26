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

            $accionistasData = $request->input('accionistas');
            
            if (!is_array($accionistasData)) {
                $accionistasData = json_decode($accionistasData, true) ?: [];
            }

            AccionistaSolicitante::where('tramite_id', $tramite->id)->delete();
            
            Log::info('Previous shareholders removed for tramite: ' . $tramite->id);
            
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

 public function getShareholdersData(Tramite $tramite)
{
    try {
        Log::info('Fetching shareholders data for tramite: ' . $tramite->id);

        $accionistas = AccionistaSolicitante::where('tramite_id', $tramite->id)
            ->with('accionista')
            ->get()
            ->map(function ($accionistaSolicitante) {
                return [
                    'id' => $accionistaSolicitante->accionista->id ?? 0,
                    'nombre' => is_string($accionistaSolicitante->accionista->nombre) 
                        ? $accionistaSolicitante->accionista->nombre 
                        : (is_array($accionistaSolicitante->accionista->nombre) 
                            ? json_encode($accionistaSolicitante->accionista->nombre) 
                            : 'No disponible'),
                    'apellido_paterno' => is_string($accionistaSolicitante->accionista->apellido_paterno) 
                        ? $accionistaSolicitante->accionista->apellido_paterno 
                        : (is_array($accionistaSolicitante->accionista->apellido_paterno) 
                            ? json_encode($accionistaSolicitante->accionista->apellido_paterno) 
                            : 'No disponible'),
                    'apellido_materno' => is_string($accionistaSolicitante->accionista->apellido_materno) 
                        ? $accionistaSolicitante->accionista->apellido_materno 
                        : (is_array($accionistaSolicitante->accionista->apellido_materno) 
                            ? json_encode($accionistaSolicitante->accionista->apellido_materno) 
                            : ''),
                    'porcentaje_participacion' => is_numeric($accionistaSolicitante->porcentaje_participacion) 
                        ? $accionistaSolicitante->porcentaje_participacion 
                        : (is_array($accionistaSolicitante->porcentaje_participacion) 
                            ? json_encode($accionistaSolicitante->porcentaje_participacion) 
                            : 0),
                ];
            })
            ->toArray();

        Log::info('Shareholders data retrieved: ', $accionistas);

        return $accionistas;
    } catch (\Exception $e) {
        Log::error('Error fetching shareholders data: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return []; // Return empty array to avoid breaking the view
    }
}
}