<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login del usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Registrar acción de login
        LogAccion::create([
            'usuario_id' => $user->id,
            'fecha_hora' => now(),
            'accion' => 'login',
            'detalles' => ['ip' => $request->ip()]
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nombre_usuario' => $user->nombre_usuario,
                    'email' => $user->email,
                    'rol' => $user->rol->nombre_rol ?? null,
                    'permisos' => $user->getPermissions()->pluck('nombre_permiso'),
                ],
                'token' => $token
            ]
        ]);
    }

    /**
     * Logout del usuario
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Registrar acción de logout
        LogAccion::create([
            'usuario_id' => $user->id,
            'fecha_hora' => now(),
            'accion' => 'logout',
            'detalles' => ['ip' => $request->ip()]
        ]);

        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }

    /**
     * Obtener usuario autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['rol', 'personal.categoria']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nombre_usuario' => $user->nombre_usuario,
                'email' => $user->email,
                'rol' => $user->rol->nombre_rol ?? null,
                'permisos' => $user->getPermissions()->pluck('nombre_permiso'),
                'personal' => $user->personal ? [
                    'nombre_completo' => $user->personal->nombre_completo,
                    'categoria' => $user->personal->categoria->nombre_categoria ?? null,
                ] : null
            ]
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $user->id,
            'fecha_hora' => now(),
            'accion' => 'cambio_password',
            'tabla_afectada' => 'users',
            'registro_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }
}
