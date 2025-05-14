<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    /**
     * Transformar los datos del proveedor en un arreglo.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pv' => $this->pv,
            'estado' => $this->estado,
            'solicitante' => [
                'id' => $this->solicitante_id ?? null, // Use solicitante_id if available
                'tipo_persona' => null, // Set to null if not available in query
                'rfc' => $this->rfc ?? null, // Use rfc from query
            ],
            'fecha_registro' => $this->fecha_registro,
            'fecha_vencimiento' => $this->fecha_vencimiento,
        ];
    }
}