<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAccessConfiguration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Verificar si el usuario tiene al menos uno de los permisos de configuración
        $configPermissions = [
            'ver_roles',
            'ver_usuarios', 
            'ver_configuracion',
            'ver_logs',
            'admin_sistema'
        ];

        $hasConfigPermission = false;
        foreach ($configPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                $hasConfigPermission = true;
                break;
            }
        }

        if (!$hasConfigPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a la configuración'
                ], 403);
            }

            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}
