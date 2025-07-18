<?php

namespace Database\Seeders;

use App\Models\Obra;
use Illuminate\Database\Seeder;

class ObraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obras con datos específicos y realistas
        $obras = [
            [
                'nombre_obra' => 'Construcción de Carretera Principal',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 65,
                'fecha_inicio' => '2024-03-15',
                'fecha_fin' => '2025-12-31',
            ],
            [
                'nombre_obra' => 'Renovación de Puente Vehicular',
                'estatus' => Obra::ESTATUS_COMPLETADA,
                'avance' => 100,
                'fecha_inicio' => '2023-08-01',
                'fecha_fin' => '2024-06-30',
            ],
            [
                'nombre_obra' => 'Instalación de Sistema de Drenaje',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => '2025-08-01',
                'fecha_fin' => '2026-02-28',
            ],
            [
                'nombre_obra' => 'Pavimentación Avenida Central',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 45,
                'fecha_inicio' => '2024-10-01',
                'fecha_fin' => '2025-05-31',
            ],
            [
                'nombre_obra' => 'Construcción de Planta de Tratamiento',
                'estatus' => Obra::ESTATUS_SUSPENDIDA,
                'avance' => 30,
                'fecha_inicio' => '2024-01-15',
                'fecha_fin' => '2025-09-30',
            ],
            [
                'nombre_obra' => 'Modernización de Sistema Eléctrico',
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'avance' => 80,
                'fecha_inicio' => '2024-06-01',
                'fecha_fin' => '2025-03-31',
            ],
            [
                'nombre_obra' => 'Expansión de Red de Gas',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'avance' => 0,
                'fecha_inicio' => '2025-09-01',
                'fecha_fin' => '2026-08-31',
            ],
            [
                'nombre_obra' => 'Reparación de Infraestructura Portuaria',
                'estatus' => Obra::ESTATUS_CANCELADA,
                'avance' => 15,
                'fecha_inicio' => '2024-02-01',
                'fecha_fin' => null,
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

        // Crear obras adicionales usando factory para testing
        Obra::factory()
            ->count(5)
            ->enProgreso()
            ->create();

        Obra::factory()
            ->count(3)
            ->completada()
            ->create();

        Obra::factory()
            ->count(2)
            ->planificada()
            ->create();

        Obra::factory()
            ->count(1)
            ->suspendida()
            ->create();

        Obra::factory()
            ->count(1)
            ->atrasada()
            ->create();

        $this->command->info('✅ Obras creadas exitosamente.');
        $this->command->info('📊 Total obras: '.Obra::count());
        $this->command->info('🚧 En progreso: '.Obra::porEstatus(Obra::ESTATUS_EN_PROGRESO)->count());
        $this->command->info('✅ Completadas: '.Obra::porEstatus(Obra::ESTATUS_COMPLETADA)->count());
        $this->command->info('📋 Planificadas: '.Obra::porEstatus(Obra::ESTATUS_PLANIFICADA)->count());
        $this->command->info('⏸️ Suspendidas: '.Obra::porEstatus(Obra::ESTATUS_SUSPENDIDA)->count());
        $this->command->info('❌ Canceladas: '.Obra::porEstatus(Obra::ESTATUS_CANCELADA)->count());
    }
}
