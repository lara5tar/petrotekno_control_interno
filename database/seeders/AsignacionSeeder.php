<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AsignacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan las tablas relacionadas
        $vehiculos = \App\Models\Vehiculo::all();
        $obras = \App\Models\Obra::all();
        $personal = \App\Models\Personal::all();
        $usuarios = \App\Models\User::all();

        if ($vehiculos->isEmpty() || $obras->isEmpty() || $personal->isEmpty() || $usuarios->isEmpty()) {
            $this->command->warn('⚠️  Faltan datos en tablas relacionadas. Ejecuta primero los seeders de Vehiculos, Obras, Personal y Users.');

            return;
        }

        $this->command->info('🚀 Creando asignaciones...');

        // Crear asignaciones históricas (liberadas) - Estas no tienen conflictos
        $vehiculosUsados = collect();
        $personalUsado = collect();

        for ($i = 0; $i < 15; $i++) {
            $vehiculo = $vehiculos->random();
            $operador = $personal->random();

            \App\Models\Asignacion::factory()
                ->liberada()
                ->create([
                    'vehiculo_id' => $vehiculo->id,
                    'obra_id' => $obras->random()->id,
                    'personal_id' => $operador->id,
                    'creado_por_id' => $usuarios->random()->id,
                ]);
        }

        // Para asignaciones activas, usar vehículos y personal únicos
        $vehiculosDisponibles = $vehiculos->shuffle();
        $personalDisponible = $personal->shuffle();

        $maxAsignacionesActivas = min(5, $vehiculosDisponibles->count(), $personalDisponible->count());
        $this->command->info("📋 Creando {$maxAsignacionesActivas} asignaciones activas...");

        for ($i = 0; $i < $maxAsignacionesActivas; $i++) {
            try {
                \App\Models\Asignacion::create([
                    'vehiculo_id' => $vehiculosDisponibles[$i]->id,
                    'obra_id' => $obras->random()->id,
                    'personal_id' => $personalDisponible[$i]->id,
                    'creado_por_id' => $usuarios->random()->id,
                    'fecha_asignacion' => now()->subDays(rand(1, 30)),
                    'kilometraje_inicial' => rand(50000, 200000),
                    'observaciones' => 'Asignación activa creada por seeder - ' . $this->faker()->sentence(),
                ]);
                $this->command->info('✅ Asignación activa ' . ($i + 1) . ' creada');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  No se pudo crear asignación activa ' . ($i + 1) . ': ' . $e->getMessage());
            }
        }

        $totalAsignaciones = \App\Models\Asignacion::count();
        $asignacionesActivas = \App\Models\Asignacion::activas()->count();

        $this->command->info("✅ Se crearon {$totalAsignaciones} asignaciones en total");
        $this->command->info("📊 Asignaciones activas: {$asignacionesActivas}");
        $this->command->info('📊 Asignaciones liberadas: ' . ($totalAsignaciones - $asignacionesActivas));
    }

    private function faker()
    {
        return \Faker\Factory::create();
    }
}
