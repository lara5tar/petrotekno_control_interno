<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::updateOrCreate(
            ['nombre_rol' => 'Admin'],
            ['descripcion' => 'Administrador con acceso completo al sistema']
        );

        $supervisorRole = Role::updateOrCreate(
            ['nombre_rol' => 'Supervisor'],
            ['descripcion' => 'Supervisor con acceso limitado de gestión']
        );

        $operadorRole = Role::updateOrCreate(
            ['nombre_rol' => 'Operador'],
            ['descripcion' => 'Operador con acceso básico de consulta']
        );

        // Limpiar permisos existentes antes de reasignar
        $adminRole->permisos()->detach();
        $supervisorRole->permisos()->detach();
        $operadorRole->permisos()->detach();

        // Asignar todos los permisos al Admin
        $allPermissions = Permission::all();
        $adminRole->permisos()->attach($allPermissions->pluck('id'));

        // Permisos para Supervisor
        $supervisorPermissions = Permission::whereIn('nombre_permiso', [
            'ver_usuarios',
            'editar_usuarios',
            'ver_roles',
            'ver_permisos',
            'ver_personal',
            'crear_personal',
            'editar_personal',
            'ver_vehiculos',
            'crear_vehiculos',
            'editar_vehiculos',
            'ver_obras',
            'crear_obras',
            'actualizar_obras',
            'ver_documentos',
            'crear_documentos',
            'editar_documentos',
            'ver_mantenimientos',
            'crear_mantenimientos',
            'actualizar_mantenimientos',
            'ver_asignaciones',
            'crear_asignaciones',
            'editar_asignaciones',
            'liberar_asignaciones',
            'ver_catalogos',
        ])->get();
        $supervisorRole->permisos()->attach($supervisorPermissions->pluck('id'));

        // Permisos para Operador
        $operadorPermissions = Permission::whereIn('nombre_permiso', [
            'ver_personal',
            'ver_vehiculos',
            'ver_obras',
            'ver_documentos',
            'ver_mantenimientos',
            'ver_asignaciones',
            'ver_catalogos',
        ])->get();
        $operadorRole->permisos()->attach($operadorPermissions->pluck('id'));
    }
}
