<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt; // Add Crypt facade
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\Tramite;

class DocumentosController extends Controller
{
    public function subir(Request $request)
    {
        $user = Auth::user();
        $solicitante = $user->solicitante;
        $tramite = Tramite::where('solicitante_id', $solicitante->id)
            ->where('estado', 'Pendiente')
            ->first();

        if (!$tramite) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontró un trámite pendiente.'
            ], 400);
        }

        // Validar input
        $request->validate([
            'documento_id' => 'required|exists:documento,id',
            'archivo' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        $documento = Documento::find($request->input('documento_id'));
        $archivo = $request->file('archivo');
        $nombreArchivo = uniqid('doc_'.$documento->id.'_').'.pdf';
        $ruta = $archivo->storeAs('documentos_solicitante/'.$tramite->id, $nombreArchivo, 'public');

        // Encrypt the file path
        $rutaEncriptada = Crypt::encryptString($ruta);

        // Crear o actualizar registro en documento_solicitante
        $docSolicitante = DocumentoSolicitante::updateOrCreate(
            [
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id
            ],
            [
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => DocumentoSolicitante::where('tramite_id', $tramite->id)
                    ->where('documento_id', $documento->id)
                    ->max('version_documento') + 1,
                'ruta_archivo' => $rutaEncriptada // Store encrypted path
            ]
        );

        return response()->json([
            'success' => true,
            'ruta' => Storage::disk('public')->url($ruta), // Return unencrypted URL for client use
            'docSolicitanteId' => $docSolicitante->id,
            'mensaje' => 'Documento subido correctamente',
        ]);
    }


    public function get(Request $request, $tramiteId)
{
    try {
        // Validate the tramite_id
        $request->validate([
            'tramiteId' => 'required|exists:tramite,id',
        ]);

        // Fetch documents associated with the tramite
        $documentos = DocumentoSolicitante::where('tramite_id', $tramiteId)
            ->with('documento')
            ->get()
            ->map(function ($docSolicitante) {
                return [
                    'id' => $docSolicitante->id,
                    'documento_id' => $docSolicitante->documento_id,
                    'nombre' => $docSolicitante->documento->nombre,
                    'tipo' => $docSolicitante->documento->tipo,
                    'fecha_entrega' => $docSolicitante->fecha_entrega ? $docSolicitante->fecha_entrega->toIso8601String() : null,
                    'estado' => $docSolicitante->estado,
                    'version_documento' => $docSolicitante->version_documento,
                    'ruta_archivo' => Storage::disk('public')->url(Crypt::decryptString($docSolicitante->ruta_archivo)),
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'documentos' => $documentos,
            'mensaje' => 'Documentos obtenidos correctamente.',
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'mensaje' => 'Error de validación.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        Log::error('Error decrypting document path: ' . $e->getMessage(), ['tramite_id' => $tramiteId]);
        return response()->json([
            'success' => false,
            'mensaje' => 'Error al desencriptar la ruta de un documento.',
        ], 500);
    } catch (\Exception $e) {
        Log::error('Error fetching documents: ' . $e->getMessage(), ['tramite_id' => $tramiteId]);
        return response()->json([
            'success' => false,
            'mensaje' => 'Error al obtener los documentos.',
        ], 500);
    }
}
}