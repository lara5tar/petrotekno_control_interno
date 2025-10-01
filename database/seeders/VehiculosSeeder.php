<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\TipoActivo;
use App\Enums\EstadoVehiculo;

class VehiculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener tipos de activos disponibles
        $vehiculoTipo = TipoActivo::where('nombre', 'Vehículo')->first();
        $maquinariaTipo = TipoActivo::where('nombre', 'Maquinaria')->first();

        // Crear vehículos de prueba
        $vehiculos = [
            // Camionetas
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Ford',
                'modelo' => 'F-150',
                'anio' => 2022,
                'n_serie' => 'FORD2022001',
                'placas' => 'ABC-123',
                'estatus' => EstadoVehiculo::DISPONIBLE,
                'kilometraje_actual' => 15000,
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Ciudad de México',
                'municipio' => 'Miguel Hidalgo',
                'observaciones' => 'Camioneta en excelentes condiciones, ideal para trabajos de construcción.',
            ],
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Chevrolet',
                'modelo' => 'Silverado',
                'anio' => 2021,
                'n_serie' => 'CHEV2021001',
                'placas' => 'DEF-456',
                'estatus' => EstadoVehiculo::ASIGNADO,
                'kilometraje_actual' => 28000,
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Estado de México',
                'municipio' => 'Naucalpan',
                'observaciones' => 'Vehículo asignado a proyecto de carreteras.',
            ],
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'anio' => 2023,
                'n_serie' => 'TOYO2023001',
                'placas' => 'GHI-789',
                'estatus' => EstadoVehiculo::DISPONIBLE,
                'kilometraje_actual' => 8500,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 100000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Jalisco',
                'municipio' => 'Guadalajara',
                'observaciones' => 'Pickup nueva, ideal para transporte de personal y herramientas.',
            ],
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Nissan',
                'modelo' => 'NP300',
                'anio' => 2020,
                'n_serie' => 'NISS2020001',
                'placas' => 'JKL-012',
                'estatus' => EstadoVehiculo::EN_MANTENIMIENTO,
                'kilometraje_actual' => 45000,
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 80000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Nuevo León',
                'municipio' => 'Monterrey',
                'observaciones' => 'En mantenimiento preventivo, cambio de aceite y filtros.',
            ],

            // Camiones
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Freightliner',
                'modelo' => 'Cascadia',
                'anio' => 2019,
                'n_serie' => 'FREI2019001',
                'placas' => 'MNO-345',
                'estatus' => EstadoVehiculo::ASIGNADO,
                'kilometraje_actual' => 125000,
                'intervalo_km_motor' => 25000,
                'intervalo_km_transmision' => 160000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Veracruz',
                'municipio' => 'Veracruz',
                'observaciones' => 'Camión de carga pesada para transporte de maquinaria.',
            ],
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Volvo',
                'modelo' => 'FH16',
                'anio' => 2021,
                'n_serie' => 'VOLV2021001',
                'placas' => 'PQR-678',
                'estatus' => EstadoVehiculo::DISPONIBLE,
                'kilometraje_actual' => 75000,
                'intervalo_km_motor' => 30000,
                'intervalo_km_transmision' => 200000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Tamaulipas',
                'municipio' => 'Tampico',
                'observaciones' => 'Camión de alta capacidad, excelente para largas distancias.',
            ],

            // Maquinaria
            [
                'tipo_activo_id' => $maquinariaTipo?->id,
                'marca' => 'Caterpillar',
                'modelo' => '320D',
                'anio' => 2022,
                'n_serie' => 'CAT2022001',
                'placas' => 'STU-901',
                'estatus' => EstadoVehiculo::ASIGNADO,
                'kilometraje_actual' => null, // Maquinaria no tiene kilometraje
                'intervalo_km_motor' => null,
                'intervalo_km_transmision' => null,
                'intervalo_km_hidraulico' => 2000, // Horas de operación para hidráulico
                'estado' => 'Sonora',
                'municipio' => 'Hermosillo',
                'observaciones' => 'Excavadora en perfectas condiciones, ideal para excavaciones profundas.',
            ],
            [
                'tipo_activo_id' => $maquinariaTipo?->id,
                'marca' => 'Komatsu',
                'modelo' => 'PC210',
                'anio' => 2020,
                'n_serie' => 'KOMA2020001',
                'placas' => 'VWX-234',
                'estatus' => EstadoVehiculo::FUERA_DE_SERVICIO,
                'kilometraje_actual' => null,
                'intervalo_km_motor' => null,
                'intervalo_km_transmision' => null,
                'intervalo_km_hidraulico' => 1500,
                'estado' => 'Chihuahua',
                'municipio' => 'Chihuahua',
                'observaciones' => 'Requiere reparación mayor en sistema hidráulico.',
            ],
            [
                'tipo_activo_id' => $maquinariaTipo?->id,
                'marca' => 'John Deere',
                'modelo' => '350G',
                'anio' => 2023,
                'n_serie' => 'DEER2023001',
                'placas' => 'YZA-567',
                'estatus' => EstadoVehiculo::DISPONIBLE,
                'kilometraje_actual' => null,
                'intervalo_km_motor' => null,
                'intervalo_km_transmision' => null,
                'intervalo_km_hidraulico' => 2500,
                'estado' => 'Coahuila',
                'municipio' => 'Saltillo',
                'observaciones' => 'Excavadora nueva, lista para proyectos de construcción.',
            ],

            // Vehículos adicionales con diferentes marcas
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Mercedes-Benz',
                'modelo' => 'Actros',
                'anio' => 2021,
                'n_serie' => 'MERC2021001',
                'placas' => 'BCD-890',
                'estatus' => EstadoVehiculo::ASIGNADO,
                'kilometraje_actual' => 98000,
                'intervalo_km_motor' => 40000,
                'intervalo_km_transmision' => 250000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Puebla',
                'municipio' => 'Puebla',
                'observaciones' => 'Camión de carga europea, excelente rendimiento de combustible.',
            ],
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'Isuzu',
                'modelo' => 'NPR',
                'anio' => 2022,
                'n_serie' => 'ISUZ2022001',
                'placas' => 'EFG-123',
                'estatus' => EstadoVehiculo::DISPONIBLE,
                'kilometraje_actual' => 22000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 120000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Guanajuato',
                'municipio' => 'León',
                'observaciones' => 'Camión mediano ideal para entregas y transporte de materiales.',
            ],
            [
                'tipo_activo_id' => $vehiculoTipo?->id,
                'marca' => 'RAM',
                'modelo' => '2500',
                'anio' => 2023,
                'n_serie' => 'RAM2023001',
                'placas' => 'HIJ-456',
                'estatus' => EstadoVehiculo::BAJA,
                'kilometraje_actual' => 180000,
                'intervalo_km_motor' => 8000,
                'intervalo_km_transmision' => 160000,
                'intervalo_km_hidraulico' => null,
                'estado' => 'Yucatán',
                'municipio' => 'Mérida',
                'observaciones' => 'Vehículo dado de baja por alto kilometraje y costos de mantenimiento.',
            ],
        ];

        // Crear los vehículos
        foreach ($vehiculos as $vehiculoData) {
            try {
                Vehiculo::create($vehiculoData);
                $this->command->info("Vehículo creado: {$vehiculoData['marca']} {$vehiculoData['modelo']} - {$vehiculoData['placas']}");
            } catch (\Exception $e) {
                $this->command->error("Error creando vehículo {$vehiculoData['marca']} {$vehiculoData['modelo']}: " . $e->getMessage());
            }
        }

        $this->command->info('Seeder de vehículos completado exitosamente.');
    }
}