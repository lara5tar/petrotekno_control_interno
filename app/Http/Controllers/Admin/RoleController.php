<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:ver_roles'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:crear_roles'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:editar_roles'])->only(['edit', 'update', 'permissions', 'updatePermissions']);
        $this->middleware(['auth', 'permission:eliminar_roles'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::with(['permisos', 'usuarios'])->get();
            
            return view('admin.roles.index', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error en RoleController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar los roles: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $permissions = Permission::all()->groupBy(function($permission) {
                // Agrupar por módulo basado en el prefijo del nombre del permiso
                $parts = explode('_', $permission->nombre_permiso);
                return isset($parts[1]) ? $parts[1] : 'general';
            });
            
            return view('admin.roles.create', compact('permissions'));
        } catch (\Exception $e) {
            Log::error('Error en RoleController@create: ' . $e->getMessage());
            return redirect()->route('admin.roles.index')->with('error', 'Error al cargar la página de creación');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre_rol' => 'required|string|max:255|unique:roles,nombre_rol',
                'descripcion' => 'nullable|string|max:500',
                'permissions' => 'array'
            ]);

            DB::beginTransaction();

            $role = Role::create([
                'nombre_rol' => $request->nombre_rol,
                'descripcion' => $request->descripcion
            ]);

            if ($request->has('permissions') && !empty($request->permissions)) {
                $role->permisos()->attach($request->permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en RoleController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el rol: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        try {
            $role->load(['permisos', 'usuarios.personal']);
            return view('admin.roles.show', compact('role'));
        } catch (\Exception $e) {
            Log::error('Error en RoleController@show: ' . $e->getMessage());
            return redirect()->route('admin.roles.index')->with('error', 'Error al cargar el rol');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        try {
            $permissions = Permission::all()->groupBy(function($permission) {
                $parts = explode('_', $permission->nombre_permiso);
                return isset($parts[1]) ? $parts[1] : 'general';
            });
            
            $rolePermissions = $role->permisos->pluck('id')->toArray();
            
            return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
        } catch (\Exception $e) {
            Log::error('Error en RoleController@edit: ' . $e->getMessage());
            return redirect()->route('admin.roles.index')->with('error', 'Error al cargar la página de edición');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        try {
            $request->validate([
                'nombre_rol' => 'required|string|max:255|unique:roles,nombre_rol,' . $role->id,
                'descripcion' => 'nullable|string|max:500',
                'permissions' => 'array'
            ]);

            DB::beginTransaction();

            $role->update([
                'nombre_rol' => $request->nombre_rol,
                'descripcion' => $request->descripcion
            ]);

            $role->permisos()->sync($request->permissions ?? []);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en RoleController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el rol: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            if ($role->usuarios()->count() > 0) {
                return redirect()->route('admin.roles.index')
                    ->with('error', 'No se puede eliminar un rol que tiene usuarios asignados');
            }

            // No permitir eliminar roles del sistema
            if (in_array($role->nombre_rol, ['Admin', 'Administrador', 'Supervisor', 'Operador'])) {
                return redirect()->route('admin.roles.index')
                    ->with('error', 'No se puede eliminar un rol del sistema');
            }

            DB::beginTransaction();
            
            $role->permisos()->detach();
            $role->delete();
            
            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en RoleController@destroy: ' . $e->getMessage());
            return redirect()->route('admin.roles.index')
                ->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }

    /**
     * Show permissions management for a role
     */
    public function permissions(Role $role)
    {
        try {
            $permissions = Permission::all()->groupBy(function($permission) {
                $parts = explode('_', $permission->nombre_permiso);
                return isset($parts[1]) ? $parts[1] : 'general';
            });
            
            $rolePermissions = $role->permisos->pluck('id')->toArray();
            
            return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
        } catch (\Exception $e) {
            Log::error('Error en RoleController@permissions: ' . $e->getMessage());
            return redirect()->route('admin.roles.index')->with('error', 'Error al cargar los permisos');
        }
    }

    /**
     * Update permissions for a role
     */
    public function updatePermissions(Request $request, Role $role)
    {
        try {
            $request->validate([
                'permissions' => 'array'
            ]);

            DB::beginTransaction();
            
            $role->permisos()->sync($request->permissions ?? []);
            
            DB::commit();

            return redirect()->route('admin.roles.permissions', $role)
                ->with('success', 'Permisos actualizados exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en RoleController@updatePermissions: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar los permisos: ' . $e->getMessage());
        }
    }

    /**
     * Get role users for AJAX
     */
    public function users(Role $role)
    {
        try {
            $users = $role->usuarios()->with('personal')->get();
            
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error en RoleController@users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los usuarios'
            ], 500);
        }
    }
    
    /**
     * Obtener información rápida de un rol para mostrar en configuración
     */
    public function quickInfo(Role $role)
    {
        try {
            $role->load(['usuarios', 'permisos']);
            
            $permisosGrouped = $role->permisos->groupBy(function($permiso) {
                $partes = explode('_', $permiso->nombre_permiso);
                return count($partes) > 1 ? implode('_', array_slice($partes, 1)) : 'General';
            });
            
            $data = [
                'role' => [
                    'id' => $role->id,
                    'nombre' => $role->nombre_rol,
                    'descripcion' => $role->descripcion,
                    'usuarios_count' => $role->usuarios->count(),
                    'permisos_count' => $role->permisos->count(),
                    'created_at' => $role->created_at->format('d/m/Y'),
                ],
                'usuarios' => $role->usuarios->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'last_login' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca'
                    ];
                }),
                'modulos_permisos' => $permisosGrouped->map(function($permisos, $modulo) {
                    return [
                        'modulo' => str_replace('_', ' ', ucfirst($modulo)),
                        'count' => $permisos->count(),
                        'permisos' => $permisos->pluck('nombre_permiso')->toArray()
                    ];
                })->values()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener información rápida del rol: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la información del rol'
            ], 500);
        }
    }
}
