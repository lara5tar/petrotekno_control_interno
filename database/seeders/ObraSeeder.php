<?php

namespace Database\Seeders;

use App\Models\Obra;
use App\Models\Vehiculo;
use App\Models\Personal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ObraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos relacionados
        $vehiculos = Vehiculo::all();
        $operadores = Personal::whereHas('categoria', function($q) {
            $q->where('nombre_categoria', 'Operador');
        })->where('estatus', 'activo')->get();
        $supervisores = Personal::whereHas('categoria', function($q) {
            $q->where('nombre_categoria', 'Supervisor');
        })->where('estatus', 'activo')->get();
        $adminUser = User::where('email', 'admin@petrotekno.com')->first();

        // Obras con datos específicos y realistas, incluyendo asignaciones
        $obras = [
            // Obras en progreso con asignaciones activas
            [
                'nombre_obra' => 'Construcción de Carretera Principal',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 65,
                'fecha_inicio' => '2024-03-15',
                'fecha_fin' => '2025-12-31',
                'vehiculo_id' => $vehiculos->where('placas', 'PET-002')->first()?->id, // Ford F-150
                'operador_id' => $operadores->first()?->id,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2024-03-15 08:00:00'),
                'kilometraje_inicial' => 30000,
                'combustible_inicial' => 45.5,
                'observaciones' => 'Obra principal con vehículo asignado para supervisión y transporte.',
            ],
            [
                'nombre_obra' => 'Renovación de Puente Vehicular',
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'avance' => 100,
                'fecha_inicio' => '2023-08-01',
                'fecha_fin' => '2024-06-30',
                'vehiculo_id' => null, // Obra completada, vehículo liberado
                'operador_id' => null,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2023-08-01 07:30:00'),
                'fecha_liberacion' => Carbon::parse('2024-06-30 17:00:00'),
                'kilometraje_inicial' => 25000,
                'kilometraje_final' => 45000,
                'combustible_inicial' => 50.0,
                'combustible_final' => 12.5,
                'combustible_suministrado' => 2500.0,
                'costo_combustible' => 62500.0,
                'observaciones' => 'Obra completada exitosamente. Vehículo liberado.',
            ],
            [
                'nombre_obra' => 'Instalación de Sistema de Drenaje',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 25,
                'fecha_inicio' => '2024-11-01',
                'fecha_fin' => '2025-08-31',
                'vehiculo_id' => $vehiculos->where('placas', 'PET-005')->first()?->id, // Excavadora
                'operador_id' => $operadores->skip(1)->first()?->id,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2024-11-01 06:00:00'),
                'kilometraje_inicial' => 2700,
                'combustible_inicial' => 180.0,
                'observaciones' => 'Excavadora asignada para trabajos de excavación del sistema de drenaje.',
            ],
            [
                'nombre_obra' => 'Pavimentación Avenida Central',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 45,
                'fecha_inicio' => '2024-10-01',
                'fecha_fin' => '2025-05-31',
                'vehiculo_id' => $vehiculos->where('placas', 'PET-009')->first()?->id, // Mazda CX-5
                'operador_id' => $supervisores->first()?->id,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2024-10-01 08:30:00'),
                'kilometraje_inicial' => 39000,
                'combustible_inicial' => 35.0,
                'observaciones' => 'Vehículo de supervisión asignado para control de calidad de pavimentación.',
            ],
            [
                'nombre_obra' => 'Construcción de Planta de Tratamiento',
                'estatus' => Obra::ESTATUS_SUSPENDIDA,
                'avance' => 30,
                'fecha_inicio' => '2024-01-15',
                'fecha_fin' => '2025-09-30',
                'vehiculo_id' => null, // Suspendida, vehículo liberado temporalmente
                'operador_id' => null,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2024-01-15 07:00:00'),
                'fecha_liberacion' => Carbon::parse('2024-08-15 16:00:00'),
                'kilometraje_inicial' => 15000,
                'kilometraje_final' => 28000,
                'combustible_inicial' => 40.0,
                'combustible_final' => 8.5,
                'combustible_suministrado' => 850.0,
                'costo_combustible' => 21250.0,
                'observaciones' => 'Obra suspendida por revisión de permisos. Vehículo liberado temporalmente.',
            ],
            [
                'nombre_obra' => 'Modernización de Sistema Eléctrico',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 80,
                'fecha_inicio' => '2024-06-01',
                'fecha_fin' => '2025-03-31',
                'vehiculo_id' => $vehiculos->where('placas', 'PET-010')->first()?->id, // Isuzu NPR
                'operador_id' => $operadores->skip(2)->first()?->id,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2024-06-01 07:00:00'),
                'kilometraje_inicial' => 72000,
                'combustible_inicial' => 85.0,
                'observaciones' => 'Camión utilitario para transporte de materiales eléctricos.',
            ],
            [
                'nombre_obra' => 'Expansión de Red de Gas',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => '2025-09-01',
                'fecha_fin' => '2026-08-31',
                'vehiculo_id' => null, // Planificada, sin asignación aún
                'operador_id' => null,
                'encargado_id' => $adminUser?->id,
                'observaciones' => 'Obra en planificación. Asignación de recursos pendiente.',
            ],
            [
                'nombre_obra' => 'Reparación de Infraestructura Portuaria',
                'estatus' => Obra::ESTATUS_CANCELADA,
                'avance' => 15,
                'fecha_inicio' => '2024-02-01',
                'fecha_fin' => null,
                'vehiculo_id' => null, // Cancelada, vehículo liberado
                'operador_id' => null,
                'encargado_id' => $adminUser?->id,
                'fecha_asignacion' => Carbon::parse('2024-02-01 08:00:00'),
                'fecha_liberacion' => Carbon::parse('2024-03-15 16:00:00'),
                'kilometraje_inicial' => 18000,
                'kilometraje_final' => 22000,
                'combustible_inicial' => 60.0,
                'combustible_final' => 25.0,
                'combustible_suministrado' => 300.0,
                'costo_combustible' => 7500.0,
                'observaciones' => 'Obra cancelada por cambios en el proyecto. Vehículo liberado.',
            ],
            [
                'nombre_obra' => 'Construcción de Terminal de Autobuses',
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'avance' => 100,
                'fecha_inicio' => '2022-11-01',
                'fecha_fin' => '2024-04-30',
            ],
            [
                'nombre_obra' => 'Instalación de Red de Fibra Óptica',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 55,
                'fecha_inicio' => '2024-07-01',
                'fecha_fin' => '2025-06-30',
            ],
        ];

        foreach ($obras as $obraData) {
            Obra::updateOrCreate(
                ['nombre_obra' => $obraData['nombre_obra']],
                $obraData
            );
        }

        // Crear algunas obras adicionales usando factory para testing
        Obra::factory()
            ->count(3)
            ->enProgreso()
            ->create();

        Obra::factory()
            ->count(2)
            ->completada()
            ->create();

        Obra::factory()
            ->count(2)
            ->planificada()
            ->create();

        // Mostrar estadísticas detalladas
        $this->command->info('✅ Obras creadas exitosamente.');
        $this->command->info('📊 Total obras: ' . Obra::count());
        $this->command->info('🚧 En progreso: ' . Obra::where('estatus', Obra::ESTATUS_EN_PROGRESO)->count());
        $this->command->info('✅ Completadas: ' . Obra::where('estatus', Obra::ESTATUS_COMPLETADA)->count());
        $this->command->info('📋 Planificadas: ' . Obra::where('estatus', Obra::ESTATUS_PLANIFICADA)->count());
        $this->command->info('⏸️ Suspendidas: ' . Obra::where('estatus', Obra::ESTATUS_SUSPENDIDA)->count());
        $this->command->info('❌ Canceladas: ' . Obra::where('estatus', Obra::ESTATUS_CANCELADA)->count());
        $this->command->info('🚗 Obras con vehículos asignados: ' . Obra::whereNotNull('vehiculo_id')->count());
        $this->command->info('👷 Obras con operadores asignados: ' . Obra::whereNotNull('operador_id')->count());
        $this->command->info('⛽ Obras con datos de combustible: ' . Obra::whereNotNull('combustible_inicial')->count());
    }
}
