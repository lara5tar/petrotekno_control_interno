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
        
        // Si no existen los tipos, crearlos
        if (!$tipoVehiculo) {
            $tipoVehiculo = TipoActivo::create(['nombre' => 'Vehículo']);
        }
        if (!$tipoMaquinaria) {
            $tipoMaquinaria = TipoActivo::create(['nombre' => 'Maquinaria']);
        }
        
        // Crear vehículos Toyota
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Toyota',
            'Hilux',
            2024,
            'TOYHIL24XYZ123456',
            'ABC-123-XY',
            15000,
            5000,
            10000,
            15000,
            'Camioneta pick-up en excelente estado',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Toyota',
            'Corolla',
            2023,
            'TOYCOR23ABC789012',
            'DEF-456-AB',
            25000,
            5000,
            10000,
            15000,
            'Sedán económico para oficina',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Toyota',
            'Camry',
            2022,
            'TOYCAM22GHI345678',
            'GHI-789-CD',
            45000,
            5000,
            10000,
            15000,
            'Sedán ejecutivo en mantenimiento',
            EstadoVehiculo::EN_MANTENIMIENTO
        );
        
        // Crear vehículos Ford
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Ford',
            'F-150',
            2024,
            'FORDF1524DEF567890',
            'JKL-012-EF',
            8000,
            5000,
            10000,
            15000,
            'Pickup nueva para trabajos pesados',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Ford',
            'Focus',
            2023,
            'FORFOC23JKL901234',
            'MNO-345-GH',
            35000,
            5000,
            10000,
            15000,
            'Compacto fuera de servicio',
            EstadoVehiculo::FUERA_DE_SERVICIO
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Ford',
            'Explorer',
            2021,
            'FOREXP21MNO567890',
            'PQR-678-IJ',
            65000,
            5000,
            10000,
            15000,
            'SUV para supervisión',
            EstadoVehiculo::DISPONIBLE
        );
        
        // Crear vehículos Chevrolet
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Chevrolet',
            'Silverado',
            2024,
            'CHESIL24PQR123456',
            'STU-901-KL',
            12000,
            5000,
            10000,
            15000,
            'Pickup pesada nueva',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Chevrolet',
            'Aveo',
            2022,
            'CHEAVE22STU789012',
            'VWX-234-MN',
            38000,
            5000,
            10000,
            15000,
            'Compacto en mantenimiento preventivo',
            EstadoVehiculo::EN_MANTENIMIENTO
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Chevrolet',
            'Tahoe',
            2023,
            'CHETAH23VWX345678',
            'YZA-567-OP',
            22000,
            5000,
            10000,
            15000,
            'SUV grande para transporte',
            EstadoVehiculo::DISPONIBLE
        );
        
        // Crear vehículos Nissan
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Nissan',
            'Sentra',
            2023,
            'NISSEN23YZA901234',
            'BCD-890-QR',
            18000,
            5000,
            10000,
            15000,
            'Sedán compacto para ciudad',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Nissan',
            'Frontier',
            2024,
            'NISFRO24BCD567890',
            'EFG-123-ST',
            5000,
            5000,
            10000,
            15000,
            'Pickup nueva sin asignar',
            EstadoVehiculo::FUERA_DE_SERVICIO
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Nissan',
            'Altima',
            2021,
            'NISALT21EFG123456',
            'HIJ-456-UV',
            55000,
            5000,
            10000,
            15000,
            'Sedán con alto kilometraje',
            EstadoVehiculo::EN_MANTENIMIENTO
        );
        
        // Crear vehículos Honda
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Honda',
            'Civic',
            2023,
            'HONCIV23HIJ789012',
            'KLM-789-WX',
            28000,
            5000,
            10000,
            15000,
            'Compacto deportivo',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Honda',
            'CR-V',
            2024,
            'HONCRV24KLM345678',
            'NOP-012-YZ',
            9000,
            5000,
            10000,
            15000,
            'SUV compacta nueva',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Honda',
            'Accord',
            2022,
            'HONACC22NOP901234',
            'QRS-345-AB',
            42000,
            5000,
            10000,
            15000,
            'Sedán ejecutivo inactivo',
            EstadoVehiculo::FUERA_DE_SERVICIO
        );
        
        // Crear vehículos Volkswagen
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Volkswagen',
            'Jetta',
            2023,
            'VOLJET23QRS567890',
            'TUV-678-CD',
            31000,
            5000,
            10000,
            15000,
            'Sedán alemán confiable',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Volkswagen',
            'Tiguan',
            2024,
            'VOLTIG24TUV123456',
            'WXY-901-EF',
            7000,
            5000,
            10000,
            15000,
            'SUV en mantenimiento inicial',
            EstadoVehiculo::EN_MANTENIMIENTO
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Volkswagen',
            'Vento',
            2021,
            'VOLVEN21WXY789012',
            'ZAB-234-GH',
            48000,
            5000,
            10000,
            15000,
            'Sedán compacto operativo',
            EstadoVehiculo::DISPONIBLE
        );
        
        // Crear vehículos Hyundai
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Hyundai',
            'Elantra',
            2023,
            'HYUELA23ZAB345678',
            'CDE-567-IJ',
            21000,
            5000,
            10000,
            15000,
            'Sedán coreano eficiente',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Hyundai',
            'Tucson',
            2024,
            'HYUTUC24CDE901234',
            'FGH-890-KL',
            3000,
            5000,
            10000,
            15000,
            'SUV nueva sin asignar',
            EstadoVehiculo::FUERA_DE_SERVICIO
        );
        
        $this->crearVehiculo(
            $tipoVehiculo->id,
            'Hyundai',
            'Santa Fe',
            2022,
            'HYUSAN22FGH567890',
            'IJK-123-MN',
            39000,
            5000,
            10000,
            15000,
            'SUV grande en servicio',
            EstadoVehiculo::EN_MANTENIMIENTO
        );
        
        // Crear maquinaria pesada
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
            'Excavadora para trabajos pesados',
            EstadoVehiculo::DISPONIBLE
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
            'Tractor agrícola',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoMaquinaria->id,
            'Komatsu',
            'Bulldozer D65',
            2019,
            'KOMD6519MNO234567',
            'MAQ-003',
            null,
            null,
            null,
            null,
            'Bulldozer en mantenimiento mayor',
            EstadoVehiculo::EN_MANTENIMIENTO
        );
        
        $this->crearVehiculo(
            $tipoMaquinaria->id,
            'Volvo',
            'Cargador L120',
            2023,
            'VOLL12023PQR890123',
            'MAQ-004',
            null,
            null,
            null,
            null,
            'Cargador frontal nuevo',
            EstadoVehiculo::DISPONIBLE
        );
        
        $this->crearVehiculo(
            $tipoMaquinaria->id,
            'Liebherr',
            'Grúa LTM 1050',
            2020,
            'LIEBLTM20STU456789',
            'MAQ-005',
            null,
            null,
            null,
            null,
            'Grúa móvil fuera de servicio',
            EstadoVehiculo::FUERA_DE_SERVICIO
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
        $observaciones,
        $estado = null
    ) {
        echo "Creando vehículo: {$marca} {$modelo}\n";
        
        Vehiculo::create([
            'tipo_activo_id' => $tipoActivoId,
            'marca' => $marca,
            'modelo' => $modelo,
            'anio' => $anio,
            'n_serie' => $nSerie,
            'placas' => $placas,
            'estatus' => $estado ? $estado->value : EstadoVehiculo::DISPONIBLE->value,
            'kilometraje_actual' => $kilometrajeActual,
            'intervalo_km_motor' => $intervaloKmMotor,
            'intervalo_km_transmision' => $intervaloKmTransmision,
            'intervalo_km_hidraulico' => $intervaloKmHidraulico,
            'observaciones' => $observaciones,
        ]);
        
        echo "✅ Vehículo creado: {$marca} {$modelo}\n";
    }
}