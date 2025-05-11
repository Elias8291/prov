<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        // Carga los usuarios con sus roles (optimizado para spatie/laravel-permission)
        $users = User::with('roles')->paginate(10);
        
        return view('usuarios.index', [
            'users' => $users
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'string', 'email', 'max:255', 'unique:users,correo'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'rfc' => ['required', 'string', 'min:12', 'max:13', 'unique:users,rfc'],
            'estado' => ['required', 'string', 'in:activo,inactivo,suspendido'],
            'rol' => ['required', 'string'],
        ]);

        // Create user
        $user = User::create([
            'nombre' => $validated['nombre'],
            'correo' => $validated['correo'],
            'password' => $validated['password'], // Auto-hashed by Laravel
            'rfc' => $validated['rfc'],
            'estado' => $validated['estado'],
        ]);

        // Assign role
        $user->assignRole($validated['rol']);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'nombre' => ['required', 'string', 'max:255'],
        'correo' => ['required', 'string', 'email', 'max:255', 'unique:users,correo,'.$user->id],
        'password' => ['nullable', 'confirmed', Password::defaults()],
        'estado' => ['required', 'string', 'in:activo,inactivo,suspendido'],
        'rol' => ['required', 'string'],
    ]);

    // Update user data
    $userData = [
        'nombre' => $validated['nombre'],
        'correo' => $validated['correo'],
        'estado' => $validated['estado'],
    ];

    // Only update password if provided
    if (!empty($validated['password'])) {
        $userData['password'] = $validated['password']; // Auto-hashed by Laravel
    }

    $user->update($userData);

    // Update role (remove existing roles first)
    $user->syncRoles([$validated['rol']]);

    return redirect()->route('usuarios.index')
        ->with('success', 'Usuario actualizado correctamente');
}

public function destroy(User $user)
{
    // Change user status to inactivo before soft deleting
    $user->update(['estado' => 'inactivo']);
    
    // Soft delete the user
    $user->delete();

    return redirect()->route('usuarios.index')
        ->with('success', 'Usuario eliminado correctamente');
}
}