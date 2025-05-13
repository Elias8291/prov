<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DetalleTramite;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = $this->getProveedoresWithDetails();
        return view('proveedores.index', compact('proveedores'));
    }
    
   public function search(Request $request)
{
    $term = $request->input('term');
    
    $proveedores = DB::table('proveedor')
        ->join('solicitante', 'proveedor.solicitante_id', '=', 'solicitante.id')
        ->leftJoin('tramite', 'solicitante.id', '=', 'tramite.solicitante_id')
        ->leftJoin('detalle_tramite', 'tramite.id', '=', 'detalle_tramite.tramite_id')
        ->where(function($query) use ($term) {
            $query->where('detalle_tramite.razon_social', 'like', "%{$term}%")
                ->orWhere('solicitante.rfc', 'like', "%{$term}%")
                ->orWhere('proveedor.pv', 'like', "%{$term}%");
        })
        ->select(
            'proveedor.pv',
            'detalle_tramite.razon_social',
            'solicitante.rfc',
            'proveedor.estado',
            DB::raw('DATE_FORMAT(proveedor.fecha_registro, "%Y-%m-%d") as fecha_registro'),
            DB::raw('DATE_FORMAT(proveedor.fecha_vencimiento, "%Y-%m-%d") as fecha_vencimiento')
        )
        ->distinct()
        ->get();
        
    return response()->json($proveedores);
}
   private function getProveedoresWithDetails()
{
    return DB::table('proveedor')
        ->join('solicitante', 'proveedor.solicitante_id', '=', 'solicitante.id')
        ->leftJoin('tramite', 'solicitante.id', '=', 'tramite.solicitante_id')
        ->leftJoin('detalle_tramite', 'tramite.id', '=', 'detalle_tramite.tramite_id')
        ->select(
            'proveedor.pv',
            'detalle_tramite.razon_social',
            'solicitante.rfc',
            'proveedor.estado',
            DB::raw('DATE_FORMAT(proveedor.fecha_registro, "%Y-%m-%d") as fecha_registro'),
            DB::raw('DATE_FORMAT(proveedor.fecha_vencimiento, "%Y-%m-%d") as fecha_vencimiento')
        )
        ->distinct()
        ->paginate(10);
}
}