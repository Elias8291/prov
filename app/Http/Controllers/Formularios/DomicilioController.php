<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use App\Models\Asentamiento;

class DomicilioController extends Controller
{
    public function guardar(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Input data for procesarDatosLegales:', $request->all());

            // Obtener o crear el detalle_tramite
            $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

            // Crear o actualizar la dirección
            if ($detalle->direccion_id) {
                $direccion = Direccion::find($detalle->direccion_id);
                Log::info('Updating existing address with ID: ' . $detalle->direccion_id);
            } else {
                $direccion = new Direccion();
                Log::info('Creating new address');
            }
            
            // Normalizar el código postal a 5 dígitos
            $codigoPostal = str_pad($request->input('codigo_postal'), 5, '0', STR_PAD_LEFT);
            
            // Obtener el asentamiento basado en el código postal y colonia seleccionada
            $coloniaValue = $request->input('colonia');
            Log::info("Looking for asentamiento with CP: $codigoPostal and colonia: $coloniaValue");
            
            $asentamiento = Asentamiento::where('codigo_postal', $codigoPostal)
                ->where('nombre', $coloniaValue)
                ->first();
                
            if (!$asentamiento) {
                Log::warning("Asentamiento not found for codigo_postal: $codigoPostal and colonia: $coloniaValue");
            } else {
                Log::info("Asentamiento found with ID: " . $asentamiento->id);
            }

            // AQUÍ SE CONFIGURA LA DIRECCIÓN CON LOS DATOS OBTENIDOS
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

    // Este método se puede usar desde JavaScript para obtener datos del CP
    public function obtenerDatosPorCodigoPostal(Request $request)
    {
        $codigoPostal = $request->input('codigo_postal');
        
        // Llamar internamente al endpoint que ya tienes
        $response = Http::post(route('inscripcion.obtener-datos-direccion'), [
            'codigo_postal' => $codigoPostal
        ]);
        
        return $response->json();
    }
}