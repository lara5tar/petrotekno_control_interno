<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Enums\EstadoVehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiculoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚗 Creando vehículos de prueba...');
        
        // Crear varios vehículos disponibles para poder eliminar
        $vehiculos = [
            [
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'anio' => 2020,
                'placas' => 'TST-001',
                'n_serie' => 'VIN001TEST',
                'estatus' => EstadoVehiculo::DISPONIBLE->value,
                'kilometraje_actual' => 15000,
            ],
            [
                'marca' => 'Ford',
                'modelo' => 'Ranger',
                'anio' => 2019,
                'placas' => 'TST-002',
                'n_serie' => 'VIN002TEST',
                'estatus' => EstadoVehiculo::DISPONIBLE->value,
                'kilometraje_actual' => 25000,
            ],
            [
                'marca' => 'Chevrolet',
                'modelo' => 'Colorado',
                'anio' => 2021,
                'placas' => 'TST-003',
                'n_serie' => 'VIN003TEST',
                'estatus' => EstadoVehiculo::DISPONIBLE->value,
                'kilometraje_actual' => 8000,
            ],
        ];
        
        foreach ($vehiculos as $vehiculoData) {
            Vehiculo::create($vehiculoData);
            $this->command->info("✅ Vehículo creado: {$vehiculoData['marca']} {$vehiculoData['modelo']} - {$vehiculoData['placas']}");
        }
        
        $this->command->info('🎯 Vehículos de prueba creados exitosamente');
    }
}
