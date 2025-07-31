<?php

namespace Database\Seeders;

use App\Models\Kilometraje;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Obra;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KilometrajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener vehículos, usuarios y obras
        $vehiculos = Vehiculo::all();
        $usuarios = User::all();
        $obras = Obra::all();

        // Verificar que existan datos necesarios
        if ($vehiculos->isEmpty() || $usuarios->isEmpty() || $obras->isEmpty()) {
            $this->command->warn('⚠️ No hay suficientes datos para crear registros de kilometraje.');
            return;
        }

        // Usar el primer usuario como fallback
        $usuarioDefault = $usuarios->first();

        // Registros de kilometraje con datos realistas
        $registrosKilometraje = [
            // Registros para Ford F-150 (PET-001)
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-001')->first()?->id,
                'kilometraje' => 22680,
                'fecha_captura' => Carbon::now()->subDays(30),
                'usuario_captura_id' => $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Recorrido a obra Construcción de Puente - Zona Norte',
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-001')->first()?->id,
                'kilometraje' => 22850,
                'fecha_captura' => Carbon::now()->subDays(25),
                'usuario_captura_id' => $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Carga de combustible en estación Pemex Centro',
            ],

            // Registros para Chevrolet Silverado (PET-002)
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-002')->first()?->id,
                'kilometraje' => 45420,
                'fecha_captura' => Carbon::now()->subDays(28),
                'usuario_captura_id' => $usuarios->skip(1)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->skip(1)->first()?->id,
                'observaciones' => 'Transporte de materiales a obra de carretera',
            ],

            // Registros para Excavadora CAT 320D (PET-005)
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-005')->first()?->id,
                'kilometraje' => 2858,
                'fecha_captura' => Carbon::now()->subDays(20),
                'usuario_captura_id' => $usuarios->skip(2)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Excavación para cimentación - 8 horas de operación',
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-005')->first()?->id,
                'kilometraje' => 2866,
                'fecha_captura' => Carbon::now()->subDays(19),
                'usuario_captura_id' => $usuarios->skip(2)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Carga de combustible diésel para maquinaria pesada',
            ],

            // Registros para Grúa Hidráulica (PET-006)
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-006')->first()?->id,
                'kilometraje' => 1926,
                'fecha_captura' => Carbon::now()->subDays(15),
                'usuario_captura_id' => $usuarios->skip(3)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Instalación de vigas prefabricadas - 6 horas',
            ],

            // Registros para Nissan Sentra (PET-009) - Vehículo administrativo
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-009')->first()?->id,
                'kilometraje' => 15285,
                'fecha_captura' => Carbon::now()->subDays(12),
                'usuario_captura_id' => $usuarios->skip(4)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->skip(2)->first()?->id,
                'observaciones' => 'Visita a proveedores y gestiones administrativas',
            ],

            // Registros para Cargador Frontal (PET-012)
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-012')->first()?->id,
                'kilometraje' => 3185,
                'fecha_captura' => Carbon::now()->subDays(10),
                'usuario_captura_id' => $usuarioDefault->id,
                'obra_id' => $obras->skip(1)->first()?->id,
                'observaciones' => 'Carga y movimiento de materiales - 5 horas',
            ],

            // Registros más recientes
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-003')->first()?->id,
                'kilometraje' => 68050,
                'fecha_captura' => Carbon::now()->subDays(5),
                'usuario_captura_id' => $usuarios->skip(1)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->skip(2)->first()?->id,
                'observaciones' => 'Transporte de equipo especializado',
            ],
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-004')->first()?->id,
                'kilometraje' => 67180,
                'fecha_captura' => Carbon::now()->subDays(3),
                'usuario_captura_id' => $usuarios->skip(2)->first()?->id ?? $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Supervisión de múltiples obras en progreso',
            ],

            // Registro de hoy
            [
                'vehiculo_id' => $vehiculos->where('placas', 'PET-001')->first()?->id,
                'kilometraje' => 22920,
                'fecha_captura' => Carbon::now(),
                'usuario_captura_id' => $usuarioDefault->id,
                'obra_id' => $obras->first()?->id,
                'observaciones' => 'Recorrido de inspección matutina',
            ],
        ];

        foreach ($registrosKilometraje as $registro) {
            Kilometraje::create($registro);
        }

        // Crear algunos registros adicionales usando factory
        Kilometraje::factory(10)->create();

        // Mostrar estadísticas
        $this->command->info('✅ Registros de kilometraje creados exitosamente.');
        $this->command->info('📊 Total registros: ' . Kilometraje::count());
        $this->command->info('🚗 Vehículos con registros: ' . Kilometraje::distinct('vehiculo_id')->count());
        $this->command->info('👨‍💼 Usuarios que capturaron: ' . Kilometraje::distinct('usuario_captura_id')->count());
        $this->command->info('🏗️ Obras con registros: ' . Kilometraje::distinct('obra_id')->count());
    }
}