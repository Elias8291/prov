<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Asentamiento;
use App\Models\Localidad;
use App\Models\Municipio;
use App\Models\Estado;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionController extends Controller
{
    /**
     * Get address information for the current authenticated solicitante
     */
    public function getSolicitanteAddressInfo()
    {
        // Get the current user
        $user = Auth::user();
        
        if (!$user || !$user->solicitante) {
            return response()->json(['success' => false, 'message' => 'No solicitante found for this user'], 404);
        }
        
        // Get the most recent tramite for this solicitante
        $tramite = Tramite::where('solicitante_id', $user->solicitante->id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$tramite) {
            return response()->json(['success' => false, 'message' => 'No tramite found for this solicitante'], 404);
        }
        
        // Get the detalle_tramite
        $detalleTramite = DetalleTramite::where('tramite_id', $tramite->id)->first();
        
        if (!$detalleTramite || !$detalleTramite->direccion_id) {
            return response()->json(['success' => false, 'message' => 'No address found for this tramite'], 404);
        }
        
        // Get the direccion
        $direccion = Direccion::find($detalleTramite->direccion_id);
        
        if (!$direccion) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'codigo_postal' => $direccion->codigo_postal,
                'calle' => $direccion->calle,
                'numero_exterior' => $direccion->numero_exterior,
                'numero_interior' => $direccion->numero_interior,
                'entre_calle_1' => $direccion->entre_calle_1,
                'entre_calle_2' => $direccion->entre_calle_2,
                'asentamiento_id' => $direccion->asentamiento_id
            ]
        ]);
    }
    
    /**
     * Get address information by postal code
     */
    public function getAddressByCodigoPostal($codigo)
    {
        // Find asentamientos with this postal code
        $asentamientos = Asentamiento::where('codigo_postal', $codigo)->get();
        
        if ($asentamientos->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No locations found with this postal code'], 404);
        }
        
        // Get a sample asentamiento to retrieve location data
        $sampleAsentamiento = $asentamientos->first();
        $localidad = Localidad::find($sampleAsentamiento->localidad_id);
        $municipio = $localidad ? Municipio::find($localidad->municipio_id) : null;
        $estado = $municipio ? Estado::find($municipio->estado_id) : null;
        
        return response()->json([
            'success' => true,
            'estado' => $estado ? $estado->nombre : '',
            'municipio' => $municipio ? $municipio->nombre : '',
            'asentamientos' => $asentamientos->map(function($asentamiento) {
                return [
                    'id' => $asentamiento->id,
                    'nombre' => $asentamiento->nombre,
                    'tipo' => $asentamiento->tipoAsentamiento ? $asentamiento->tipoAsentamiento->nombre : ''
                ];
            })
        ]);
    }
}