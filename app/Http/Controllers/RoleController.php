<?php

namespace App\Http\Controllers;

use App\Models\LogAccion;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permisos')->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_rol' => 'required|string|unique:roles',
            'descripcion' => 'nullable|string',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $role = Role::create([
            'nombre_rol' => $request->nombre_rol,
            'descripcion' => $request->descripcion,
        ]);

        if ($request->has('permisos')) {
            $role->permisos()->attach($request->permisos);
        }

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'crear_rol',
            'tabla_afectada' => 'roles',
            'registro_id' => $role->id,
            'detalles' => ['rol_creado' => $role->nombre_rol],
        ]);

        $role->load('permisos');

        return response()->json([
            'success' => true,
            'message' => 'Rol creado exitosamente',
            'data' => $role,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permisos', 'usuarios')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'nombre_rol' => 'required|string|unique:roles,nombre_rol,'.$role->id,
            'descripcion' => 'nullable|string',
            'permisos' => 'array',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $oldData = $role->toArray();

        $role->update([
            'nombre_rol' => $request->nombre_rol,
            'descripcion' => $request->descripcion,
        ]);

        if ($request->has('permisos')) {
            $role->permisos()->sync($request->permisos);
        }

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'actualizar_rol',
            'tabla_afectada' => 'roles',
            'registro_id' => $role->id,
            'detalles' => [
                'rol_actualizado' => $role->nombre_rol,
                'cambios' => array_diff_assoc($request->only(['nombre_rol', 'descripcion']), $oldData),
            ],
        ]);

        $role->load('permisos');

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado exitosamente',
            'data' => $role,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        // No permitir eliminar roles con usuarios asignados
        if ($role->usuarios()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el rol porque tiene usuarios asignados',
            ], 400);
        }

        $roleName = $role->nombre_rol;
        $role->delete();

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'eliminar_rol',
            'tabla_afectada' => 'roles',
            'registro_id' => $id,
            'detalles' => ['rol_eliminado' => $roleName],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado exitosamente',
        ]);
    }

    /**
     * Asignar permiso a rol
     */
    public function attachPermission(Request $request, $roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $role->permisos()->attach($permissionId);

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'asignar_permiso_rol',
            'tabla_afectada' => 'roles_permisos',
            'detalles' => [
                'rol' => $role->nombre_rol,
                'permiso_id' => $permissionId,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permiso asignado al rol exitosamente',
        ]);
    }

    /**
     * Quitar permiso de rol
     */
    public function detachPermission(Request $request, $roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $role->permisos()->detach($permissionId);

        // Registrar acción
        LogAccion::create([
            'usuario_id' => $request->user()->id,
            'fecha_hora' => now(),
            'accion' => 'quitar_permiso_rol',
            'tabla_afectada' => 'roles_permisos',
            'detalles' => [
                'rol' => $role->nombre_rol,
                'permiso_id' => $permissionId,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permiso removido del rol exitosamente',
        ]);
    }
}
