<?php

namespace App\Services;

use App\Models\Tramite;
use App\Models\Solicitante;
use Illuminate\Support\Facades\Auth;

class TramiteService
{
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

    public function obtenerTotalSecciones($tipoPersona)
    {
        return $tipoPersona == 'Física' ? 3 : ($tipoPersona == 'Moral' ? 6 : 5);
    }
    public function finalizarTramite(Tramite $tramite)
    {
        $tramite->estado = 'Completado';
        $tramite->fecha_finalizacion = now();
        $tramite->save();

        return $tramite;
    }

    public function obtenerDatosSeccion(?Tramite $tramite, int $seccion, string $tipoPersona, bool $isRevisor): array
    {
        $datosPrevios = [];
        $actividadesSeleccionadas = [];

        if ($tramite && $seccion == 1) {
            $datosPrevios = [
                'rfc' => $tramite->solicitante->rfc ?? 'No disponible',
                'curp' => $tipoPersona == 'Física' ? ($tramite->solicitante->curp ?? 'No disponible') : null,
                'razon_social' => $tramite->solicitante->razon_social ?? '',
                'objeto_social' => $tramite->detalleTramite->objeto_social ?? '',
                'correo_electronico' => $tramite->solicitante->correo_electronico ?? '',
                'contacto_telefono' => $tramite->detalleTramite->contacto->telefono ?? '',
                'contacto_web' => $tramite->detalleTramite->contacto->pagina_web ?? '',
                'contacto_nombre' => $tramite->detalleTramite->contacto->nombre ?? '',
                'contacto_cargo' => $tramite->detalleTramite->contacto->cargo ?? '',
                'contacto_correo' => $tramite->detalleTramite->contacto->correo_electronico ?? '',
            ];

            $actividadesSeleccionadas = array_map(function ($actividad) {
                return [
                    'id' => $actividad['id'],
                    'nombre' => $actividad['nombre'],
                    'sector_id' => $actividad['sector_id'],
                ];
            }, $tramite->detalleTramite->actividades ?? []);
        }

        return [
            'datosPrevios' => $datosPrevios,
            'actividadesSeleccionadas' => $actividadesSeleccionadas,
        ];
    }
}
