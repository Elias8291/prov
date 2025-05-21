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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    // Método para almacenar de forma segura los datos extraídos del PDF
    public function secureData(Request $request)
    {
        try {
            Log::info('Recibida solicitud para asegurar datos');

            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'tipo_persona' => 'required|in:Física,Moral',
                'rfc' => 'required|string|max:255',
                'curp' => 'nullable|string|max:255',
                'cp' => 'required|string|max:5',
                'direccion' => 'required|string|max:255',
            ]);

            $token = Str::uuid()->toString();
            Session::put('secure_registration_' . $token, $validated);
            Log::info('Datos asegurados con token: ' . $token);

            return response()->json([
                'success' => true,
                'token' => $token,
                'message' => 'Datos asegurados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al asegurar datos de registro: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al asegurar datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        Log::info('Solicitud de registro recibida', [
            'email' => $request->email,
            'token_presente' => $request->has('secure_data_token'),
            'archivo_presente' => $request->hasFile('sat_file')
        ]);

        try {
            // Validar los campos necesarios
            $validated = $request->validate([
                'sat_file' => 'required|file|mimes:pdf|max:5120',
                'email' => 'required|email|max:255|unique:users,correo',
                'password' => 'required|string|min:8|confirmed',
                'secure_data_token' => 'required|string',
            ], [
                'sat_file.required' => 'La constancia del SAT es obligatoria.',
                'sat_file.mimes' => 'La constancia del SAT debe ser un archivo PDF.',
                'sat_file.max' => 'La constancia del SAT no debe superar los 5MB.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico no es válido.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                'secure_data_token.required' => 'Error de seguridad: token no proporcionado.',
            ]);

            // Recuperar datos seguros de la sesión
            $secureDataKey = 'secure_registration_' . $request->secure_data_token;
            Log::info('Buscando datos con clave: ' . $secureDataKey);

            if (!Session::has($secureDataKey)) {
                Log::error('Token no encontrado en sesión: ' . $request->secure_data_token);
                throw new \Exception('Error de seguridad: datos no encontrados o expirados. Por favor, intente nuevamente.');
            }

            $secureData = Session::get($secureDataKey);
            Log::info('Datos recuperados de sesión: ', $secureData);

            // Verificar la unicidad del RFC por separado
            $existingUser = User::where('rfc', $secureData['rfc'])->first();
            if ($existingUser) {
                throw new \Exception('El RFC ' . $secureData['rfc'] . ' ya está registrado.');
            }

            // Iniciar transacción de base de datos
            DB::beginTransaction();

            // Crear dirección
            $direccion = Direccion::create([
                'codigo_postal' => $secureData['cp'],
                'asentamiento_id' => null,
                'calle' => $secureData['direccion'],
            ]);

            // Crear usuario
            $user = User::create([
                'nombre' => $secureData['nombre'],
                'correo' => $request->email,
                'rfc' => $secureData['rfc'],
                'password' => Hash::make($request->password),
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar rol de solicitante
            $user->assignRole('solicitante');

            // Crear solicitante
            $solicitante = Solicitante::create([
                'usuario_id' => $user->id,
                'tipo_persona' => $secureData['tipo_persona'],
                'curp' => $secureData['curp'],
                'rfc' => $secureData['rfc'],
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
                'razon_social' => $secureData['nombre'],
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

            // Guardar el archivo
            $archivo = $request->file('sat_file');
            $nombreArchivo = uniqid('doc_' . $documento->id . '_') . '.pdf';
            $ruta = $archivo->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');

            // Encriptar la ruta del archivo
            $rutaEncriptada = Crypt::encryptString($ruta);

            // Crear documento_solicitante
            DocumentoSolicitante::create([
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'fecha_entrega' => now(),
                'estado' => 'Pendiente',
                'version_documento' => 1,
                'ruta_archivo' => $rutaEncriptada,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Eliminar datos seguros de la sesión
            Session::forget($secureDataKey);
            Log::info('Datos de sesión eliminados: ' . $secureDataKey);

            // Confirmar transacción
            DB::commit();
            Log::info('Registro completado exitosamente');

            // Redirige a welcome con éxito y muestra el modal
            return redirect()->route('welcome')
                ->with('message', 'Registro exitoso. Por favor inicia sesión desde la página principal.')
                ->with('show_success_modal', true);
        }  catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Error de validación: ' . json_encode($e->errors()));
        
        // Obtener el primer mensaje de error
        $errorMessage = '';
        foreach ($e->errors() as $field => $messages) {
            $errorMessage = $messages[0];
            break;
        }
        
        return redirect()->route('welcome')
            ->with('error', $errorMessage)
            ->with('show_error_modal', true)
            ->with('show_register_form', true)
            ->with('pdf_data_error', true)
            ->withInput();
            
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error durante el registro: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->route('welcome')
            ->with('error', $e->getMessage())
            ->with('show_error_modal', true)
            ->with('show_register_form', true)
            ->with('pdf_data_error', true)
            ->withInput();
    }
    }
}
