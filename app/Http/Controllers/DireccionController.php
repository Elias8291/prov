<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asentamiento;
use Illuminate\Support\Facades\Log;

class DireccionController extends Controller
{
   public function obtenerDatosDireccion(Request $request)
{
    $codigoPostal = $request->input('codigo_postal');

    // Validate postal code
    if (!$codigoPostal || !preg_match('/^\d{4,5}$/', $codigoPostal)) {
        return response()->json([
            'success' => false,
            'message' => 'Código postal inválido. Debe contener 4 o 5 dígitos.'
        ], 400);
    }

    try {
        // Normalize postal code to 5 digits
        $codigoPostal = str_pad($codigoPostal, 5, '0', STR_PAD_LEFT);

        // Query asentamientos with related localidad, municipio, and estado
        $asentamientos = \App\Models\Asentamiento::where('codigo_postal', $codigoPostal)
            ->with(['localidad.municipio.estado'])
            ->get();

        if ($asentamientos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron datos para el código postal proporcionado.'
            ], 404);
        }

        // Extract estado and municipio from the first asentamiento
        $primerAsentamiento = $asentamientos->first();
        $estado = $primerAsentamiento->localidad->municipio->estado->nombre ?? '';
        $municipio = $primerAsentamiento->localidad->municipio->nombre ?? '';

        // Prepare asentamientos list for the dropdown
        $asentamientosList = $asentamientos->map(function ($asentamiento) {
            return [
                'id' => $asentamiento->id,
                'nombre' => $asentamiento->nombre
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'estado' => $estado,
            'municipio' => $municipio,
            'asentamientos' => $asentamientosList
        ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error obteniendo datos de dirección: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la solicitud. Por favor, intenta de nuevo.'
        ], 500);
    }
}
}