<?php

namespace Database\Seeders;

use App\Enums\EstadoVehiculo;
use App\Models\TipoActivo;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class VehiculosTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoVehiculo = TipoActivo::where('nombre', 'Vehículo')->first();
        $tipoMaquinaria = TipoActivo::where('nombre', 'Maquinaria')->first();
        
        // Crear vehículos de tipo "Vehículo"
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Toyota',
            'Hilux',
            2022,
            'TOYHIL22XYZ123456',
            'ABC-123-XY',
            15000,
            5000,
            10000,
            15000,
            'Camioneta pick-up en excelente estado'
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Nissan',
            'NP300',
            2021,
            'NISNP321ABC789012',
            'XYZ-456-AB',
            22000,
            5000,
            10000,
            15000,
            'Camioneta de trabajo'
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Ford',
            'Ranger',
            2023,
            'FORDRAN23DEF345678',
            'DEF-789-CD',
            8000,
            5000,
            10000,
            15000,
            'Vehículo nuevo para supervisión'
        );
        
        // Crear vehículos de tipo "Maquinaria"
        $this->crearVehiculo(
            $tipoMaquinaria->id,
            'Caterpillar',
            'Excavadora 320',
            2020,
            'CAT320EXC20GHI567890',
            'MAQ-001',
            null,
            null,
            null,
            null,
            'Excavadora para trabajos pesados'
        );
        
        $this->crearVehiculo(
            $tipoMaquinaria->id,
            'John Deere',
            'Tractor 6120M',
            2021,
            'JD6120M21JKL901234',
            'MAQ-002',
            null,
            null,
            null,
            null,
            'Tractor agrícola'
        );
    }
    
    /**
     * Crear un vehículo con los datos proporcionados
     */
    private function crearVehiculo(
        $tipoActivoId,
        $marca,
        $modelo,
        $anio,
        $nSerie,
        $placas,
        $kilometrajeActual,
        $intervaloKmMotor,
        $intervaloKmTransmision,
        $intervaloKmHidraulico,
        $observaciones
    ) {
        echo "Creando vehículo: {$marca} {$modelo}\n";
        
        Vehiculo::create([
            'tipo_activo_id' => $tipoActivoId,
            'marca' => $marca,
            'modelo' => $modelo,
            'anio' => $anio,
            'n_serie' => $nSerie,
            'placas' => $placas,
            'estatus' => EstadoVehiculo::DISPONIBLE->value,
            'kilometraje_actual' => $kilometrajeActual,
            'intervalo_km_motor' => $intervaloKmMotor,
            'intervalo_km_transmision' => $intervaloKmTransmision,
            'intervalo_km_hidraulico' => $intervaloKmHidraulico,
            'observaciones' => $observaciones,
        ]);
        
        echo "✅ Vehículo creado: {$marca} {$modelo}\n";
    }
}