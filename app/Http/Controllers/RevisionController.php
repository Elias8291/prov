<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Solicitante;
use App\Models\User;
use App\Models\DetalleTramite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
class RevisionController extends Controller
{
    /**
     * Display a listing of solicitudes pending revision.
     *
     * @return \Illuminate\View\View
     */
   public function index(Request $request)
{
    // Get solicitudes that haven't been reviewed yet
    // - progreso_tramite between 0-7
    // - fecha_finalizacion is null
    // - revisado_por is null
    // - estado is one of the allowed states
   $query = Tramite::with(['solicitante', 'solicitante.usuario', 'detalleTramite'])
        ->whereBetween('progreso_tramite', [0, 7])
        ->whereNull('fecha_finalizacion')
        ->whereNull('revisado_por')
        ->whereNull('fecha_revision')
        ->whereIn('estado', ['Pendiente', 'Rechazado', 'Por Cotejar']);

    // Apply progress filter if provided
    if ($request->has('progreso') && !empty($request->progreso)) {
        $query->where('progreso_tramite', $request->progreso);
    }

    // Apply estado filter if provided
    if ($request->has('estado_tramite') && !empty($request->estado_tramite)) {
        $query->where('estado', $request->estado_tramite);
    }

    $solicitudesPendientes = $query->orderBy('created_at', 'desc')
        ->paginate(15)
        ->appends($request->query());

    return view('revision.index', [
        'solicitudes' => $solicitudesPendientes,
    ]);
}
    
