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
}