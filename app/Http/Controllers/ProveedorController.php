<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use Carbon\Carbon;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->input('days');
        $status = $request->input('status', ''); // Default to empty (all statuses)
        $proveedores = $this->getProveedoresWithDetails($days, $status);
        return view('proveedores.index', compact('proveedores', 'days', 'status'));
    }
    
    public function search(Request $request)
    {
        $term = $request->input('term');
        $days = $request->input('days');
        $status = $request->input('status', '');
        
        $query = DB::table('proveedor')
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
            ->distinct();
        
        // Apply search term filter
        if ($term) {
            $query->where(function($q) use ($term) {
                $q->where('detalle_tramite.razon_social', 'like', "%{$term}%")
                  ->orWhere('solicitante.rfc', 'like', "%{$term}%")
                  ->orWhere('proveedor.pv', 'like', "%{$term}%");
            });
        }
        
        // Apply status filter
        if ($status) {
            $query->where('proveedor.estado', '=', $status);
        }
        
        // Apply expiration date filter
        if ($days) {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->addDays((int)$days); // Cast to integer
            $query->whereBetween('proveedor.fecha_vencimiento', [$startDate, $endDate]);
        }
        
        $proveedores = $query->get();
        
        return response()->json($proveedores);
    }
    
    private function getProveedoresWithDetails($days = null, $status = null)
    {
        $query = DB::table('proveedor')
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
            ->distinct();
        
        // Apply status filter
        if ($status) {
            $query->where('proveedor.estado', '=', $status);
        }
        
        // Apply expiration date filter
        if ($days) {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->addDays((int)$days); // Cast to integer
            $query->whereBetween('proveedor.fecha_vencimiento', [$startDate, $endDate]);
        }
        
        return $query->paginate(10);
    }
}