    /**
     * Display the specified solicitud for revision.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $tramite = Tramite::with([
            'solicitante', 
            'solicitante.usuario', 
            'detalleTramite',
            'detalleTramite.direccion',
            'documentosSolicitante',
            'documentosSolicitante.documento'
        ])->findOrFail($id);
        
        // Check if the tramite is eligible for review (0-7 progress, not reviewed yet)
        if ($tramite->progreso_tramite < 0 || $tramite->progreso_tramite > 7 || !is_null($tramite->revisado_por)) {
            return redirect()->route('revision.index')
                ->with('error', 'La solicitud no está disponible para revisión o ya ha sido revisada.');
        }
        
        return view('revision.show', compact('tramite'));
    }
    
    /**
     * Begin the revision process by marking a tramite as under review
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function beginReview($id)
    {
        $tramite = Tramite::findOrFail($id);
        
        // Check if the tramite is eligible for review
        if ($tramite->progreso_tramite < 0 || $tramite->progreso_tramite > 7 || !is_null($tramite->revisado_por)) {
            return redirect()->route('revision.index')
                ->with('error', 'La solicitud no está disponible para revisión o ya ha sido revisada.');
        }
        
        try {
            DB::beginTransaction();
            
            // Mark the tramite as under review
            $tramite->update([
                'fecha_inicio' => now(),
                'revisado_por' => Auth::id(),
                'estado' => 'En Revision'
            ]);
            
            DB::commit();
            
            return redirect()->route('revision.show', $id)
                ->with('success', 'Has comenzado la revisión de esta solicitud.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al iniciar la revisión: ' . $e->getMessage());
        }
    }
    

   /**
 * Display the supplier history view with RFC-based search
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  string  $rfc
 * @return \Illuminate\View\View
 */
public function iniciarRevision(Request $request, $rfc)
    {
        // Buscar solicitantes con el RFC proporcionado
        $solicitantes = Solicitante::with(['usuario', 'tramites.detalleTramite'])
            ->where('rfc', $rfc)
            ->get();

        if ($solicitantes->isEmpty()) {
            return view('revision.iniciar_revision', [
                'supplier' => null,
                'pvs' => [],
                'message' => 'No se encontraron solicitantes con el RFC proporcionado.'
            ]);
        }

        $solicitante = $solicitantes->first();

        // Buscar proveedores asociados al solicitante_id
        $proveedores = Proveedor::with(['solicitante'])
            ->where('solicitante_id', $solicitante->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($proveedores->isEmpty()) {
            return view('revision.iniciar_revision', [
                'supplier' => null,
                'pvs' => [],
                'message' => 'No se encontraron proveedores asociados al solicitante con este RFC.'
            ]);
        }

        // Obtener la razón social del primer tramite->detalle_tramite
        $razonSocial = null;
        if ($solicitante->tramites->isNotEmpty() && $solicitante->tramites->first()->detalleTramite) {
            $razonSocial = $solicitante->tramites->first()->detalleTramite->razon_social ?? 'N/A';
        } else {
            $razonSocial = $solicitante->razon_social ?? ($solicitante->usuario->nombre ?? 'N/A');
        }

        $supplier = [
            'name' => $razonSocial,
            'rfc' => $solicitante->rfc,
            'contact_name' => $solicitante->tramites->isNotEmpty() && $solicitante->tramites->first()->detalleTramite
                ? ($solicitante->tramites->first()->detalleTramite->representante_legal ?? 'N/A')
                : ($solicitante->representante_legal ?? 'N/A'),
            'phone' => $solicitante->tramites->isNotEmpty() && $solicitante->tramites->first()->detalleTramite
                ? ($solicitante->tramites->first()->detalleTramite->telefono_contacto ?? 'N/A')
                : ($solicitante->telefono_contacto ?? 'N/A'),
        ];

        $pvs = $proveedores->map(function ($proveedor) use ($solicitante) {
            return [
                'pv_id' => 'PV-' . $proveedor->pv,
                'status' => strtolower(str_replace(' ', '_', $proveedor->estado)),
                'start_date' => $proveedor->fecha_registro ? \Carbon\Carbon::parse($proveedor->fecha_registro)->format('d/m/Y') : 'N/A',
                'end_date' => $proveedor->fecha_vencimiento ? \Carbon\Carbon::parse($proveedor->fecha_vencimiento)->format('d/m/Y') : 'N/A',
                'registration_date' => \Carbon\Carbon::parse($proveedor->created_at)->format('d/m/Y'),
                'responsible_person' => $solicitante->tramites->isNotEmpty() && $solicitante->tramites->first()->detalleTramite
                    ? ($solicitante->tramites->first()->detalleTramite->representante_legal ?? 'N/A')
                    : ($solicitante->representante_legal ?? 'N/A'),
                'documents_completed' => 0, // Ajustar si hay relación con documentos
                'documents_total' => 0, // Ajustar si hay relación con documentos
                'observations' => $proveedor->observaciones ?? null,
            ];
        })->toArray();

        return view('revision.iniciar_revision', [
            'supplier' => $supplier,
            'pvs' => $pvs,
        ]);
    }
    /**
     * Complete the revision process
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeReview(Request $request, $id)
    {
        $request->validate([
            'resultado' => 'required|in:Aprobado,Rechazado,Pendiente',
            'observaciones' => 'nullable|string|max:500',
        ]);
        
        $tramite = Tramite::findOrFail($id);
        
        // Check if this user is assigned to review this tramite
        if ($tramite->revisado_por != Auth::id()) {
            return redirect()->route('revision.index')
                ->with('error', 'No tienes permiso para completar esta revisión.');
        }
        
        try {
            DB::beginTransaction();
            
            // Update the tramite status based on the review result
            $tramite->update([
                'fecha_finalizacion' => now(),
                'fecha_revision' => now(),
                'estado' => $request->resultado,
                'observaciones' => $request->observaciones,
            ]);
            
            DB::commit();
            
            return redirect()->route('revision.index')
                ->with('success', 'Revisión completada exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al completar la revisión: ' . $e->getMessage());
        }
    }
    
    /**
     * List revisions assigned to the current user
     *
     * @return \Illuminate\View\View
     */
    public function myRevisions()
    {
        $misRevisiones = Tramite::with(['solicitante', 'solicitante.usuario', 'detalleTramite'])
            ->where('revisado_por', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
        
        return view('revision.mis-revisiones', [
            'revisiones' => $misRevisiones,
        ]);
    }
}