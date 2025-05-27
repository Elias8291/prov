<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use App\Models\Asentamiento;
use Illuminate\Support\Facades\DB;

class DomicilioController extends Controller
{
    // Obtiene los datos formateados de la dirección para un trámite
    public function getAddressData(Tramite $tramite)
    {
        $addressData = [
            'codigo_postal' => 'No disponible',
            'estado' => 'No disponible',
            'municipio' => 'No disponible',
            'colonia' => 'No disponible',
            'calle' => 'No disponible',
            'numero_exterior' => 'No disponible',
            'numero_interior' => 'No disponible',
            'entre_calle_1' => 'No disponible',
            'entre_calle_2' => 'No disponible',
        ];

        $detalle = DetalleTramite::where('tramite_id', $tramite->id)->first();

        if ($detalle && $detalle->direccion) {
            $this->fillAddressData($detalle->direccion, $addressData);
        }

        return $addressData;
    }

    // Guarda los datos de dirección para un trámite
    public function guardar(Request $request, Tramite $tramite)
    {
        $this->validateRequest($request);

        return DB::transaction(function () use ($request, $tramite) {
            $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
            $direccion = $this->guardarDireccion($request, $detalle->direccion_id);
            $detalle->direccion_id = $direccion->id;
            $detalle->save();

            return true;
        });
    }

    // Obtiene datos de dirección por código postal
    public function obtenerDatosPorCodigoPostal(Request $request)
    {
        $this->validateRequest($request, 'obtenerDatosPorCodigoPostal');

        $response = Http::post(route('inscripcion.obtener-datos-direccion'), [
            'codigo_postal' => $request->input('codigo_postal'),
        ]);

        return $response->json();
    }

    private function validateRequest(Request $request, string $method = 'guardar')
    {
        $rules = $method === 'guardar'
            ? [
                'codigo_postal' => 'required|string|size:5',
                'colonia' => 'required|string|max:255',
                'calle' => 'required|string|max:255',
                'numero_exterior' => 'required|string|max:50',
                'numero_interior' => 'nullable|string|max:50',
                'entre_calle_1' => 'nullable|string|max:255',
                'entre_calle_2' => 'nullable|string|max:255',
            ]
            : [
                'codigo_postal' => 'required|string|size:5',
            ];

        $request->validate($rules);
    }

    private function fillAddressData(Direccion $direccion, array &$addressData)
    {
        $addressData['codigo_postal'] = $direccion->codigo_postal ?? 'No disponible';
        $addressData['calle'] = $direccion->calle ?? 'No disponible';
        $addressData['numero_exterior'] = $direccion->numero_exterior ?? 'No disponible';
        $addressData['numero_interior'] = $direccion->numero_interior ?? 'No disponible';
        $addressData['entre_calle_1'] = $direccion->entre_calle_1 ?? 'No disponible';
        $addressData['entre_calle_2'] = $direccion->entre_calle_2 ?? 'No disponible';

        if ($direccion->asentamiento) {
            $addressData['colonia'] = $direccion->asentamiento->nombre ?? 'No disponible';
            if ($direccion->asentamiento->localidad && $direccion->asentamiento->localidad->municipio) {
                $addressData['municipio'] = $direccion->asentamiento->localidad->municipio->nombre ?? 'No disponible';
                $addressData['estado'] = $direccion->asentamiento->localidad->municipio->estado->nombre ?? 'No disponible';
            }
        }
    }

    private function guardarDireccion(Request $request, ?int $direccionId): Direccion
    {
        $direccion = $direccionId ? Direccion::find($direccionId) : new Direccion();
        $codigoPostal = str_pad($request->input('codigo_postal'), 5, '0', STR_PAD_LEFT);
        $asentamiento = $this->getAsentamiento($codigoPostal, $request->input('colonia'));

        $direccion->codigo_postal = $codigoPostal;
        $direccion->asentamiento_id = $asentamiento?->id;
        $direccion->calle = $request->input('calle');
        $direccion->numero_exterior = $request->input('numero_exterior');
        $direccion->numero_interior = $request->input('numero_interior');
        $direccion->entre_calle_1 = $request->input('entre_calle_1');
        $direccion->entre_calle_2 = $request->input('entre_calle_2');
        $direccion->save();

        return $direccion;
    }

    private function getAsentamiento(string $codigoPostal, string $colonia): ?Asentamiento
    {
        return Asentamiento::where('codigo_postal', $codigoPostal)
            ->where('nombre', $colonia)
            ->first();
    }
}