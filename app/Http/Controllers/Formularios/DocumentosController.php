<?php

namespace App\Http\Controllers\Formularios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\Tramite;
use Illuminate\Support\Facades\DB;

class DocumentosController extends Controller
{
    /**
     * Sube un documento para un trámite pendiente
     *
     * @param Request $request La solicitud con los datos del documento
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación
     */
    public function subir(Request $request)
    {
        $this->validateRequest($request, 'subir');

        $solicitante = Auth::user()->solicitante;
        $tramite = $this->getTramitePendiente($solicitante->id);

        if (!$tramite) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se encontró un trámite pendiente.'
            ], 400);
        }

        return DB::transaction(function () use ($request, $tramite) {
            $documento = Documento::find($request->input('documento_id'));
            $ruta = $this->storeArchivo($request->file('archivo'), $tramite->id, $documento->id);
            $docSolicitante = $this->guardarDocumentoSolicitante($tramite->id, $documento->id, $ruta);

            return response()->json([
                'success' => true,
                'ruta' => asset('storage/' . $ruta),
                'docSolicitanteId' => $docSolicitante->id,
                'mensaje' => 'Documento subido correctamente',
            ]);
        });
    }

    /**
     * Obtiene los documentos asociados a un trámite
     *
     * @param Request $request La solicitud HTTP
     * @param int $tramiteId El ID del trámite
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los documentos
     */
    public function get(Request $request, $tramiteId)
    {
        $this->validateRequest($request, 'get', $tramiteId);

        $documentos = DocumentoSolicitante::where('tramite_id', $tramiteId)
            ->with('documento')
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'documento_id' => $doc->documento_id,
                'nombre' => $doc->documento->nombre,
                'tipo' => $doc->documento->tipo,
                'fecha_entrega' => $doc->fecha_entrega
                    ? \Carbon\Carbon::parse($doc->fecha_entrega)->toIso8601String()
                    : null,
                'estado' => $doc->estado,
                'version_documento' => $doc->version_documento,
                'ruta_archivo' => asset('storage/' . Crypt::decryptString($doc->ruta_archivo)),
            ])
            ->toArray();

        return response()->json([
            'success' => true,
            'documentos' => $documentos,
            'mensaje' => 'Documentos obtenidos correctamente.',
        ]);
    }

    /**
     * Valida los datos de la solicitud
     *
     * @param Request $request La solicitud a validar
     * @param string $method El método que se está ejecutando (subir o get)
     * @param int|null $tramiteId El ID del trámite, si aplica
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request, string $method, $tramiteId = null)
    {
        $rules = $method === 'subir'
            ? [
                'documento_id' => 'required|exists:documento,id',
                'archivo' => 'required|file|mimes:pdf|max:10240',
            ]
            : [
                'tramiteId' => 'required|exists:tramite,id',
            ];

        if ($method === 'update') {
            $rules['tramiteId'] = 'required|exists:tramite,id';
            $rules['documento_id'] = 'required|exists:documento,id';
        }

        $request->merge(['tramiteId' => $tramiteId]);
        $request->validate($rules);
    }

    /**
     * Obtiene el trámite pendiente para un solicitante
     *
     * @param int $solicitanteId El ID del solicitante
     * @return Tramite|null El trámite pendiente o null si no se encuentra
     */
    private function getTramitePendiente($solicitanteId): ?Tramite
    {
        return Tramite::where('solicitante_id', $solicitanteId)
            ->where('estado', 'Pendiente')
            ->first();
    }

    /**
     * Almacena el archivo en el sistema de almacenamiento
     *
     * @param \Illuminate\Http\UploadedFile $archivo El archivo subido
     * @param int $tramiteId El ID del trámite
     * @param int $documentoId El ID del documento
     * @return string La ruta donde se almacenó el archivo
     */
    private function storeArchivo($archivo, $tramiteId, $documentoId): string
    {
        $nombreArchivo = uniqid('doc_' . $documentoId . '_') . '.pdf';
        return $archivo->storeAs('documentos_solicitante/' . $tramiteId, $nombreArchivo, 'public');
    }

    /**
     * Guarda o actualiza el registro del documento del solicitante
     *
     * @param int $tramiteId El ID del trámite
     * @param int $documentoId El ID del documento
     * @param string $ruta La ruta del archivo almacenado
     * @return DocumentoSolicitante El registro del documento del solicitante
     */
    private function guardarDocumentoSolicitante($tramiteId, $documentoId, $ruta): DocumentoSolicitante
    {
        return DocumentoSolicitante::updateOrCreate(
            [
                'tramite_id' => $tramiteId,
                'documento_id' => $documentoId,
            ],
            [
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => DocumentoSolicitante::where('tramite_id', $tramiteId)
                    ->where('documento_id', $documentoId)
                    ->max('version_documento') + 1,
                'ruta_archivo' => Crypt::encryptString($ruta),
            ]
        );
    }

 public function updateDocumentStatus(Request $request, $tramiteId, $documentoId)
    {
        $request->validate([
            'approval' => 'required|in:approved,not-approved',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            Log::info('Updating document status:', [
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'approval' => $request->input('approval'),
                'comment' => $request->input('comment')
            ]);

            $docSolicitante = DocumentoSolicitante::where('tramite_id', $tramiteId)
                ->where('documento_id', $documentoId)
                ->firstOrFail();
            $docSolicitante->estado = $request->input('approval') === 'approved' ? 'Aprobado' : 'Rechazado';
            $docSolicitante->observaciones = $request->input('comment');
            $docSolicitante->save();

            return response()->json([
                'success' => true,
                'mensaje' => 'Documento actualizado correctamente.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Document not found:', ['tramiteId' => $tramiteId, 'documentoId' => $documentoId]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Documento no encontrado.',
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error updating document status:', [
                'message' => $e->getMessage(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'approval' => $request->input('approval')
            ]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al actualizar el documento: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error updating document status:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tramiteId' => $tramiteId,
                'documentoId' => $documentoId,
                'approval' => $request->input('approval')
            ]);
            return response()->json([
                'success' => false,
                'mensaje' => 'Error inesperado al actualizar el documento: ' . $e->getMessage(),
            ], 500);
        }
    }
}
