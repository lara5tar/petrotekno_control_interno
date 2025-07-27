<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::with('roles')->get();

        return response()->json([
            'success' => true,
            'data' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre_permiso' => 'required|string|unique:permisos',
            'descripcion' => 'nullable|string',
        ]);

        $permission = Permission::create($request->only([
            'nombre_permiso',
            'descripcion',
        ]));

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'crear_permiso',
            'tabla_afectada' => 'permisos',
            'registro_id' => $permission->id,
            'detalles' => ['permiso_creado' => $permission->nombre_permiso],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permiso creado exitosamente',
            'data' => $permission,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $permission = Permission::with('roles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'nombre_permiso' => 'required|string|unique:permisos,nombre_permiso,' . $permission->id,
            'descripcion' => 'nullable|string',
        ]);

        $oldData = $permission->toArray();

        $permission->update($request->only([
            'nombre_permiso',
            'descripcion',
        ]));

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'actualizar_permiso',
            'tabla_afectada' => 'permisos',
            'registro_id' => $permission->id,
            'detalles' => [
                'permiso_actualizado' => $permission->nombre_permiso,
                'cambios' => array_diff_assoc($request->only(['nombre_permiso', 'descripcion']), $oldData),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permiso actualizado exitosamente',
            'data' => $permission,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);

        // Verificar si está asignado a algún rol
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el permiso porque está asignado a uno o más roles',
            ], 400);
        }

        $permissionName = $permission->nombre_permiso;
        $permission->delete();

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'eliminar_permiso',
            'tabla_afectada' => 'permisos',
            'registro_id' => $id,
            'detalles' => ['permiso_eliminado' => $permissionName],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permiso eliminado exitosamente',
        ]);
    }
}
