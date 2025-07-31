<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissionWeb
{
    /**
     * Handle an incoming request for web views.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            \Log::debug('CheckPermissionWeb: Usuario no autenticado');
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        $user = Auth::user();
        \Log::debug('CheckPermissionWeb: Usuario autenticado: ' . $user->id . ', verificando permiso: ' . $permission);

        // Verificar si el usuario tiene el permiso requerido
        if (!$user->hasPermission($permission)) {
            \Log::debug('CheckPermissionWeb: Usuario no tiene el permiso: ' . $permission);
            // Para requests web, redirigir con mensaje de error
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        \Log::debug('CheckPermissionWeb: Permiso concedido para: ' . $permission);
        return $next($request);
    }
}
