<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\DocumentoController;
use App\Providers\RouteServiceProvider;

// Ruta para verificar autenticación (usada por JavaScript)
Route::get('/check-auth', function () {
    return response()->json(['authenticated' => Auth::check()]);
})->name('check.auth');

// Ruta principal que redirecciona según el estado de autenticación
Route::get('/', function () {
    if (Auth::check()) {
        return redirect(RouteServiceProvider::HOME);
    }
    return view('welcome');
})->name('welcome');

// Rutas para invitados (no autenticados)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::get('/register', function () {
        return view('auth.registrarse');
    })->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
});

// Ruta de logout (accesible solo para usuarios autenticados)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas que requieren autenticación
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
    // Inscripción - formulario multisección
    Route::get('/inscripcion', [InscripcionController::class, 'mostrarFormulario'])->name('inscripcion.formulario');
    Route::post('/inscripcion', [InscripcionController::class, 'procesarSeccion'])->name('inscripcion.procesar');
    Route::get('/inscripcion/exito', [InscripcionController::class, 'exito'])->name('inscripcion.exito');
    Route::get('/inscripcion/actividades', [InscripcionController::class, 'obtenerActividades'])->name('inscripcion.actividades');
    Route::post('/inscripcion/guardar', [InscripcionController::class, 'guardarSeccion'])->name('inscripcion.guardar');

    // Resto de rutas de tu sistema...
    Route::get('/sectores', [SectorActividadController::class, 'getSectores'])->name('sectores.index');
    Route::get('/sectores/{sectorId}/actividades', [SectorActividadController::class, 'getActividadesBySector'])->name('sectores.actividades');

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

    Route::get('/revision', [RevisionController::class, 'index'])->name('revision.index');
    Route::get('/revision/{id}', [RevisionController::class, 'show'])->name('revision.show');
    Route::post('/revision/{id}/begin', [RevisionController::class, 'beginReview'])->name('revision.begin');
    Route::post('/revision/{id}/complete', [RevisionController::class, 'completeReview'])->name('revision.complete');
    Route::get('/my-revisions', [RevisionController::class, 'myRevisions'])->name('revision.my-revisions');
    Route::get('/revision/iniciar/{rfc}', [RevisionController::class, 'iniciarRevision'])->name('revision.iniciar');
    Route::get('/solicitante/address-info', [DireccionController::class, 'getSolicitanteAddressInfo']);
    Route::get('/direccion/by-codigo-postal/{codigo}', [DireccionController::class, 'getAddressByCodigoPostal']);
    Route::post('/inscripcion/obtener-datos-direccion', [InscripcionController::class, 'obtenerDatosDireccion'])->name('inscripcion.obtener-datos-direccion');

    Route::post('/inscripcion/procesar-seccion', [InscripcionController::class, 'procesarSeccion'])
        ->name('inscripcion.procesar_seccion');
    Route::post('/registro-datos-constitucion', [InscripcionController::class, 'guardarDatosConstitucion'])
        ->name('registro.datos.constitucion');
    Route::get('/estados', [EstadoController::class, 'getEstados'])->name('estados');

    // Rutas para el CRUD de documentos
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
    Route::get('/documentos/{documento}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit');
    Route::put('/documentos/{documento}', [DocumentoController::class, 'update'])->name('documentos.update');
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
    Route::post('/inscripcion/documento/upload', [InscripcionController::class, 'subirDocumento'])
    ->name('inscripcion.documento.upload');
});