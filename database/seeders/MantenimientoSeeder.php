<?php

namespace Database\Seeders;

use App\Models\Mantenimiento;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MantenimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener vehículos
        $vehiculos = Vehiculo::all();

        // Mantenimientos con datos realistas
        $mantenimientos = [
            // Mantenimientos preventivos
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-001')->first()?->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => 'Taller Mecánico Central',
                'descripcion' => 'Cambio de aceite de motor y filtros',
                'fecha_inicio' => Carbon::now()->subDays(5),
                'fecha_fin' => Carbon::now()->subDays(4),
                'kilometraje_servicio' => 25000,
                'costo' => 2500.00,
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-003')->first()?->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'transmision',
                'proveedor' => 'Servicio Especializado RAM',
                'descripcion' => 'Servicio mayor: cambio de aceite de transmisión',
                'fecha_inicio' => Carbon::now()->subDays(15),
                'fecha_fin' => Carbon::now()->subDays(13),
                'kilometraje_servicio' => 75000,
                'costo' => 8500.00,
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-004')->first()?->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'general',
                'proveedor' => 'Taller Automotriz Norte',
                'descripcion' => 'Inspección general y cambio de filtros',
                'fecha_inicio' => Carbon::now()->subDays(10),
                'fecha_fin' => Carbon::now()->subDays(9),
                'kilometraje_servicio' => 67000,
                'costo' => 3450.00,
            ],

            // Mantenimientos correctivos
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-006')->first()?->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'hidraulico',
                'proveedor' => 'Hidráulicos Especializados SA',
                'descripcion' => 'Reparación de sistema hidráulico - fuga en cilindro',
                'fecha_inicio' => Carbon::now()->subDays(20),
                'fecha_fin' => Carbon::now()->subDays(17),
                'kilometraje_servicio' => 1950,
                'costo' => 18500.00,
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-011')->first()?->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'transmision',
                'proveedor' => 'Transmisiones Diesel Pro',
                'descripcion' => 'Cambio de embrague y volante',
                'fecha_inicio' => Carbon::now()->subDays(25),
                'fecha_fin' => Carbon::now()->subDays(22),
                'kilometraje_servicio' => 95000,
                'costo' => 27800.00,
            ],

            // Mantenimientos de maquinaria pesada
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-005')->first()?->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'hidraulico',
                'proveedor' => 'Caterpillar Service Center',
                'descripcion' => 'Cambio de aceite hidráulico y filtros - Excavadora',
                'fecha_inicio' => Carbon::now()->subDays(30),
                'fecha_fin' => Carbon::now()->subDays(28),
                'kilometraje_servicio' => 3000,
                'costo' => 8000.00,
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-012')->first()?->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'general',
                'proveedor' => 'Maquinaria Pesada del Norte',
                'descripcion' => 'Inspección anual y certificación - Cargador Frontal',
                'fecha_inicio' => Carbon::now()->subDays(35),
                'fecha_fin' => Carbon::now()->subDays(33),
                'kilometraje_servicio' => 3200,
                'costo' => 5200.00,
            ],

            // Mantenimientos recientes
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-007')->first()?->id,
                'tipo_servicio' => 'PREVENTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => 'Taller Mecánico Central',
                'descripcion' => 'Servicio de 100,000 km - revisión completa',
                'fecha_inicio' => Carbon::now()->subDays(3),
                'fecha_fin' => Carbon::now()->subDays(1),
                'kilometraje_servicio' => 100000,
                'costo' => 12000.00,
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-008')->first()?->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'general',
                'proveedor' => 'Aire Acondicionado Automotriz',
                'descripcion' => 'Reparación de aire acondicionado',
                'fecha_inicio' => Carbon::now()->subDays(7),
                'fecha_fin' => Carbon::now()->subDays(6),
                'kilometraje_servicio' => 28000,
                'costo' => 4500.00,
            ],
        ];

        foreach ($mantenimientos as $mantenimientoData) {
            Mantenimiento::create($mantenimientoData);
        }

        // Crear algunos mantenimientos adicionales usando factory
        Mantenimiento::factory(8)->create();

        // Mostrar estadísticas
        $this->command->info('✅ Mantenimientos creados exitosamente.');
        $this->command->info('🔧 Total mantenimientos: ' . Mantenimiento::count());
        $this->command->info('🛠️ Preventivos: ' . Mantenimiento::where('tipo_servicio', 'PREVENTIVO')->count());
        $this->command->info('🚨 Correctivos: ' . Mantenimiento::where('tipo_servicio', 'CORRECTIVO')->count());
        $this->command->info('⚙️ Sistema motor: ' . Mantenimiento::where('sistema_vehiculo', 'motor')->count());
        $this->command->info('🔄 Sistema transmisión: ' . Mantenimiento::where('sistema_vehiculo', 'transmision')->count());
        $this->command->info('💧 Sistema hidráulico: ' . Mantenimiento::where('sistema_vehiculo', 'hidraulico')->count());
        $this->command->info('💰 Costo total: $' . number_format(Mantenimiento::sum('costo'), 2));
    }
}