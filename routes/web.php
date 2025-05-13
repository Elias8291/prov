<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SolicitanteController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\SectorActividadController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RevisionController;
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
Route::get('/register', function () {
    return view('auth.registrarse');
})->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/index', function () {
        return view('index.index');
    })->name('index');
    
    // Términos y condiciones de inscripción
    Route::get('/inscripcion/terminos_y_condiciones', [SolicitanteController::class, 'mostrarTerminosYCondiciones'])
        ->name('inscripcion.terminos_y_condiciones');
    Route::post('/inscripcion/aceptar-terminos', [SolicitanteController::class, 'aceptarTerminos'])
        ->name('inscripcion.aceptar_terminos');
Route::get('/inscripcion/terminos_y_condiciones', [SolicitanteController::class, 'mostrarTerminosYCondiciones'])
    ->name('inscripcion.terminos_y_condiciones');
    // Inscripción - formulario multisección
    Route::get('/inscripcion', [InscripcionController::class, 'mostrarFormulario'])->name('inscripcion.formulario');
    Route::post('/inscripcion', [InscripcionController::class, 'procesarSeccion'])->name('inscripcion.procesar');
    Route::get('/inscripcion/exito', [InscripcionController::class, 'exito'])->name('inscripcion.exito');
    Route::get('/inscripcion/actividades', [InscripcionController::class, 'obtenerActividades'])->name('inscripcion.actividades');
    Route::post('/inscripcion/guardar', [InscripcionController::class, 'guardarSeccion'])->name('inscripcion.guardar');
    Route::get('/revision', [RevisionController::class, 'index'])->name('revision.index');
    // Resto de rutas de tu sistema...
    Route::get('/sectores', [SectorActividadController::class, 'getSectores'])->name('sectores.index');
    Route::get('/sectores/{sectorId}/actividades', [SectorActividadController::class, 'getActividadesBySector'])->name('sectores.actividades');
    Route::get('/codigo-postal/{codigoPostal}', [DireccionController::class, 'getByCodigoPostal']);
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('/proveedores/search', [ProveedorController::class, 'search'])->name('proveedores.search');
});