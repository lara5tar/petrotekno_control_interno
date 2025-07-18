<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $permisos = [
            'crear_vehiculos' => 'Crear nuevos vehículos en el sistema',
            'editar_vehiculos' => 'Editar información de vehículos existentes',
            'eliminar_vehiculos' => 'Eliminar vehículos del sistema',
            'ver_vehiculos' => 'Ver listado y detalles de vehículos',
            'crear_personal' => 'Crear nuevos registros de personal',
            'editar_personal' => 'Editar información del personal',
            'eliminar_personal' => 'Eliminar registros de personal',
            'ver_personal' => 'Ver listado y detalles del personal',
            'crear_obras' => 'Crear nuevas obras en el sistema',
            'editar_obras' => 'Editar información de obras existentes',
            'eliminar_obras' => 'Eliminar obras del sistema',
            'ver_obras' => 'Ver listado y detalles de obras',
            'crear_documentos' => 'Crear nuevos documentos',
            'editar_documentos' => 'Editar documentos existentes',
            'eliminar_documentos' => 'Eliminar documentos',
            'ver_documentos' => 'Ver listado y detalles de documentos',
            'crear_mantenimientos' => 'Registrar mantenimientos',
            'editar_mantenimientos' => 'Editar registros de mantenimiento',
            'eliminar_mantenimientos' => 'Eliminar registros de mantenimiento',
            'ver_mantenimientos' => 'Ver historial de mantenimientos',
            'crear_asignaciones' => 'Crear asignaciones de vehículos',
            'editar_asignaciones' => 'Editar asignaciones existentes',
            'eliminar_asignaciones' => 'Eliminar asignaciones',
            'ver_asignaciones' => 'Ver listado de asignaciones',
            'administrar_usuarios' => 'Gestionar usuarios del sistema',
            'administrar_roles' => 'Gestionar roles y permisos',
            'ver_reportes' => 'Acceder a reportes del sistema',
            'exportar_datos' => 'Exportar información del sistema',
        ];

        $permiso = $this->faker->randomElement(array_keys($permisos));

        return [
            'nombre_permiso' => $permiso,
            'descripcion' => $permisos[$permiso],
        ];
    }

    /**
     * Create a specific permission by name.
     */
    public function withName(string $nombre): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_permiso' => $nombre,
            'descripcion' => "Permiso para {$nombre}",
        ]);
    }
}
