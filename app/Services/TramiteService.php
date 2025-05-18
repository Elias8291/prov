<?php

namespace App\Services;

use App\Models\Tramite;
use App\Models\Solicitante;
use Illuminate\Support\Facades\Auth;

class TramiteService
{
    /**
     * Obtiene o crea un trámite pendiente para el solicitante
     */
    public function obtenerTramitePendiente($solicitanteId = null)
    {
        if (is_null($solicitanteId) && Auth::check()) {
            $user = Auth::user();
            $solicitante = $user->solicitante;
            
            if (!$solicitante) {
                return null;
            }
            
            $solicitanteId = $solicitante->id;
        }
        
        if (!$solicitanteId) {
            return null;
        }
        
        return Tramite::firstOrCreate(
            [
                'solicitante_id' => $solicitanteId,
                'estado' => 'Pendiente'
            ],
            [
                'progreso_tramite' => 1,
                'fecha_registro' => now()
            ]
        );
    }
    
    /**
     * Avanza a la siguiente sección del trámite
     */
    public function avanzarSeccion(Tramite $tramite)
    {
        $solicitante = Solicitante::find($tramite->solicitante_id);
        $tipoPersona = $solicitante->tipo_persona ?? 'No definido';
        $totalSecciones = $this->obtenerTotalSecciones($tipoPersona);
        
        if ($tramite->progreso_tramite < $totalSecciones) {
            $tramite->increment('progreso_tramite');
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtiene el total de secciones según tipo de persona
     */
    public function obtenerTotalSecciones($tipoPersona)
    {
        return $tipoPersona == 'Física' ? 3 : ($tipoPersona == 'Moral' ? 6 : 5);
    }
    
    /**
     * Finaliza el trámite y lo marca como completado
     */
    public function finalizarTramite(Tramite $tramite)
    {
        $tramite->estado = 'Completado';
        $tramite->fecha_finalizacion = now();
        $tramite->save();
        
        return $tramite;
    }
}