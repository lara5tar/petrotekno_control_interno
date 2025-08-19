<?php

namespace Database\Seeders;

use App\Models\Permission;
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

            // Vehículos
            ['nombre_permiso' => 'ver_vehiculos', 'descripcion' => 'Ver listado de vehículos'],
            ['nombre_permiso' => 'crear_vehiculos', 'descripcion' => 'Crear registros de vehículos'],
            ['nombre_permiso' => 'editar_vehiculos', 'descripcion' => 'Editar vehículos existentes'],
            ['nombre_permiso' => 'eliminar_vehiculos', 'descripcion' => 'Eliminar vehículos'],
            ['nombre_permiso' => 'restaurar_vehiculos', 'descripcion' => 'Restaurar vehículos eliminados'],

            // Obras
            ['nombre_permiso' => 'ver_obras', 'descripcion' => 'Ver listado de obras'],
            ['nombre_permiso' => 'crear_obras', 'descripcion' => 'Crear nuevas obras'],
            ['nombre_permiso' => 'actualizar_obras', 'descripcion' => 'Actualizar obras existentes'],
            ['nombre_permiso' => 'eliminar_obras', 'descripcion' => 'Eliminar obras'],
            ['nombre_permiso' => 'restaurar_obras', 'descripcion' => 'Restaurar obras eliminadas'],

            // Documentos
            ['nombre_permiso' => 'ver_documentos', 'descripcion' => 'Ver listado de documentos'],
            ['nombre_permiso' => 'crear_documentos', 'descripcion' => 'Crear nuevos documentos'],
            ['nombre_permiso' => 'editar_documentos', 'descripcion' => 'Editar documentos existentes'],
            ['nombre_permiso' => 'eliminar_documentos', 'descripcion' => 'Eliminar documentos'],

            // Mantenimientos
            ['nombre_permiso' => 'ver_mantenimientos', 'descripcion' => 'Ver listado de mantenimientos'],
            ['nombre_permiso' => 'crear_mantenimientos', 'descripcion' => 'Crear nuevos mantenimientos'],
            ['nombre_permiso' => 'actualizar_mantenimientos', 'descripcion' => 'Actualizar mantenimientos existentes'],
            ['nombre_permiso' => 'eliminar_mantenimientos', 'descripcion' => 'Eliminar mantenimientos'],
            ['nombre_permiso' => 'restaurar_mantenimientos', 'descripcion' => 'Restaurar mantenimientos eliminados'],

            // Asignaciones
            ['nombre_permiso' => 'ver_asignaciones', 'descripcion' => 'Ver listado de asignaciones'],
            ['nombre_permiso' => 'crear_asignaciones', 'descripcion' => 'Crear nuevas asignaciones'],
            ['nombre_permiso' => 'editar_asignaciones', 'descripcion' => 'Editar asignaciones existentes'],
            ['nombre_permiso' => 'eliminar_asignaciones', 'descripcion' => 'Eliminar asignaciones'],
            ['nombre_permiso' => 'liberar_asignaciones', 'descripcion' => 'Liberar asignaciones activas'],

            // Catálogos
            ['nombre_permiso' => 'ver_catalogos', 'descripcion' => 'Ver catálogos del sistema'],
            ['nombre_permiso' => 'crear_catalogos', 'descripcion' => 'Crear elementos en catálogos'],
            ['nombre_permiso' => 'editar_catalogos', 'descripcion' => 'Editar elementos de catálogos'],
            ['nombre_permiso' => 'eliminar_catalogos', 'descripcion' => 'Eliminar elementos de catálogos'],

            // Kilometrajes
            ['nombre_permiso' => 'ver_kilometrajes', 'descripcion' => 'Ver listado de kilometrajes'],
            ['nombre_permiso' => 'crear_kilometrajes', 'descripcion' => 'Crear registros de kilometrajes'],
            ['nombre_permiso' => 'editar_kilometrajes', 'descripcion' => 'Editar kilometrajes existentes'],
            ['nombre_permiso' => 'eliminar_kilometrajes', 'descripcion' => 'Eliminar kilometrajes'],

            // Alertas
            ['nombre_permiso' => 'ver_alertas', 'descripcion' => 'Ver alertas del sistema'],
            ['nombre_permiso' => 'crear_alertas', 'descripcion' => 'Crear nuevas alertas'],
            ['nombre_permiso' => 'editar_alertas', 'descripcion' => 'Editar alertas existentes'],
            ['nombre_permiso' => 'eliminar_alertas', 'descripcion' => 'Eliminar alertas'],
            ['nombre_permiso' => 'gestionar_alertas', 'descripcion' => 'Gestión completa de alertas'],

            // Sistema
            ['nombre_permiso' => 'ver_logs', 'descripcion' => 'Ver logs del sistema'],
            ['nombre_permiso' => 'administrar_sistema', 'descripcion' => 'Administración completa del sistema'],
        ];

        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(
                ['nombre_permiso' => $permiso['nombre_permiso']],
                $permiso
            );
        }
    }
}
