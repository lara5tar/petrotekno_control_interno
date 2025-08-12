<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnsureAdminAllPermissionsSeeder extends Seeder
{
    /**
     * Asegura que el usuario admin tenga todos los permisos disponibles en el sistema
     */
    public function run(): void
    {
        $this->command->info('🔐 Configurando permisos completos para usuario admin...');

        // 1. Buscar o crear el rol de administrador
        $adminRole = Role::firstOrCreate([
            'nombre_rol' => 'Admin'
        ], [
            'descripcion' => 'Administrador con acceso completo al sistema'
        ]);

        // También verificar si existe como "Administrador"
        $adminRoleAlt = Role::firstOrCreate([
            'nombre_rol' => 'Administrador'
        ], [
            'descripcion' => 'Administrador con acceso completo al sistema'
        ]);

        $this->command->info("✅ Roles de administrador verificados");

        // 2. Obtener TODOS los permisos disponibles en el sistema
        $allPermissions = Permission::all();
        $this->command->info("📋 Total de permisos en el sistema: {$allPermissions->count()}");

        // 3. Limpiar permisos existentes y asignar TODOS los permisos
        foreach ([$adminRole, $adminRoleAlt] as $role) {
            // Limpiar permisos existentes
            $role->permisos()->detach();
            
            // Asignar TODOS los permisos
            $role->permisos()->attach($allPermissions->pluck('id'));
            
            $this->command->info("✅ Todos los permisos asignados al rol: {$role->nombre_rol}");
        }

        // 4. Buscar usuario admin y asegurar que tenga el rol correcto
        $adminUsers = User::whereIn('email', [
            'admin@petrotekno.com',
            'administrador@petrotekno.com'
        ])->get();

        foreach ($adminUsers as $user) {
            // Asignar rol de admin si no lo tiene
            if (!$user->rol_id || !in_array($user->rol->nombre_rol, ['Admin', 'Administrador'])) {
                $user->update(['rol_id' => $adminRole->id]);
                $this->command->info("✅ Rol de administrador asignado al usuario: {$user->email}");
            }
        }

        // 5. Si no existe usuario admin, verificar el primer usuario
        if ($adminUsers->isEmpty()) {
            $firstUser = User::first();
            if ($firstUser) {
                $firstUser->update(['rol_id' => $adminRole->id]);
                $this->command->info("✅ Rol de administrador asignado al primer usuario: {$firstUser->email}");
            }
        }

        // 6. Verificación final - mostrar todos los permisos del admin
        $adminUser = User::whereHas('rol', function($q) {
            $q->whereIn('nombre_rol', ['Admin', 'Administrador']);
        })->first();

        if ($adminUser) {
            $this->command->info("\n🎯 VERIFICACIÓN FINAL:");
            $this->command->info("Usuario: {$adminUser->email}");
            $this->command->info("Rol: {$adminUser->rol->nombre_rol}");
            $this->command->info("Total de permisos asignados: {$adminUser->rol->permisos->count()}");
            
            $this->command->info("\n📋 PERMISOS DEL USUARIO ADMIN:");
            $permisos = $adminUser->rol->permisos->sortBy('nombre_permiso');
            foreach ($permisos as $permiso) {
                $this->command->info("  ✓ {$permiso->nombre_permiso} - {$permiso->descripcion}");
            }

            // Verificar permisos específicos importantes
            $permisosImportantes = [
                'administrar_sistema',
                'ver_logs',
                'crear_usuarios',
                'eliminar_usuarios',
                'crear_roles',
                'eliminar_roles',
                'ver_kilometrajes',
                'crear_kilometrajes',
                'editar_kilometrajes',
                'eliminar_kilometrajes'
            ];

            $this->command->info("\n🔍 VERIFICACIÓN DE PERMISOS CRÍTICOS:");
            foreach ($permisosImportantes as $permisoNombre) {
                $tiene = $adminUser->hasPermission($permisoNombre);
                $estado = $tiene ? '✅' : '❌';
                $this->command->info("  {$estado} {$permisoNombre}");
            }
        }

        $this->command->info("\n🎉 ¡Configuración de permisos completos para admin finalizada!");
    }
}