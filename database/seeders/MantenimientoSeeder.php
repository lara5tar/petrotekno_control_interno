<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mantenimiento;
use App\Models\Vehiculo;

class MantenimientoSeeder extends Seeder
{
    public function run()
    {
        // Obtener varios vehículos para diversificar los mantenimientos
        $vehiculos = Vehiculo::limit(5)->get();

        if ($vehiculos->count() === 0) {
            echo "❌ No hay vehículos disponibles\n";
            return;
        }

        // Proveedores de servicios ficticios
        $proveedores = [
            'Taller Mecánico Central',
            'Servicios Automotrices SA',
            'Llantera del Norte',
            'Autoservicio Express',
            'Mecánica Industrial López',
            'Taller Especializado Hernández',
            'Centro de Servicio Automotriz',
            'Reparaciones y Mantenimiento ABC'
        ];

        // Tipos de mantenimiento por sistema
        $mantenimientosPorSistema = [
            'motor' => [
                'Cambio de aceite y filtros',
                'Afinación mayor',
                'Reparación de motor',
                'Cambio de bujías',
                'Cambio de correa de distribución',
                'Limpieza de inyectores'
            ],
            'transmision' => [
                'Cambio de aceite de transmisión',
                'Reparación de caja de cambios',
                'Ajuste de embrague',
                'Cambio de banda de transmisión',
                'Reparación de diferencial'
            ],
            'hidraulico' => [
                'Reparación de frenos',
                'Cambio de pastillas de freno',
                'Cambio de aceite hidráulico',
                'Reparación de sistema de dirección',
                'Cambio de mangueras hidráulicas',
                'Mantenimiento de bomba hidráulica'
            ],
            'general' => [
                'Revisión general',
                'Cambio de llantas',
                'Alineación y balanceo',
                'Mantenimiento de aire acondicionado',
                'Cambio de filtro de aire',
                'Inspección eléctrica',
                'Cambio de batería',
                'Reparación de carrocería'
            ]
        ];

        $registrosCreados = 0;

        foreach ($vehiculos as $vehiculo) {
            // Crear entre 3 y 8 mantenimientos por vehículo
            $cantidadMantenimientos = rand(3, 8);
            
            for ($i = 0; $i < $cantidadMantenimientos; $i++) {
                $sistema = array_rand($mantenimientosPorSistema);
                $descripcion = $mantenimientosPorSistema[$sistema][array_rand($mantenimientosPorSistema[$sistema])];
                $proveedor = $proveedores[array_rand($proveedores)];
                $tipoServicio = rand(0, 1) ? 'PREVENTIVO' : 'CORRECTIVO';
                
                // Fechas realistas
                $diasAtras = rand(7, 365); // Entre 7 días y 1 año
                $fechaInicio = now()->subDays($diasAtras);
                $fechaFin = null;
                
                // 80% de probabilidad de que esté completado
                if (rand(1, 100) <= 80) {
                    $duracion = rand(1, 7); // Entre 1 y 7 días
                    $fechaFin = $fechaInicio->copy()->addDays($duracion);
                }
                
                // Costos realistas basados en el tipo de mantenimiento
                $costoBase = match($sistema) {
                    'motor' => rand(800, 5000),
                    'transmision' => rand(1500, 8000),
                    'hidraulico' => rand(500, 3500),
                    'general' => rand(300, 2500),
                };
                
                // Los correctivos cuestan más
                if ($tipoServicio === 'CORRECTIVO') {
                    $costoBase *= 1.5;
                }
                
                // Kilometraje realista
                $kilometraje = $vehiculo->kilometraje_actual ?? rand(10000, 80000);
                $kilometraje = $kilometraje - rand(1000, 10000); // Menor al actual
                
                try {
                    Mantenimiento::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo_servicio' => $tipoServicio,
                        'sistema_vehiculo' => $sistema,
                        'descripcion' => $descripcion,
                        'fecha_inicio' => $fechaInicio,
                        'fecha_fin' => $fechaFin,
                        'costo' => round($costoBase, 2),
                        'kilometraje_servicio' => max(1000, $kilometraje),
                        'proveedor' => $proveedor,
                    ]);
                    
                    $registrosCreados++;
                } catch (\Exception $e) {
                    echo "❌ Error creando mantenimiento: " . $e->getMessage() . "\n";
                }
            }
        }

        echo "✅ Se crearon {$registrosCreados} registros de mantenimiento para {$vehiculos->count()} vehículos\n";
        
        // Mostrar estadísticas
        $preventivos = Mantenimiento::where('tipo_servicio', 'PREVENTIVO')->count();
        $correctivos = Mantenimiento::where('tipo_servicio', 'CORRECTIVO')->count();
        $completados = Mantenimiento::whereNotNull('fecha_fin')->count();
        $enProceso = Mantenimiento::whereNull('fecha_fin')->count();
        
        echo "📊 Estadísticas:\n";
        echo "   - Preventivos: {$preventivos}\n";
        echo "   - Correctivos: {$correctivos}\n";
        echo "   - Completados: {$completados}\n";
        echo "   - En proceso: {$enProceso}\n";
    }
}
