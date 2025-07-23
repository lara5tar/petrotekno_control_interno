<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'sanitize' => \App\Http\Middleware\SanitizeInput::class,
        ]);

        // Aplicar sanitización a las rutas API donde se recibe input de usuario
        $middleware->group('api', [
            'sanitize',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
