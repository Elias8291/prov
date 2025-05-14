<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}