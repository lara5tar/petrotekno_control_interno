<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Personal;
use App\Models\Vehiculo;
use App\Models\Kilometraje;
use App\Models\Mantenimiento;
use App\Models\Obra;
use App\Models\AsignacionObra;
use Carbon\Carbon;

class CreateTestDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info("🚀 Creando datos de prueba para alertas...");

        // Crear personal de prueba
        $operador1 = Personal::create([
            'nombre_completo' => 'Juan Carlos Pérez González',
            'estatus' => 'activo',
            'categoria_id' => 1 // Asumiendo que existe
        ]);

        $operador2 = Personal::create([
            'nombre_completo' => 'María Elena Rodriguez Silva',
            'estatus' => 'activo',
            'categoria_id' => 1
        ]);

        // Crear obra de prueba
        $obra = Obra::firstOrCreate(
            ['nombre_obra' => 'Proyecto Demo Alertas'],
            [
                'ubicacion' => 'Lima, Perú',
                'estatus' => 'en_progreso',
                'fecha_inicio' => Carbon::now()->subMonths(2),
                'fecha_fin' => Carbon::now()->addMonths(6),
                'avance' => 45.5
            ]
        );

        // Crear vehículos de prueba
        $vehiculo1 = Vehiculo::firstOrCreate(
            ['placas' => 'ABC123'],
            [
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'anio' => 2020,
                'n_serie' => 'TOY2020ABC123',
                'kilometraje_actual' => 45000,
                'estatus' => 'disponible',
                'poliza_vencimiento' => Carbon::now()->addDays(15),
                'derecho_vencimiento' => Carbon::now()->subDays(5),
            ]
        );

        $vehiculo2 = Vehiculo::firstOrCreate(
            ['placas' => 'DEF456'],
            [
                'marca' => 'Nissan',
                'modelo' => 'Frontier',
                'anio' => 2019,
                'n_serie' => 'NIS2019DEF456',
                'kilometraje_actual' => 38000,
                'estatus' => 'asignado',
                'poliza_vencimiento' => Carbon::now()->subDays(10),
                'derecho_vencimiento' => Carbon::now()->addDays(30),
            ]
        );

        $vehiculo3 = Vehiculo::firstOrCreate(
            ['placas' => 'GHI789'],
            [
                'marca' => 'Ford',
                'modelo' => 'Ranger',
                'anio' => 2021,
                'n_serie' => 'FOR2021GHI789',
                'kilometraje_actual' => 52000,
                'estatus' => 'disponible',
                'poliza_vencimiento' => Carbon::now()->addMonths(3),
                'derecho_vencimiento' => Carbon::now()->addMonths(2),
            ]
        );

        // Crear asignaciones de obra (solo si no existen)
        if (!AsignacionObra::where('vehiculo_id', $vehiculo1->id)->where('estado', 'activa')->exists()) {
            AsignacionObra::create([
                'vehiculo_id' => $vehiculo1->id,
                'obra_id' => $obra->id,
                'operador_id' => $operador1->id,
                'fecha_asignacion' => Carbon::now()->subDays(30),
                'estado' => 'activa'
            ]);
        }

        if (!AsignacionObra::where('vehiculo_id', $vehiculo2->id)->where('estado', 'activa')->exists()) {
            AsignacionObra::create([
                'vehiculo_id' => $vehiculo2->id,
                'obra_id' => $obra->id,
                'operador_id' => $operador2->id,
                'fecha_asignacion' => Carbon::now()->subDays(20),
                'estado' => 'activa'
            ]);
        }

        // Crear kilometrajes
        Kilometraje::firstOrCreate([
            'vehiculo_id' => $vehiculo1->id,
            'kilometraje' => 45000,
        ], [
            'fecha_captura' => Carbon::now(),
            'observaciones' => 'Kilometraje actual'
        ]);

        Kilometraje::firstOrCreate([
            'vehiculo_id' => $vehiculo2->id,
            'kilometraje' => 38000,
        ], [
            'fecha_captura' => Carbon::now()->subDays(3),
            'observaciones' => 'Kilometraje actualizado'
        ]);

        // Crear mantenimientos que generen alertas
        Mantenimiento::firstOrCreate([
            'vehiculo_id' => $vehiculo1->id,
            'descripcion' => 'Cambio de aceite',
        ], [
            'tipo_servicio' => 'Preventivo',
            'sistema_vehiculo' => 'motor',
            'fecha_inicio' => Carbon::now()->subDays(5),
            'fecha_fin' => Carbon::now()->subDays(5),
            'kilometraje_servicio' => 40000,
            'costo' => 150.00,
            'proveedor' => 'Taller Central'
        ]);

        Mantenimiento::firstOrCreate([
            'vehiculo_id' => $vehiculo1->id,
            'descripcion' => 'Revisión de frenos',
        ], [
            'tipo_servicio' => 'Preventivo',
            'sistema_vehiculo' => 'general',
            'fecha_inicio' => Carbon::now()->addDays(5),
            'fecha_fin' => Carbon::now()->addDays(5),
            'kilometraje_servicio' => 45000,
            'costo' => 300.00,
            'proveedor' => 'Taller Especializado'
        ]);

        Mantenimiento::firstOrCreate([
            'vehiculo_id' => $vehiculo2->id,
            'descripcion' => 'Reparación de motor',
        ], [
            'tipo_servicio' => 'Correctivo',
            'sistema_vehiculo' => 'motor',
            'fecha_inicio' => Carbon::now()->subDays(2),
            'fecha_fin' => Carbon::now()->subDays(2),
            'kilometraje_servicio' => 38000,
            'costo' => 800.00,
            'proveedor' => 'Mecánica Premium'
        ]);

        Mantenimiento::firstOrCreate([
            'vehiculo_id' => $vehiculo3->id,
            'descripcion' => 'Cambio de filtros',
        ], [
            'tipo_servicio' => 'Preventivo',
            'sistema_vehiculo' => 'general',
            'fecha_inicio' => Carbon::now()->addDays(15),
            'fecha_fin' => Carbon::now()->addDays(15),
            'kilometraje_servicio' => 52000,
            'costo' => 120.00,
            'proveedor' => 'AutoServicios'
        ]);

        $this->command->info("✅ Datos de prueba creados exitosamente:");
        $this->command->info("   📋 3 vehículos con diferentes estados de documentos");
        $this->command->info("   👥 2 operadores asignados");
        $this->command->info("   🏗️ 1 obra activa");
        $this->command->info("   🔧 4 mantenimientos (2 vencidos, 2 próximos)");
        $this->command->info("   📊 Esto debería generar varias alertas visibles");
    }
}
