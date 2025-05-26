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
    /**
     * Fetch formatted address data for a given Tramite.
     *
     * @param Tramite $tramite
     * @return array
     */
    public function getAddressData(Tramite $tramite)
    {
        $addressData = [
            'codigo_postal' => 'No disponible',
            'estado' => 'No disponible',
            'municipio' => 'No disponible',
            'colonia' => 'No disponible',
            'calle' => 'No disponible',
            'numero_exterior' => 'No disponible',
            'numero_interior' => 'No disponible',
            'entre_calle_1' => 'No disponible',
            'entre_calle_2' => 'No disponible',
        ];

        try {
            $detalle = DetalleTramite::where('tramite_id', $tramite->id)->first();

            if ($detalle && $detalle->direccion) {
                $direccion = $detalle->direccion;
                $addressData['codigo_postal'] = $direccion->codigo_postal ?? 'No disponible';
                $addressData['calle'] = $direccion->calle ?? 'No disponible';
                $addressData['numero_exterior'] = $direccion->numero_exterior ?? 'No disponible';
                $addressData['numero_interior'] = $direccion->numero_interior ?? 'No disponible';
                $addressData['entre_calle_1'] = $direccion->entre_calle_1 ?? 'No disponible';
                $addressData['entre_calle_2'] = $direccion->entre_calle_2 ?? 'No disponible';

                if ($direccion->asentamiento) {
                    $addressData['colonia'] = $direccion->asentamiento->nombre ?? 'No disponible';
                    if ($direccion->asentamiento->localidad && $direccion->asentamiento->localidad->municipio) {
                        $addressData['municipio'] = $direccion->asentamiento->localidad->municipio->nombre ?? 'No disponible';
                        $addressData['estado'] = $direccion->asentamiento->localidad->municipio->estado->nombre ?? 'No disponible';
                    }
                }
            }

            Log::info('Address data retrieved for tramite ID: ' . $tramite->id, $addressData);
        } catch (\Exception $e) {
            Log::error('Error fetching address data for tramite ID: ' . $tramite->id . ' - ' . $e->getMessage());
        }

        return $addressData;
    }

    public function guardar(Request $request, Tramite $tramite)
    {
        try {
            Log::info('Input data for procesarDatosLegales:', $request->all());

            $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);

            if ($detalle->direccion_id) {
                $direccion = Direccion::find($detalle->direccion_id);
                Log::info('Updating existing address with ID: ' . $detalle->direccion_id);
            } else {
                $direccion = new Direccion();
                Log::info('Creating new address');
            }

            $codigoPostal = str_pad($request->input('codigo_postal'), 5, '0', STR_PAD_LEFT);
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

            $direccion->codigo_postal = $codigoPostal;
            $direccion->asentamiento_id = $asentamiento ? $asentamiento->id : null;
            $direccion->calle = $request->input('calle');
            $direccion->numero_exterior = $request->input('numero_exterior');
            $direccion->numero_interior = $request->input('numero_interior');
            $direccion->entre_calle_1 = $request->input('entre_calle_1');
            $direccion->entre_calle_2 = $request->input('entre_calle_2');
            $direccion->save();

            Log::info('Address saved with ID: ' . $direccion->id);

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

    public function obtenerDatosPorCodigoPostal(Request $request)
    {
        $codigoPostal = $request->input('codigo_postal');

        $response = Http::post(route('inscripcion.obtener-datos-direccion'), [
            'codigo_postal' => $codigoPostal
        ]);

        return $response->json();
    }
}