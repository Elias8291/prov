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
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
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

            $secureDataKey = 'secure_registration_' . $request->secure_data_token;
            if (!Session::has($secureDataKey)) {
                Log::error('Token no encontrado en sesión: ' . $request->secure_data_token);
                $this->storeTempFile($request);
                return redirect('/')
                    ->withErrors(['register' => 'Error de seguridad: datos no encontrados o expirados.'])
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->with('show_register', true);
            }

            $secureData = Session::get($secureDataKey);

            $existingUser = User::where('rfc', $secureData['rfc'])->first();
            if ($existingUser) {
                $this->storeTempFile($request);
                return redirect('/')
                    ->withErrors(['register' => 'El RFC ' . $secureData['rfc'] . ' ya está registrado.'])
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->with('show_register', true);
            }

            DB::beginTransaction();

            $verificationToken = Str::random(64);

            $direccion = Direccion::create([
                'codigo_postal' => $secureData['cp'],
                'asentamiento_id' => null,
                'calle' => $secureData['direccion'],
            ]);

            $user = User::create([
                'nombre' => $secureData['nombre'],
                'correo' => $request->email,
                'rfc' => $secureData['rfc'],
                'password' => Hash::make($request->password),
                'estado' => 'pendiente',
                'verification_token' => $verificationToken,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $solicitante = Solicitante::create([
                'usuario_id' => $user->id,
                'tipo_persona' => $secureData['tipo_persona'],
                'curp' => $secureData['curp'],
                'rfc' => $secureData['rfc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tramite = Tramite::create([
                'solicitante_id' => $solicitante->id,
                'tipo_tramite' => 'Inscripcion',
                'estado' => 'Pendiente',
                'progreso_tramite' => 0,
                'fecha_inicio' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $detalleTramite = DetalleTramite::create([
                'tramite_id' => $tramite->id,
                'razon_social' => $secureData['nombre'],
                'email' => $request->email,
                'direccion_id' => $direccion->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $documento = Documento::where('nombre', 'Constancia de Situación Fiscal')->first();
            if (!$documento) {
                throw new \Exception('El documento "Constancia de Situación Fiscal" no está registrado en la base de datos.');
            }

            $archivo = $request->file('sat_file');
            $nombreArchivo = uniqid('doc_' . $documento->id . '_') . '.pdf';
            $ruta = $archivo->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');
            $rutaEncriptada = Crypt::encryptString($ruta);

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

            $verificationUrl = URL::temporarySignedRoute(
                'verify.email',
                now()->addMinutes(5),
                ['user_id' => $user->id, 'token' => $verificationToken]
            );

            Mail::to($user->correo)->send(new VerifyEmail($user, $verificationUrl));

            Session::forget($secureDataKey);
            Session::forget('temp_sat_file_name');
            Session::forget('temp_sat_file_path');
            Log::info('Datos de sesión eliminados: ' . $secureDataKey);

            DB::commit();
            Log::info('Registro completado exitosamente, correo de verificación enviado');

            return redirect('/')
                ->with('show_success_modal', true)
                ->with('message', 'Registro exitoso. Te hemos enviado un correo de verificación. Por favor revisa tu bandeja de entrada y sigue las instrucciones para activar tu cuenta.')
                ->with('show_login', true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación: ' . json_encode($e->errors()));
            $this->storeTempFile($request);
            return redirect('/')
                ->withErrors($e->errors())
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('show_register', true);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error durante el registro: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->storeTempFile($request);
            return redirect('/')
                ->withErrors(['register' => 'Error durante el registro: ' . $e->getMessage()])
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('show_register', true);
        }
    }

    public function verifyEmail(Request $request, $user_id)
    {
        Log::info('Iniciando verificación de correo', [
            'user_id' => $user_id,
            'token' => $request->query('token'),
            'has_signature' => $request->hasValidSignature(),
            'request_url' => $request->fullUrl()
        ]);

        if (!$request->hasValidSignature()) {
            Log::error('URL de verificación inválida o expirada', ['user_id' => $user_id]);
            return redirect('/')
                ->withErrors(['verify' => 'El enlace de verificación es inválido o ha expirado.'])
                ->with('show_register', true);
        }

        $user = User::find($user_id);
        if (!$user) {
            Log::error('Usuario no encontrado', ['user_id' => $user_id]);
            return redirect('/')
                ->withErrors(['verify' => 'Usuario no encontrado.'])
                ->with('show_register', true);
        }

        $token = $request->query('token');
        Log::info('Comparando tokens', [
            'provided_token' => $token,
            'stored_token' => $user->verification_token
        ]);

        if (!$token || $user->verification_token !== $token) {
            Log::error('Token de verificación no coincide o es nulo', [
                'user_id' => $user_id,
                'provided_token' => $token,
                'stored_token' => $user->verification_token
            ]);
            return redirect('/')
                ->withErrors(['verify' => 'El token de verificación no es válido.'])
                ->with('show_register', true);
        }

        if ($user->fecha_verificacion_correo) {
            Log::info('Correo ya verificado', ['user_id' => $user_id]);
            return redirect('/')
                ->with('message', 'Tu correo ya está verificado. Por favor inicia sesión.')
                ->with('show_login', true);
        }

        DB::beginTransaction();
        try {
            $updated = $user->update([
                'estado' => 'activo',
                'fecha_verificacion_correo' => now(),
                'verification_token' => null,
            ]);

            if (!$updated) {
                Log::error('No se actualizó ningún registro en la base de datos', ['user_id' => $user_id]);
                throw new \Exception('No se pudo actualizar el estado del usuario.');
            }

            Log::info('Estado del usuario actualizado a activo', [
                'user_id' => $user_id,
                'estado' => 'activo',
                'fecha_verificacion_correo' => $user->fecha_verificacion_correo
            ]);

            if (Role::where('name', 'solicitante')->exists()) {
                $user->assignRole('solicitante');
                Log::info('Rol "solicitante" asignado correctamente', ['user_id' => $user_id]);
            } else {
                Log::warning('Rol "solicitante" no encontrado, no se asignó ningún rol', ['user_id' => $user_id]);
            }

            DB::commit();
            Log::info('Correo verificado exitosamente', ['user_id' => $user_id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al verificar el correo', [
                'user_id' => $user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/')
                ->withErrors(['verify' => 'Error al verificar el correo: ' . $e->getMessage()])
                ->with('show_register', true);
        }

        return redirect('/')
            ->with('message', 'Correo verificado exitosamente. Por favor inicia sesión.')
            ->with('show_login', true);
    }

    protected function storeTempFile(Request $request)
    {
        if ($request->hasFile('sat_file') && $request->file('sat_file')->isValid()) {
            $file = $request->file('sat_file');
            $tempPath = $file->store('temp', 'public');
            Session::flash('temp_sat_file_path', $tempPath);
            Session::flash('temp_sat_file_name', $file->getClientOriginalName());
            Log::info('Archivo temporal almacenado: ' . $tempPath);
        }
    }
}