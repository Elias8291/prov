<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SolicitanteController;
use App\Http\Controllers\SectorActividadController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ProveedorController;
// Ruta principal - Muestra la página de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Ruta de login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Ruta para olvidó contraseña
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Ruta de registro (Formulario)
Route::get('/register', function () {
    return view('auth.registrarse');
})->name('register');

// Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Grupo de rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Ruta protegida: Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Ruta protegida: Index
    Route::get('/index', function () {
        return view('index.index');
    })->name('index');

    // Ruta protegida: Términos y condiciones de inscripción
    Route::get('/inscripcion/terminos_y_condiciones', [SolicitanteController::class, 'mostrarTerminosYCondiciones'])
        ->name('inscripcion.terminos_y_condiciones');

    Route::post('/aceptar-terminos', [SolicitanteController::class, 'aceptarTerminos'])->name('aceptar.terminos');
    Route::get('/inscripcion/formularios', function () {
        return view('inscripcion.formularios');
    })->name('inscripcion.formularios');
    Route::post('/inscripcion/guardar-seccion/{numero}', [SolicitanteController::class, 'guardarSeccion'])
    ->name('inscripcion.guardar_seccion');
    Route::get('/sectores', [SectorActividadController::class, 'getSectores'])->name('sectores.index');
    Route::get('/sectores/{sectorId}/actividades', [SectorActividadController::class, 'getActividadesBySector'])->name('sectores.actividades');
    Route::get('/codigo-postal/{codigoPostal}', [DireccionController::class, 'getByCodigoPostal']);
    
    // User routes
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store'); // New route for storing users
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
    // User routes
Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
// Role routes
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::get('/proveedores/search', [ProveedorController::class, 'search'])->name('proveedores.search');
});

// Ruta para procesar el registro
Route::post('/register', [RegisterController::class, 'register'])->name('register');