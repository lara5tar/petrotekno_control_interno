<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // Usuarios
            ['nombre_permiso' => 'ver_usuarios', 'descripcion' => 'Ver listado de usuarios'],
            ['nombre_permiso' => 'crear_usuarios', 'descripcion' => 'Crear nuevos usuarios'],
            ['nombre_permiso' => 'editar_usuarios', 'descripcion' => 'Editar usuarios existentes'],
            ['nombre_permiso' => 'eliminar_usuarios', 'descripcion' => 'Eliminar usuarios'],
            
            // Roles
            ['nombre_permiso' => 'ver_roles', 'descripcion' => 'Ver listado de roles'],
            ['nombre_permiso' => 'crear_roles', 'descripcion' => 'Crear nuevos roles'],
            ['nombre_permiso' => 'editar_roles', 'descripcion' => 'Editar roles existentes'],
            ['nombre_permiso' => 'eliminar_roles', 'descripcion' => 'Eliminar roles'],
            
            // Permisos
            ['nombre_permiso' => 'ver_permisos', 'descripcion' => 'Ver listado de permisos'],
            ['nombre_permiso' => 'asignar_permisos', 'descripcion' => 'Asignar permisos a roles'],
            
            // Personal
            ['nombre_permiso' => 'ver_personal', 'descripcion' => 'Ver listado de personal'],
            ['nombre_permiso' => 'crear_personal', 'descripcion' => 'Crear registros de personal'],
            ['nombre_permiso' => 'editar_personal', 'descripcion' => 'Editar personal existente'],
            ['nombre_permiso' => 'eliminar_personal', 'descripcion' => 'Eliminar personal'],
            
            // Vehículos (para futuro)
            ['nombre_permiso' => 'ver_vehiculos', 'descripcion' => 'Ver listado de vehículos'],
            ['nombre_permiso' => 'crear_vehiculos', 'descripcion' => 'Crear registros de vehículos'],
            ['nombre_permiso' => 'editar_vehiculos', 'descripcion' => 'Editar vehículos existentes'],
            ['nombre_permiso' => 'eliminar_vehiculos', 'descripcion' => 'Eliminar vehículos'],
            
            // Sistema
            ['nombre_permiso' => 'ver_logs', 'descripcion' => 'Ver logs del sistema'],
            ['nombre_permiso' => 'administrar_sistema', 'descripcion' => 'Administración completa del sistema'],
        ];

        foreach ($permisos as $permiso) {
            Permission::create($permiso);
        }
    }
}
