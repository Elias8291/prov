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
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\Formularios\DatosGeneralesController;
use App\Http\Controllers\Formularios\DomicilioController;
use App\Http\Controllers\Formularios\ConstitucionController;
use App\Http\Controllers\Formularios\AccionistasController;
use App\Http\Controllers\Formularios\ApoderadoLegalController;
use App\Http\Controllers\Formularios\DocumentosController;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DiasInhabilesController;

// Public Routes
Route::get('/', function () {
    return Auth::check() ? redirect(RouteServiceProvider::HOME) : view('welcome');
})->name('welcome');

Route::get('/check-auth', function () {
    return response()->json(['authenticated' => Auth::check()]);
})->name('check.auth');

// Authentication Routes (Guest Only)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
});

// Registration and Verification Routes (Public)
Route::prefix('register')->group(function () {
    Route::get('/', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/', [RegisterController::class, 'register'])->name('register');
    Route::post('/secure-registration-data', [RegisterController::class, 'secureData'])->name('secure.registration.data');
    Route::get('/verify-email/{user_id}', [RegisterController::class, 'verifyEmail'])->name('verify.email');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard and Index
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/index', function () {
        return view('index.index');
    })->name('index');

    // Inscripcion Routes
    Route::prefix('inscripcion')->group(function () {
        Route::get('/', [InscripcionController::class, 'mostrarFormulario'])->name('inscripcion.formulario');
        Route::post('/', [InscripcionController::class, 'procesarSeccion'])->name('inscripcion.procesar');
        Route::get('/index', [InscripcionController::class, 'index'])->name('inscripcion.index');
        Route::get('/exito', [InscripcionController::class, 'exito'])->name('inscripcion.exito');
        Route::post('/procesar-seccion', [InscripcionController::class, 'procesarSeccion'])->name('inscripcion.procesar_seccion');
        Route::post('/documento/upload', [DocumentosController::class, 'subir'])->name('inscripcion.documento.upload');
        Route::get('/actividades', [ActividadController::class, 'obtenerActividades'])->name('inscripcion.actividades');
        Route::get('/terminos_y_condiciones', [SolicitanteController::class, 'mostrarTerminosYCondiciones'])->name('inscripcion.terminos_y_condiciones');
        Route::post('/aceptar-terminos', [SolicitanteController::class, 'aceptarTerminos'])->name('inscripcion.aceptar_terminos');

        // Formulario Sections
        Route::post('/datos-generales', [DatosGeneralesController::class, 'guardar'])->name('inscripcion.datos_generales.guardar');
        Route::post('/constitucion', [ConstitucionController::class, 'guardar'])->name('inscripcion.constitucion.guardar');
        Route::post('/accionistas', [AccionistasController::class, 'guardar'])->name('inscripcion.accionistas.guardar');
        Route::post('/apoderado-legal', [ApoderadoLegalController::class, 'guardar'])->name('inscripcion.apoderado_legal.guardar');
    });

    // Documentos Routes
    Route::prefix('documentos')->group(function () {
        Route::get('/', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::get('/create', [DocumentoController::class, 'create'])->name('documentos.create');
        Route::post('/', [DocumentoController::class, 'store'])->name('documentos.store');
        Route::get('/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
        Route::get('/{documento}/edit', [DocumentoController::class, 'edit'])->name('documentos.edit');
        Route::put('/{documento}', [DocumentoController::class, 'update'])->name('documentos.update');
        Route::delete('/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
        Route::post('/subir', [DocumentosController::class, 'subir'])->name('documentos.subir');
        Route::post('/{tramiteId}/{documentoId}/update-status', [DocumentosController::class, 'updateDocumentStatus'])->name('documentos.update-status');
    });

    // Direccion Routes
    Route::prefix('direccion')->group(function () {
        Route::get('/datos', [DireccionController::class, 'obtenerDatosDireccion'])->name('direccion.datos');
        Route::post('/obtener-datos-direccion', [DireccionController::class, 'obtenerDatosDireccion'])->name('inscripcion.obtener-datos-direccion');
        Route::get('/by-codigo-postal/{codigo}', [DireccionController::class, 'getAddressByCodigoPostal']);
        Route::get('/solicitante/address-info', [DireccionController::class, 'getSolicitanteAddressInfo']);
    });

    // Sectores and Actividades Routes
    Route::prefix('sectores')->group(function () {
        Route::get('/', [SectorActividadController::class, 'getSectores'])->name('sectores.index');
        Route::get('/{sectorId}/actividades', [SectorActividadController::class, 'getActividadesBySector'])->name('sectores.actividades');
    });

    // User Management Routes
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    });

    // Role Management Routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Proveedores Routes
    Route::prefix('proveedores')->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/search', [ProveedorController::class, 'search'])->name('proveedores.search');
    });

    // Revision Routes
    Route::prefix('revision')->group(function () {
        Route::get('/', [RevisionController::class, 'index'])->name('revision.index');
        Route::get('/{id}', [RevisionController::class, 'show'])->name('revision.show');
        Route::post('/{id}/begin', [RevisionController::class, 'beginReview'])->name('revision.begin');
        Route::post('/{id}/complete', [RevisionController::class, 'completeReview'])->name('revision.complete');
        Route::get('/my-revisions', [RevisionController::class, 'myRevisions'])->name('revision.my-revisions');
        Route::get('/iniciar/{tramiteId}', [RevisionController::class, 'iniciarRevision'])->name('revision.iniciar');
        Route::post('/procesar/{tramiteId}', [RevisionController::class, 'procesar'])->name('revision.procesar');
    });

    // Citas Routes
    Route::prefix('citas')->group(function () {
        Route::get('/', [CitaController::class, 'index'])->name('citas.index');
        Route::put('/{cita}', [CitaController::class, 'update'])->name('citas.update');
        Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy');
    });

    // Dias Inhabiles Routes
    Route::prefix('dias_inhabiles')->group(function () {
        Route::post('/', [DiasInhabilesController::class, 'store'])->name('dias_inhabiles.store');
        Route::delete('/{diaInhabil}', [DiasInhabilesController::class, 'destroy'])->name('dias_inhabiles.destroy');
    });

    // Estados Routes
    Route::get('/estados', [EstadoController::class, 'getEstados'])->name('estados');
});