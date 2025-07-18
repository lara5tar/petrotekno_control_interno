<?php

namespace Database\Seeders;

use App\Models\CatalogoEstatus;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener estatus existentes
        $estatusActivo = CatalogoEstatus::where('nombre_estatus', 'activo')->first();
        $estatusMantenimiento = CatalogoEstatus::where('nombre_estatus', 'mantenimiento')->first();
        $estatusDisponible = CatalogoEstatus::where('nombre_estatus', 'disponible')->first();
        $estatusAsignado = CatalogoEstatus::where('nombre_estatus', 'asignado')->first();

        // Crear vehículos de ejemplo
        $vehiculos = [
            [
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'anio' => 2022,
                'n_serie' => 'TOY2022001',
                'placas' => 'ABC-001',
                'estatus_id' => $estatusActivo->id,
                'kilometraje_actual' => 25000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => 30000,
                'observaciones' => 'Vehículo en excelente estado',
            ],
            [
                'marca' => 'Ford',
                'modelo' => 'F-150',
                'anio' => 2021,
                'n_serie' => 'FOR2021001',
                'placas' => 'DEF-002',
                'estatus_id' => $estatusAsignado->id,
                'kilometraje_actual' => 45000,
                'intervalo_km_motor' => 7500,
                'intervalo_km_transmision' => 80000,
                'intervalo_km_hidraulico' => 40000,
                'observaciones' => 'Asignado a obra principal',
            ],
            [
                'marca' => 'Chevrolet',
                'modelo' => 'Silverado',
                'anio' => 2020,
                'n_serie' => 'CHE2020001',
                'placas' => 'GHI-003',
                'estatus_id' => $estatusMantenimiento->id,
                'kilometraje_actual' => 85000,
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 50000,
                'intervalo_km_hidraulico' => 25000,
                'observaciones' => 'En mantenimiento preventivo',
            ],
            [
                'marca' => 'Nissan',
                'modelo' => 'Frontier',
                'anio' => 2023,
                'n_serie' => 'NIS2023001',
                'placas' => 'JKL-004',
                'estatus_id' => $estatusDisponible->id,
                'kilometraje_actual' => 5000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => 30000,
                'observaciones' => 'Vehículo nuevo disponible',
            ],
            [
                'marca' => 'Honda',
                'modelo' => 'Ridgeline',
                'anio' => 2019,
                'n_serie' => 'HON2019001',
                'placas' => 'MNO-005',
                'estatus_id' => $estatusActivo->id,
                'kilometraje_actual' => 120000,
                'intervalo_km_motor' => 7500,
                'intervalo_km_transmision' => 90000,
                'intervalo_km_hidraulico' => 45000,
                'observaciones' => 'Alto kilometraje pero en buen estado',
            ],
        ];

        foreach ($vehiculos as $vehiculoData) {
            Vehiculo::updateOrCreate(
                ['n_serie' => $vehiculoData['n_serie']],
                $vehiculoData
            );
        }

        // Crear algunos vehículos adicionales manualmente
        $vehiculosAdicionales = [
            [
                'marca' => 'Volkswagen',
                'modelo' => 'Amarok',
                'anio' => 2021,
                'n_serie' => 'VOL2021001',
                'placas' => 'PQR-006',
                'estatus_id' => $estatusActivo->id,
                'kilometraje_actual' => 35000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 60000,
                'observaciones' => 'Vehículo en rotación',
            ],
            [
                'marca' => 'Isuzu',
                'modelo' => 'D-Max',
                'anio' => 2020,
                'n_serie' => 'ISU2020001',
                'placas' => 'STU-007',
                'estatus_id' => $estatusDisponible->id,
                'kilometraje_actual' => 55000,
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 40000,
                'observaciones' => 'Disponible para nueva asignación',
            ],
        ];

        foreach ($vehiculosAdicionales as $vehiculoData) {
            Vehiculo::updateOrCreate(
                ['n_serie' => $vehiculoData['n_serie']],
                $vehiculoData
            );
        }
    }
}
