<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::create([
            'nombre_rol' => 'Admin',
            'descripcion' => 'Administrador con acceso completo al sistema'
        ]);

        $supervisorRole = Role::create([
            'nombre_rol' => 'Supervisor',
            'descripcion' => 'Supervisor con acceso limitado de gestión'
        ]);

        $operadorRole = Role::create([
            'nombre_rol' => 'Operador',
            'descripcion' => 'Operador con acceso básico de consulta'
        ]);

        // Asignar todos los permisos al Admin
        $allPermissions = Permission::all();
        $adminRole->permisos()->attach($allPermissions->pluck('id'));

        // Permisos para Supervisor
        $supervisorPermissions = Permission::whereIn('nombre_permiso', [
            'ver_usuarios', 'editar_usuarios',
            'ver_roles', 'ver_permisos',
            'ver_personal', 'crear_personal', 'editar_personal',
            'ver_vehiculos', 'crear_vehiculos', 'editar_vehiculos'
        ])->get();
        $supervisorRole->permisos()->attach($supervisorPermissions->pluck('id'));

        // Permisos para Operador
        $operadorPermissions = Permission::whereIn('nombre_permiso', [
            'ver_personal',
            'ver_vehiculos'
        ])->get();
        $operadorRole->permisos()->attach($operadorPermissions->pluck('id'));
    }
}
