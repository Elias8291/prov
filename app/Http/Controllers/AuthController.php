<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    /**
     * Constructor para aplicar middleware guest
     */
    public function __construct()
    {
        // No usamos middleware aquí
    }
    
    /**
     * Muestra el formulario de inicio de sesión
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        // Si el usuario ya está autenticado, redirigir a la página principal
        if (Auth::check()) {
            return redirect(RouteServiceProvider::HOME);
        }
        
        return view('welcome');
    }

    /**
     * Procesa la solicitud de inicio de sesión
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'rfc' => 'required|string',
            'password' => 'required|string',
        ], [
            'rfc.required' => 'El RFC es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.'
        ]);

        // Buscar usuario por RFC
        $user = User::where('rfc', $request->rfc)->first();

        // Verificar si el usuario existe
        if (!$user) {
            return redirect('/')->withErrors([
                'rfc' => 'No existe un usuario registrado con este RFC.'
            ])->withInput($request->only('rfc'))->with('show_login', true);
        }

        // Verificar si la contraseña es correcta
        if (!Hash::check($request->password, $user->password)) {
            return redirect('/')->withErrors([
                'password' => 'La contraseña ingresada es incorrecta.'
            ])->withInput($request->only('rfc'))->with('show_login', true);
        }

        // Verificar si la cuenta está activa
        if ($user->estado !== 'activo') {
            return redirect('/')->withErrors([
                'rfc' => 'Tu cuenta está ' . $user->estado . '. Por favor, contacta al administrador.'
            ])->withInput($request->only('rfc'))->with('show_login', true);
        }

        // Actualizar fecha de último acceso
        $user->ultimo_acceso = Carbon::now();
        $user->save();

        // Iniciar sesión del usuario
        Auth::login($user);

        // Redirigir al dashboard o página prevista
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Cierra la sesión del usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('status', 'Has cerrado sesión correctamente.');
    }
}