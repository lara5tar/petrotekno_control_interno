<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

class AssignKilometrajePermissionsSeeder extends Seeder
{
    public function run()
    {
        // Buscar o crear el rol de administrador
        $adminRole = Role::where('nombre_rol', 'Administrador')->first();
        
        if (!$adminRole) {
            $adminRole = Role::create([
                'nombre_rol' => 'Administrador',
                'descripcion' => 'Rol con acceso completo al sistema'
            ]);
            $this->command->info("Rol de administrador creado.");
        }

        // Verificar permisos de kilometrajes existentes
        $permissions = [
            'ver_kilometrajes',
            'crear_kilometrajes', 
            'editar_kilometrajes',
            'eliminar_kilometrajes'
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('nombre_permiso', $permissionName)->first();
            
            if ($permission) {
                // Asignar permiso al rol de administrador si no lo tiene
                if (!$adminRole->permisos()->where('nombre_permiso', $permissionName)->exists()) {
                    $adminRole->permisos()->attach($permission->id);
                    $this->command->info("Permiso '{$permissionName}' asignado al rol administrador.");
                }
            }
        }

        // Asignar rol de administrador a los usuarios principales
        $users = User::whereIn('id', [1, 2])->orWhere('email', 'like', '%admin%')->get();
        
        foreach ($users as $user) {
            if (!$user->rol_id) {
                $user->update(['rol_id' => $adminRole->id]);
                $this->command->info("Rol de administrador asignado al usuario: {$user->name}");
            }
        }

        $this->command->info("Permisos de kilometrajes configurados correctamente.");
    }
}
