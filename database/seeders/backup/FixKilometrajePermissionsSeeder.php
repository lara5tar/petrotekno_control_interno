<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class FixKilometrajePermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Buscar o crear rol de administrador
        $adminRole = Role::firstOrCreate([
            'nombre_rol' => 'Administrador'
        ], [
            'descripcion' => 'Rol con acceso completo al sistema'
        ]);

        $this->command->info("Rol administrador: {$adminRole->nombre_rol} (ID: {$adminRole->id})");

        // 2. Buscar los permisos de kilometrajes
        $permissions = [
            'ver_kilometrajes',
            'crear_kilometrajes', 
            'editar_kilometrajes',
            'eliminar_kilometrajes'
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('nombre_permiso', $permissionName)->first();
            
            if ($permission) {
                // Verificar si el rol ya tiene el permiso
                $exists = DB::table('roles_permisos')
                    ->where('rol_id', $adminRole->id)
                    ->where('permiso_id', $permission->id)
                    ->exists();

                if (!$exists) {
                    DB::table('roles_permisos')->insert([
                        'rol_id' => $adminRole->id,
                        'permiso_id' => $permission->id
                    ]);
                    $this->command->info("✓ Permiso '{$permissionName}' asignado al rol administrador.");
                } else {
                    $this->command->info("- Permiso '{$permissionName}' ya existe en el rol administrador.");
                }
            } else {
                $this->command->error("✗ Permiso '{$permissionName}' no encontrado en la base de datos.");
            }
        }

        // 3. Asignar rol de administrador a todos los usuarios activos
        $users = User::all();
        
        foreach ($users as $user) {
            if (!$user->rol_id) {
                $user->update(['rol_id' => $adminRole->id]);
                $this->command->info("✓ Rol administrador asignado al usuario: {$user->name} ({$user->email})");
            } else {
                $this->command->info("- Usuario {$user->name} ya tiene rol asignado: " . ($user->rol ? $user->rol->nombre_rol : 'Sin rol'));
            }
        }

        // 4. Verificación final
        $firstUser = User::first();
        if ($firstUser && $firstUser->rol) {
            $this->command->info("=== VERIFICACIÓN FINAL ===");
            $this->command->info("Usuario: {$firstUser->name}");
            $this->command->info("Rol: {$firstUser->rol->nombre_rol}");
            $this->command->info("Permisos del usuario:");
            foreach ($firstUser->rol->permisos as $permiso) {
                $this->command->info("  - {$permiso->nombre_permiso}");
            }
        }

        $this->command->info("=== CONFIGURACIÓN DE PERMISOS COMPLETADA ===");
    }
}
