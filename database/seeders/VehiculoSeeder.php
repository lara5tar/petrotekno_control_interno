<?php

namespace Database\Seeders;

use App\Enums\EstadoVehiculo;
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
        // Obtener estatus predefinidos
        $estatusActivo = EstadoVehiculo::DISPONIBLE->value;
        $estatusAsignado = EstadoVehiculo::ASIGNADO->value;
        $estatusDisponible = EstadoVehiculo::DISPONIBLE->value;
        $estatusMantenimiento = EstadoVehiculo::EN_MANTENIMIENTO->value;

        // Crear vehículos de ejemplo con datos más completos y variados
        $vehiculos = [
            // Vehículos principales
            [
                'marca' => 'Toyota',
                'modelo' => 'Hilux 2.8 TDI',
                'anio' => 2023,
                'n_serie' => 'TOY2023HLX001',
                'placas' => 'PET-001',
                'estatus' => $estatusActivo,
                'kilometraje_actual' => 15000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => 30000,
                'observaciones' => 'Vehículo nuevo, excelente estado. Equipado con GPS y radio comunicación.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual', 'tarjeta_circulacion'],
            ],
            [
                'marca' => 'Ford',
                'modelo' => 'F-150 XLT SuperCrew',
                'anio' => 2022,
                'n_serie' => 'FOR2022F150002',
                'placas' => 'PET-002',
                'estatus' => $estatusAsignado,
                'kilometraje_actual' => 32000,
                'intervalo_km_motor' => 7500,
                'intervalo_km_transmision' => 80000,
                'intervalo_km_hidraulico' => 40000,
                'observaciones' => 'Asignado a obra de carretera principal. Mantenimiento al día.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual'],
            ],
            [
                'marca' => 'Chevrolet',
                'modelo' => 'Silverado 2500HD',
                'anio' => 2021,
                'n_serie' => 'CHV2021SLV003',
                'placas' => 'PET-003',
                'estatus' => $estatusDisponible,
                'kilometraje_actual' => 48000,
                'intervalo_km_motor' => 8000,
                'intervalo_km_transmision' => 75000,
                'intervalo_km_hidraulico' => 35000,
                'observaciones' => 'Vehículo de carga pesada. Próximo mantenimiento programado.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_vencida'],
            ],
            [
                'marca' => 'Nissan',
                'modelo' => 'NP300 Frontier',
                'anio' => 2020,
                'n_serie' => 'NIS2020NP3004',
                'placas' => 'PET-004',
                'estatus' => $estatusActivo,
                'kilometraje_actual' => 67000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => 30000,
                'observaciones' => 'Vehículo confiable para trabajos medianos. Recién serviciado.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual', 'tarjeta_circulacion'],
            ],

            // Vehículos especializados
            [
                'marca' => 'Caterpillar',
                'modelo' => 'Excavadora 320D',
                'anio' => 2019,
                'n_serie' => 'CAT2019320D005',
                'placas' => 'PET-005',
                'estatus' => $estatusAsignado,
                'kilometraje_actual' => 2800, // Horas de operación convertidas
                'intervalo_km_motor' => 500,
                'intervalo_km_transmision' => 2000,
                'intervalo_km_hidraulico' => 1000,
                'observaciones' => 'Excavadora para trabajos pesados. Asignada a obra de drenaje.',
                'documentos_adicionales' => ['seguro_vigente', 'certificado_operacion'],
            ],
            [
                'marca' => 'John Deere',
                'modelo' => 'Retroexcavadora 310L',
                'anio' => 2020,
                'n_serie' => 'JDE2020310L006',
                'placas' => 'PET-006',
                'estatus' => $estatusMantenimiento,
                'kilometraje_actual' => 1950,
                'intervalo_km_motor' => 500,
                'intervalo_km_transmision' => 2000,
                'intervalo_km_hidraulico' => 1000,
                'observaciones' => 'En mantenimiento preventivo. Cambio de filtros hidráulicos.',
                'documentos_adicionales' => ['seguro_vigente', 'orden_mantenimiento'],
            ],
            [
                'marca' => 'Volvo',
                'modelo' => 'Camión Volteo FMX',
                'anio' => 2021,
                'n_serie' => 'VOL2021FMX007',
                'placas' => 'PET-007',
                'estatus' => $estatusActivo,
                'kilometraje_actual' => 89000,
                'intervalo_km_motor' => 15000,
                'intervalo_km_transmision' => 100000,
                'intervalo_km_hidraulico' => 50000,
                'observaciones' => 'Camión volteo para transporte de materiales. Alto rendimiento.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual', 'permiso_carga'],
            ],

            // Vehículos administrativos
            [
                'marca' => 'Honda',
                'modelo' => 'CR-V EX',
                'anio' => 2022,
                'n_serie' => 'HON2022CRV008',
                'placas' => 'PET-008',
                'estatus' => $estatusDisponible,
                'kilometraje_actual' => 28000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 80000,
                'intervalo_km_hidraulico' => null,
                'observaciones' => 'Vehículo para supervisión y traslados administrativos.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual', 'tarjeta_circulacion'],
            ],
            [
                'marca' => 'Mazda',
                'modelo' => 'CX-5 Grand Touring',
                'anio' => 2021,
                'n_serie' => 'MAZ2021CX5009',
                'placas' => 'PET-009',
                'estatus' => $estatusAsignado,
                'kilometraje_actual' => 41000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 80000,
                'intervalo_km_hidraulico' => null,
                'observaciones' => 'Asignado a supervisión de obras. Aire acondicionado reparado.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual'],
            ],

            // Vehículos utilitarios
            [
                'marca' => 'Isuzu',
                'modelo' => 'NPR Caja Seca',
                'anio' => 2020,
                'n_serie' => 'ISU2020NPR010',
                'placas' => 'PET-010',
                'estatus' => $estatusActivo,
                'kilometraje_actual' => 76000,
                'intervalo_km_motor' => 12000,
                'intervalo_km_transmision' => 90000,
                'intervalo_km_hidraulico' => 45000,
                'observaciones' => 'Camión para transporte de herramientas y materiales ligeros.',
                'documentos_adicionales' => ['seguro_vigente', 'verificacion_actual', 'permiso_federal'],
            ],
            [
                'marca' => 'Mitsubishi',
                'modelo' => 'L200 Sportero',
                'anio' => 2019,
                'n_serie' => 'MIT2019L200011',
                'placas' => 'PET-011',
                'estatus' => $estatusMantenimiento,
                'kilometraje_actual' => 95000,
                'intervalo_km_motor' => 10000,
                'intervalo_km_transmision' => 60000,
                'intervalo_km_hidraulico' => 30000,
                'observaciones' => 'Mantenimiento mayor programado. Cambio de embrague.',
                'documentos_adicionales' => ['seguro_vigente', 'orden_reparacion'],
            ],
            [
                'marca' => 'Komatsu',
                'modelo' => 'Cargador Frontal WA200',
                'anio' => 2018,
                'n_serie' => 'KOM2018WA200012',
                'placas' => 'PET-012',
                'estatus' => $estatusDisponible,
                'kilometraje_actual' => 3200,
                'intervalo_km_motor' => 500,
                'intervalo_km_transmision' => 2000,
                'intervalo_km_hidraulico' => 1000,
                'observaciones' => 'Cargador frontal para movimiento de tierras. Recién inspeccionado.',
                'documentos_adicionales' => ['seguro_vigente', 'certificado_operacion', 'inspeccion_anual'],
            ],
            [
                'marca' => 'Chevrolet',
                'modelo' => 'Silverado',
                'anio' => 2020,
                'n_serie' => 'CHE2020001',
                'placas' => 'GHI-003',
                'estatus' => $estatusMantenimiento,
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
                'estatus' => $estatusDisponible,
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
                'estatus' => $estatusActivo,
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
                'estatus' => $estatusActivo,
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
                'estatus' => $estatusDisponible,
                'kilometraje_actual' => 55000,
                'intervalo_km_motor' => 5000,
                'intervalo_km_transmision' => 40000,
                'observaciones' => 'Disponible para nueva asignación',
            ],
        ];

        // Crear todos los vehículos
        foreach ($vehiculos as $vehiculoData) {
            Vehiculo::updateOrCreate(
                ['n_serie' => $vehiculoData['n_serie']],
                $vehiculoData
            );
        }

        // Mostrar estadísticas
        $this->command->info('✅ Vehículos creados exitosamente.');
        $this->command->info('🚗 Total vehículos: ' . Vehiculo::count());
        $this->command->info('🟢 Disponibles: ' . Vehiculo::where('estatus', EstadoVehiculo::DISPONIBLE->value)->count());
        $this->command->info('🟡 Asignados: ' . Vehiculo::where('estatus', EstadoVehiculo::ASIGNADO->value)->count());
        $this->command->info('🔴 En mantenimiento: ' . Vehiculo::where('estatus', EstadoVehiculo::EN_MANTENIMIENTO->value)->count());
        $this->command->info('🟠 Fuera de servicio: ' . Vehiculo::where('estatus', EstadoVehiculo::FUERA_DE_SERVICIO->value)->count());
    }
}
