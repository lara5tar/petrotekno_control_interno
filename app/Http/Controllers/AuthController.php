<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login del usuario
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Registrar acción de login
        LogAccion::create([
            'usuario_id' => $user->id,
            'fecha_hora' => now(),
            'accion' => 'login',
            'detalles' => ['ip' => $request->ip()],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'rol' => $user->rol->nombre_rol ?? null,
                    'permisos' => $user->getPermissions()->pluck('nombre_permiso'),
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout del usuario
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        // Registrar acción de logout
        LogAccion::create([
            'usuario_id' => $user->id,
            'fecha_hora' => now(),
            'accion' => 'logout',
            'detalles' => ['ip' => $request->ip()],
        ]);

        // Revocar todos los tokens del usuario
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso',
        ]);
    }

    /**
     * Obtener usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['rol', 'personal.categoria']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'rol' => $user->rol->nombre_rol ?? null,
                'permisos' => $user->getPermissions()->pluck('nombre_permiso'),
                'personal' => $user->personal ? [
                    'nombre_completo' => $user->personal->nombre_completo,
                    'categoria' => $user->personal->categoria->nombre_categoria ?? null,
                ] : null,
            ],
        ]);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual es incorrecta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $user->id,
            'fecha_hora' => now(),
            'accion' => 'cambio_password',
            'tabla_afectada' => 'users',
            'registro_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }
}
