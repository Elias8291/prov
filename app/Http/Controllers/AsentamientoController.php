<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asentamiento;

class AsentamientoController extends Controller
{
    public function getByCodigoPostal(Request $request)
    {
        $codigoPostal = $request->query('codigo_postal');

        // Normalizar a 5 dígitos
        $codigoPostal = str_pad($codigoPostal, 5, '0', STR_PAD_LEFT);

        if (!preg_match('/^\d{5}$/', $codigoPostal)) {
            return response()->json(['error' => 'Código postal inválido'], 400);
        }

        $asentamientos = Asentamiento::where('codigo_postal', $codigoPostal)
            ->with(['localidad.municipio.estado'])
            ->get();

        if ($asentamientos->isEmpty()) {
            return response()->json([
                'asentamientos' => [],
                'estado' => '',
                'municipio' => ''
            ], 200);
        }

        $response = [
            'asentamientos' => $asentamientos->map(function ($asentamiento) {
                return [
                    'id' => $asentamiento->id,
                    'nombre' => $asentamiento->nombre,
                ];
            })->toArray(),
            'estado' => $asentamientos->first()->localidad->municipio->estado->nombre ?? '',
            'municipio' => $asentamientos->first()->localidad->municipio->nombre ?? ''
        ];

        return response()->json($response);
    }
}