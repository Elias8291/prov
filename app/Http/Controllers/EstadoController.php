<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Estado;

class EstadoController extends Controller
{
    public function getEstados()
    {
        try {
            // Obtener los estados desde la base de datos
            $estados = DB::table('estados')->select('id', 'nombre')->get();
            
            // Verificar si se obtuvieron resultados
            if ($estados->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron estados'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $estados
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estados: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener estados para usar en vistas
     * 
     * @return array
     */
    public function getEstadosParaVista()
    {
        try {
            return Estado::orderBy('nombre', 'asc')
                ->get(['id', 'nombre'])
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error al cargar estados para vista: ' . $e->getMessage());
            return [];
        }
    }
}