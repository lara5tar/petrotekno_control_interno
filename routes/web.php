<?php

use App\Http\Controllers\KilometrajeController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Ruta principal redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación (sin registro público)
Auth::routes(['register' => false]);

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {

    // Ruta del dashboard después de iniciar sesión
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Rutas web para vehículos (para Blade views)
    Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
        Route::get('/', [VehiculoController::class, 'index'])->name('index');
        Route::get('/create', [VehiculoController::class, 'create'])->name('create');
        Route::post('/', [VehiculoController::class, 'store'])->name('store');
        Route::get('/{id}', [VehiculoController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [VehiculoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VehiculoController::class, 'update'])->name('update');
        Route::delete('/{id}', [VehiculoController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [VehiculoController::class, 'restore'])->name('restore');
    });

    // Rutas web para mantenimientos (para Blade views)
    Route::prefix('mantenimientos')->name('mantenimientos.')->group(function () {
        Route::get('/', [MantenimientoController::class, 'index'])->name('index');
        Route::get('/create', [MantenimientoController::class, 'create'])->name('create');
        Route::post('/', [MantenimientoController::class, 'store'])->name('store');
        Route::get('/{id}', [MantenimientoController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MantenimientoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MantenimientoController::class, 'update'])->name('update');
        Route::delete('/{id}', [MantenimientoController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [MantenimientoController::class, 'restore'])->name('restore');
    });

    // Rutas web para personal (para Blade views)
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/', [PersonalController::class, 'index'])->name('index');
        Route::get('/create', [PersonalController::class, 'create'])->name('create');
        Route::post('/', [PersonalController::class, 'store'])->name('store');
        Route::get('/{id}', [PersonalController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PersonalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PersonalController::class, 'update'])->name('update');
        Route::delete('/{id}', [PersonalController::class, 'destroy'])->name('destroy');
    });

    // Rutas web para obras (para Blade views)
    Route::prefix('obras')->name('obras.')->group(function () {
        Route::get('/', [App\Http\Controllers\ObraController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ObraController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ObraController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\ObraController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\ObraController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\ObraController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\ObraController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [App\Http\Controllers\ObraController::class, 'restore'])->name('restore');
    });

    // Rutas web para kilometrajes (para Blade views)
    Route::prefix('kilometrajes')->name('kilometrajes.')->group(function () {
        Route::get('/', [KilometrajeController::class, 'index'])->name('index');
        Route::get('/create', [KilometrajeController::class, 'create'])->name('create');
        Route::post('/', [KilometrajeController::class, 'store'])->name('store');
        Route::get('/vehiculo/{vehiculoId}/historial', [KilometrajeController::class, 'historialPorVehiculo'])->name('historial');
        Route::get('/alertas-mantenimiento', [KilometrajeController::class, 'alertasMantenimiento'])->name('alertas');
        Route::get('/{kilometraje}', [KilometrajeController::class, 'show'])->name('show');
        Route::get('/{kilometraje}/edit', [KilometrajeController::class, 'edit'])->name('edit');
        Route::put('/{kilometraje}', [KilometrajeController::class, 'update'])->name('update');
        Route::delete('/{kilometraje}', [KilometrajeController::class, 'destroy'])->name('destroy');
    });
});
