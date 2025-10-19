<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado',
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = $request->user();
        $user->load('rol.permisos'); // Cargar relaciones explícitamente

        foreach ($permissions as $permission) {
            if (! $user->hasPermission($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "No tienes el permiso '{$permission}' necesario para acceder a este recurso",
                    ], 403);
                }
                
                // Para requests web, redirigir al home con mensaje de error
                return redirect()->route('home')->with('error', "No tienes permisos suficientes para acceder a esta sección. Permiso requerido: {$permission}");
            }
        }

        return $next($request);
    }
}
