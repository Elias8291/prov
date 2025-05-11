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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role; // Importar el modelo Role de Spatie

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Register request received', $request->except('sat_file'));
        try {
            // Validate request data
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

            // Start database transaction
            DB::beginTransaction();

            // Upload PDF file
            $pdfPath = $request->file('sat_file')->store('documents', 'public');

            // Create direccion with only codigo_postal, calle, and asentamiento_id
            $direccion = Direccion::create([
                'codigo_postal' => $request->cp,
                'asentamiento_id' => null,
                'calle' => $request->direccion,
            ]);

            // Create user
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

            // Assign the 'solicitante' role to the user
            $user->assignRole('solicitante');

            // Create solicitante
            $solicitante = Solicitante::create([
                'usuario_id' => $user->id,
                'tipo_persona' => $request->tipo_persona,
                'curp' => $request->curp,
                'rfc' => $request->rfc,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create tramite
            $tramite = Tramite::create([
                'solicitante_id' => $solicitante->id,
                'tipo_tramite' => 'Inscripcion',
                'estado' => 'Pendiente',
                'progreso_tramite' => 10,
                'fecha_inicio' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create detalle_tramite
            $detalleTramite = DetalleTramite::create([
                'tramite_id' => $tramite->id,
                'razon_social' => $request->nombre,
                'email' => $request->email,
                'direccion_id' => $direccion->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create documento
            $documento = Documento::create([
                'nombre' => 'Constancia SAT',
                'tipo' => 'Certificado',
                'descripcion' => 'Constancia del SAT subida durante el registro',
                'fecha_expiracion' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create documento_solicitante
            DocumentoSolicitante::create([
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => 1,
                'ruta_archivo' => $pdfPath, // Almacenar la ruta del archivo aquí
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Commit transaction
            DB::commit();

            // Return JSON response with redirect instruction
            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso. Se te ha asignado una contraseña por defecto: secretaria1234. Por favor inicia sesión desde la página principal.',
                'redirect' => route('welcome')
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            Log::error('Error durante el registro: ' . $e->getMessage(), [
                'request' => $request->except('sat_file'),
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON error response
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error durante el registro: ' . $e->getMessage()
            ], 500);
        }
    }
}