<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Obra;
use App\Models\Vehiculo;
use App\Models\AsignacionObra;
use Carbon\Carbon;

class CreateTestData extends Command
{
    protected $signature = 'test:create-data';
    protected $description = 'Crear datos de prueba para el sistema';

    public function handle()
    {
        $this->info('Creando datos de prueba...');
        
        try {
            // Crear obras de prueba
            $this->info('Creando obras...');
            $obra1 = Obra::create([
                'nombre_obra' => 'Construcción de Carretera Norte',
                'estatus' => 'en_progreso',
                'avance' => 45,
                'fecha_inicio' => Carbon::now()->subDays(30),
                'fecha_fin' => Carbon::now()->addDays(60),
            ]);

            $obra2 = Obra::create([
                'nombre_obra' => 'Puente Río Azul',
                'estatus' => 'planificada', 
                'avance' => 10,
                'fecha_inicio' => Carbon::now()->addDays(15),
                'fecha_fin' => Carbon::now()->addDays(120),
            ]);

            $obra3 = Obra::create([
                'nombre_obra' => 'Reparación de Túnel Central',
                'estatus' => 'completada',
                'avance' => 100,
                'fecha_inicio' => Carbon::now()->subDays(90),
                'fecha_fin' => Carbon::now()->subDays(5),
            ]);

            // Crear vehículos de prueba
            $this->info('Creando vehículos...');
            $vehiculo1 = Vehiculo::create([
                'marca' => 'Caterpillar',
                'modelo' => '320D',
                'anio' => 2020,
                'placas' => 'CAT-001',
                'n_serie' => 'CAT320D001',
                'estatus' => 'disponible',
                'kilometraje_actual' => 15000,
            ]);

            $vehiculo2 = Vehiculo::create([
                'marca' => 'Volvo',
                'modelo' => 'EC210B',
                'anio' => 2019,
                'placas' => 'VOL-002', 
                'n_serie' => 'VOLEC210B002',
                'estatus' => 'asignado',
                'kilometraje_actual' => 22000,
            ]);

            $vehiculo3 = Vehiculo::create([
                'marca' => 'Komatsu',
                'modelo' => 'PC200',
                'anio' => 2021,
                'placas' => 'KOM-003',
                'n_serie' => 'KOMPC200003',
                'estatus' => 'disponible',
                'kilometraje_actual' => 8500,
            ]);

            // Crear asignaciones de prueba
            $this->info('Creando asignaciones...');
            AsignacionObra::create([
                'obra_id' => $obra1->id,
                'vehiculo_id' => $vehiculo1->id,
                'fecha_asignacion' => Carbon::now()->subDays(25),
                'kilometraje_inicial' => 12000,
                'estado' => 'activa',
                'observaciones' => 'Asignación activa para excavación',
            ]);

            AsignacionObra::create([
                'obra_id' => $obra3->id,
                'vehiculo_id' => $vehiculo2->id,
                'fecha_asignacion' => Carbon::now()->subDays(85),
                'fecha_liberacion' => Carbon::now()->subDays(10),
                'kilometraje_inicial' => 20000,
                'kilometraje_final' => 22000,
                'estado' => 'liberada',
                'observaciones' => 'Trabajo completado satisfactoriamente',
            ]);

            AsignacionObra::create([
                'obra_id' => $obra1->id,
                'vehiculo_id' => $vehiculo3->id,
                'fecha_asignacion' => Carbon::now()->subDays(15),
                'kilometraje_inicial' => 8000,
                'estado' => 'activa',
                'observaciones' => 'Apoyo en actividades de carga',
            ]);

            AsignacionObra::create([
                'obra_id' => $obra2->id,
                'vehiculo_id' => $vehiculo1->id,
                'fecha_asignacion' => Carbon::now()->subDays(45),
                'fecha_liberacion' => Carbon::now()->subDays(30),
                'kilometraje_inicial' => 10000,
                'kilometraje_final' => 12000,
                'estado' => 'transferida',
                'observaciones' => 'Transferido a otra obra por necesidades del proyecto',
            ]);

            $this->info('✅ Datos de prueba creados exitosamente:');
            $this->info('- 3 obras');
            $this->info('- 3 vehículos');
            $this->info('- 4 asignaciones');
            
        } catch (\Exception $e) {
            $this->error('❌ Error al crear datos de prueba:');
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
