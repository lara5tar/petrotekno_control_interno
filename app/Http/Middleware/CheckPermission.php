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
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        $user = $request->user();
        $user->load('rol.permisos'); // Cargar relaciones explícitamente

        foreach ($permissions as $permission) {
            if (! $user->hasPermission($permission)) {
                return response()->json([
                    'success' => false,
                    'message' => "No tienes el permiso '{$permission}' necesario para acceder a este recurso",
                ], 403);
            }
        }

        return $next($request);
    }
}
