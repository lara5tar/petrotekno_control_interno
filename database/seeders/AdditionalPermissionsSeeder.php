<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class AdditionalPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $additionalPermissions = [
            // Permisos específicos para gestión de roles
            ['nombre_permiso' => 'ver_roles', 'descripcion' => 'Ver lista de roles y sus detalles'],
            ['nombre_permiso' => 'crear_roles', 'descripcion' => 'Crear nuevos roles en el sistema'],
            ['nombre_permiso' => 'editar_roles', 'descripcion' => 'Editar roles existentes y sus permisos'],
            ['nombre_permiso' => 'eliminar_roles', 'descripcion' => 'Eliminar roles del sistema'],
            
            // Permisos para gestión de permisos
            ['nombre_permiso' => 'ver_permisos', 'descripcion' => 'Ver lista de permisos disponibles'],
            ['nombre_permiso' => 'crear_permisos', 'descripcion' => 'Crear nuevos permisos'],
            ['nombre_permiso' => 'editar_permisos', 'descripcion' => 'Editar permisos existentes'],
            ['nombre_permiso' => 'eliminar_permisos', 'descripcion' => 'Eliminar permisos del sistema'],
            
            // Permisos para configuración
            ['nombre_permiso' => 'ver_configuracion', 'descripcion' => 'Acceder al panel de configuración'],
            ['nombre_permiso' => 'editar_configuracion', 'descripcion' => 'Modificar configuraciones del sistema'],
            
            // Permisos para categorías de personal
            ['nombre_permiso' => 'ver_categorias_personal', 'descripcion' => 'Ver categorías de personal'],
            ['nombre_permiso' => 'crear_categorias_personal', 'descripcion' => 'Crear categorías de personal'],
            ['nombre_permiso' => 'editar_categorias_personal', 'descripcion' => 'Editar categorías de personal'],
            ['nombre_permiso' => 'eliminar_categorias_personal', 'descripcion' => 'Eliminar categorías de personal'],
            
            // Permisos para respaldos y mantenimiento
            ['nombre_permiso' => 'ver_respaldos', 'descripcion' => 'Ver información de respaldos'],
            ['nombre_permiso' => 'crear_respaldos', 'descripcion' => 'Crear respaldos del sistema'],
            ['nombre_permiso' => 'restaurar_respaldos', 'descripcion' => 'Restaurar respaldos'],
            ['nombre_permiso' => 'eliminar_respaldos', 'descripcion' => 'Eliminar respaldos'],
            
            // Permisos de administración general
            ['nombre_permiso' => 'admin_sistema', 'descripcion' => 'Acceso completo de administrador del sistema'],
            ['nombre_permiso' => 'gestionar_sesiones', 'descripcion' => 'Gestionar sesiones de usuarios'],
            ['nombre_permiso' => 'ver_estadisticas', 'descripcion' => 'Ver estadísticas del sistema'],
            
            // Permisos para alertas y notificaciones
            ['nombre_permiso' => 'gestionar_alertas', 'descripcion' => 'Configurar y gestionar alertas del sistema'],
            ['nombre_permiso' => 'ver_notificaciones', 'descripcion' => 'Ver notificaciones del sistema'],
            ['nombre_permiso' => 'enviar_notificaciones', 'descripcion' => 'Enviar notificaciones a usuarios'],
        ];

        foreach ($additionalPermissions as $permissionData) {
            Permission::firstOrCreate(
                ['nombre_permiso' => $permissionData['nombre_permiso']],
                ['descripcion' => $permissionData['descripcion']]
            );
        }

        $this->command->info('Permisos adicionales creados exitosamente.');
    }
}
