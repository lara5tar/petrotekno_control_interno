<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Ruta principal redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Auth::routes();

// Ruta para login rápido en modo debug
Route::post('/debug-login', function () {
    if (! config('app.debug')) {
        abort(404);
    }

    // Buscar un usuario existente o crear uno por defecto
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'admin@petrotekno.com'],
        [
            'name' => 'Administrador',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]
    );

    Auth::login($user);

    return redirect()->intended('/home');
})->name('debug.login');

// Ruta del dashboard después de iniciar sesión
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Ruta para listar vehículos (vista estática)
Route::get('/vehiculos', function () {
    return view('vehiculos.index');
})->name('vehiculos.index');
