<?php

namespace Database\Seeders;

use App\Models\CatalogoEstatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogoEstatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estatus = [
            [
                'nombre_estatus' => 'activo',
                'descripcion' => 'Vehículo en operación normal',
                'activo' => true,
            ],
            [
                'nombre_estatus' => 'mantenimiento',
                'descripcion' => 'Vehículo en proceso de mantenimiento o reparación',
                'activo' => true,
            ],
            [
                'nombre_estatus' => 'fuera_servicio',
                'descripcion' => 'Vehículo temporalmente fuera de servicio',
                'activo' => true,
            ],
            [
                'nombre_estatus' => 'baja',
                'descripcion' => 'Vehículo dado de baja definitivamente',
                'activo' => true,
            ],
            [
                'nombre_estatus' => 'accidentado',
                'descripcion' => 'Vehículo involucrado en accidente',
                'activo' => true,
            ],
            [
                'nombre_estatus' => 'disponible',
                'descripcion' => 'Vehículo disponible para asignación',
                'activo' => true,
            ],
            [
                'nombre_estatus' => 'asignado',
                'descripcion' => 'Vehículo asignado a obra específica',
                'activo' => true,
            ],
        ];

        foreach ($estatus as $estatusData) {
            CatalogoEstatus::updateOrCreate(
                ['nombre_estatus' => $estatusData['nombre_estatus']],
                $estatusData
            );
        }
    }
}
