<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\Actividad;
use Illuminate\Http\Request;

class SectorActividadController extends Controller
{
    // Obtener todos los sectores
    public function getSectores()
    {
        try {
            $sectores = Sector::select('id', 'nombre')->get();
            return response()->json([
                'success' => true,
                'data' => $sectores
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los sectores: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener actividades por sector
    public function getActividadesBySector($sectorId)
    {
        try {
            $actividades = Actividad::select('id', 'nombre')
                ->where('sector_id', $sectorId)
                ->get();
            return response()->json([
                'success' => true,
                'data' => $actividades
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las actividades: ' . $e->getMessage()
            ], 500);
        }
    }
}