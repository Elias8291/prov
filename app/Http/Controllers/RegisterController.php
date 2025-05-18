<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Register request received', $request->except('sat_file'));

        try {
            // Validar los datos de la solicitud
            $validated = $request->validate([
                'sat_file' => 'required|file|mimes:pdf|max:5120',
                'email' => 'required|email|max:255|unique:users,correo',
                'nombre' => 'required|string|max:255',
                'tipo_persona' => 'required|in:Física,Moral',
                'rfc' => 'required|string|max:255|unique:users,rfc',
                'curp' => 'nullable|string|max:255',
                'cp' => 'required|string|max:5',
                'direccion' => 'required|string|max:255',
            ], [
                'sat_file.required' => 'La constancia del SAT es obligatoria.',
                'sat_file.mimes' => 'La constancia del SAT debe ser un archivo PDF.',
                'sat_file.max' => 'La constancia del SAT no debe superar los 5MB.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico no es válido.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'nombre.required' => 'El nombre es obligatorio.',
                'tipo_persona.required' => 'El tipo de persona es obligatorio.',
                'tipo_persona.in' => 'El tipo de persona debe ser "Física" o "Moral".',
                'rfc.required' => 'El RFC es obligatorio.',
                'rfc.unique' => 'El RFC ya está registrado.',
                'cp.required' => 'El código postal es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
            ]);

            // Iniciar transacción
            DB::beginTransaction();

            // Subir el archivo PDF
            $pdfPath = $request->file('sat_file')->store('documents', 'public');

            // Crear dirección
            $direccion = Direccion::create([
                'codigo_postal' => $request->cp,
                'asentamiento_id' => null,
                'calle' => $request->direccion,
            ]);

            // Crear usuario
            $defaultPassword = 'secretaria1234';
            $user = User::create([
                'nombre' => $request->nombre,
                'correo' => $request->email,
                'rfc' => $request->rfc,
                'password' => Hash::make($defaultPassword),
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar rol de solicitante
            $user->assignRole('solicitante');

            // Crear solicitante
            $solicitante = Solicitante::create([
                'usuario_id' => $user->id,
                'tipo_persona' => $request->tipo_persona,
                'curp' => $request->curp,
                'rfc' => $request->rfc,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Crear trámite
            $tramite = Tramite::create([
                'solicitante_id' => $solicitante->id,
                'tipo_tramite' => 'Inscripcion',
                'estado' => 'Pendiente',
                'progreso_tramite' => 0,
                'fecha_inicio' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Crear detalle del trámite
            $detalleTramite = DetalleTramite::create([
                'tramite_id' => $tramite->id,
                'razon_social' => $request->nombre,
                'email' => $request->email,
                'direccion_id' => $direccion->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buscar el documento "Constancia de Situación Fiscal"
            $documento = Documento::where('nombre', 'Constancia de Situación Fiscal')->first();

            // Verificar si el documento existe
            if (!$documento) {
                throw new \Exception('El documento "Constancia de Situación Fiscal" no está registrado en la base de datos.');
            }

            // Crear documento_solicitante con el id del documento encontrado
            DocumentoSolicitante::create([
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => 1,
                'ruta_archivo' => $pdfPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Confirmar transacción
            DB::commit();

            // Respuesta JSON con instrucción de redirección
            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso. Se te ha asignado una contraseña por defecto: secretaria1234. Por favor inicia sesión desde la página principal.',
                'redirect' => route('welcome')
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Revertir transacción
            DB::rollBack();
            Log::error('Error durante el registro: ' . $e->getMessage(), [
                'request' => $request->except('sat_file'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error durante el registro: ' . $e->getMessage()
            ], 500);
        }
    }
}