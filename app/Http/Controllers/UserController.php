<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['rol', 'personal.categoria']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_usuario', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('personal', function ($pq) use ($search) {
                      $pq->where('nombre_completo', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('rol_id')) {
            $query->where('rol_id', $request->rol_id);
        }

        $users = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'rol_id' => 'required|exists:roles,id',
            'personal_id' => 'nullable|exists:personal,id',
        ]);

        $user = User::create([
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'personal_id' => $request->personal_id,
        ]);

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'crear_usuario',
            'tabla_afectada' => 'users',
            'registro_id' => $user->id,
            'detalles' => ['usuario_creado' => $user->nombre_usuario]
        ]);

        $user->load(['rol', 'personal.categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['rol', 'personal.categoria', 'logAcciones' => function ($query) {
            $query->orderBy('fecha_hora', 'desc')->limit(10);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nombre_usuario' => [
                'required',
                'string',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8',
            'rol_id' => 'required|exists:roles,id',
            'personal_id' => 'nullable|exists:personal,id',
        ]);

        $oldData = $user->toArray();
        
        $updateData = [
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
            'personal_id' => $request->personal_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'actualizar_usuario',
            'tabla_afectada' => 'users',
            'registro_id' => $user->id,
            'detalles' => [
                'usuario_actualizado' => $user->nombre_usuario,
                'cambios' => array_diff_assoc($updateData, $oldData)
            ]
        ]);

        $user->load(['rol', 'personal.categoria']);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // No permitir eliminar el propio usuario
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 400);
        }

        $userName = $user->nombre_usuario;
        $user->delete();

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'eliminar_usuario',
            'tabla_afectada' => 'users',
            'registro_id' => $id,
            'detalles' => ['usuario_eliminado' => $userName]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }

    /**
     * Restaurar un usuario eliminado
     */
    public function restore(Request $request, string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'restaurar_usuario',
            'tabla_afectada' => 'users',
            'registro_id' => $user->id,
            'detalles' => ['usuario_restaurado' => $user->nombre_usuario]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario restaurado exitosamente',
            'data' => $user
        ]);
    }
}
