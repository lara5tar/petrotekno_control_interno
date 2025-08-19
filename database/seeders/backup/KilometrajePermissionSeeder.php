<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class KilometrajePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            [
                'nombre_permiso' => 'ver_kilometrajes',
                'descripcion' => 'Permite ver los kilometrajes registrados',
            ],
            [
                'nombre_permiso' => 'crear_kilometrajes',
                'descripcion' => 'Permite crear nuevos registros de kilometraje',
            ],
            [
                'nombre_permiso' => 'editar_kilometrajes',
                'descripcion' => 'Permite editar registros de kilometraje existentes',
            ],
            [
                'nombre_permiso' => 'eliminar_kilometrajes',
                'descripcion' => 'Permite eliminar registros de kilometraje',
            ],
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(
                ['nombre_permiso' => $permiso['nombre_permiso']],
                $permiso
            );
        }
    }
}
