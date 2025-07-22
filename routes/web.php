<?php

use App\Http\Controllers\KilometrajeController;
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
