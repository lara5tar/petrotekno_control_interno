<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mantenimiento;
use App\Models\Vehiculo;

class MantenimientoSeeder extends Seeder
{
    public function run()
    {
        $vehiculo = Vehiculo::first();

        if ($vehiculo) {
            // Crear mantenimientos de prueba
            Mantenimiento::create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'PREVENTIVO',
                'descripcion' => 'Cambio de aceite y filtros',
                'fecha_inicio' => now()->subDays(30),
                'fecha_fin' => now()->subDays(29),
                'costo' => 1500.00,
                'kilometraje_servicio' => 25000,
                'proveedor' => 'Taller Mecánico Central',
                'sistema_vehiculo' => 'motor'
            ]);
            
            Mantenimiento::create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'descripcion' => 'Reparación de frenos',
                'fecha_inicio' => now()->subDays(15),
                'fecha_fin' => now()->subDays(13),
                'costo' => 2800.00,
                'kilometraje_servicio' => 25500,
                'proveedor' => 'Servicios Automotrices SA',
                'sistema_vehiculo' => 'hidraulico'
            ]);
            
            Mantenimiento::create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'PREVENTIVO',
                'descripcion' => 'Revisión general y cambio de llantas',
                'fecha_inicio' => now()->subDays(7),
                'fecha_fin' => null, // En proceso
                'costo' => 3200.00,
                'kilometraje_servicio' => 26000,
                'proveedor' => 'Llantera del Norte',
                'sistema_vehiculo' => 'general'
            ]);

            echo "✅ Mantenimientos creados exitosamente\n";
        } else {
            echo "❌ No hay vehículos disponibles\n";
        }
    }
}
