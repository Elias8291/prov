<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asentamiento;
use App\Models\Localidad;
use App\Models\Municipio;
use App\Models\Estado;
class DireccionController extends Controller
{
   
public function getByCodigoPostal($codigoPostal)
{
    try {
        // Validar que el código postal tenga 5 dígitos
        if (!preg_match('/^\d{5}$/', $codigoPostal)) {
            return response()->json(['error' => 'Código postal inválido'], 400);
        }

        // Buscar asentamientos con el código postal
        $asentamientos = Asentamiento::with(['localidad.municipio.estado'])
            ->where('codigo_postal', $codigoPostal)
            ->get();

        if ($asentamientos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron asentamientos para este código postal'], 404);
        }

        // Obtener datos comunes (estado y municipio son los mismos para todos los asentamientos)
        $firstAsentamiento = $asentamientos->first();
        $localidad = $firstAsentamiento->localidad;
        $municipio = $localidad->municipio;
        $estado = $municipio->estado;

        // Formatear la respuesta
        $response = [
            'codigo_postal' => $codigoPostal,
            'estado' => $estado->nombre,
            'municipio' => $municipio->nombre,
            'asentamientos' => $asentamientos->map(function ($asentamiento) {
                return [
                    'id' => $asentamiento->id,
                    'nombre' => $asentamiento->nombre,
                    'tipo_asentamiento' => $asentamiento->tipoAsentamiento->nombre
                ];
            })
        ];

        return response()->json($response);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al buscar datos de dirección'], 500);
    }
}
}
