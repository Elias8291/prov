<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorBusquedaController;

Route::get('/proveedores', [ProveedorBusquedaController::class, 'buscarProveedores']);