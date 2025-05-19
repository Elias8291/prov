<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;

class ActividadController extends Controller
{
   public function obtenerActividades(Request $request)
{
    $sectorId = $request->input('sector_id');

    if (!$sectorId) {
        return response()->json(['error' => 'Sector no especificado'], 400);
    }

    $actividades = Actividad::where('sector_id', $sectorId)->get()->map(function ($actividad) {
        return [
            'id' => $actividad->id,
            'nombre' => $actividad->nombre,
        ];
    })->toArray();

    return response()->json(['actividades' => $actividades]);
}
}