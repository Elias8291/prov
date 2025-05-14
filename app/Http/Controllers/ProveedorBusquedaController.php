<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorBusquedaController extends Controller
{
    public function buscarProveedores(Request $request)
    {
        try {
            $proveedores = DB::table('proveedor')
                ->join('solicitante', 'proveedor.solicitante_id', '=', 'solicitante.id')
                ->join('tramite', 'tramite.solicitante_id', '=', 'solicitante.id')
                ->join('detalle_tramite', 'detalle_tramite.tramite_id', '=', 'tramite.id')
                ->select(
                    'proveedor.pv',
                    'proveedor.fecha_registro',
                    'proveedor.fecha_vencimiento',
                    'solicitante.rfc',
                    'solicitante.objeto_social',
                    'detalle_tramite.razon_social'
                )
                ->get();

            return response()->json($proveedores);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}