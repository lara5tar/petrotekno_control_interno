<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Models\TipoActivo;
use Illuminate\Database\Seeder;

class VehiculosCompletoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚗 Creando vehículos completos...');
        
        $tiposActivo = TipoActivo::all();
        if ($tiposActivo->isEmpty()) {
            $this->command->error('No hay tipos de activo.');
            return;
        }

        $tipoTransporte = $tiposActivo->where('tiene_kilometraje', true)->first();
        $tipoSinKm = $tiposActivo->where('tiene_kilometraje', false)->first();

        $vehiculos = [
            ['marca' => 'Ford', 'modelo' => 'F-150 XLT', 'anio' => 2022, 'n_serie' => '1FTEW1EP5NKE12345', 'placas' => 'ABC123D', 'tipo_activo_id' => $tipoTransporte?->id ?? 1, 'estatus' => 'disponible', 'kilometraje_actual' => 15230],
            ['marca' => 'Nissan', 'modelo' => 'NP300', 'anio' => 2023, 'n_serie' => '3N6AD33A8NK901234', 'placas' => 'GHI789F', 'tipo_activo_id' => $tipoTransporte?->id ?? 1, 'estatus' => 'disponible', 'kilometraje_actual' => 8450],
            ['marca' => 'Chevrolet', 'modelo' => 'Silverado', 'anio' => 2020, 'n_serie' => '1GC4K0E85LF123456', 'placas' => 'JKL012G', 'tipo_activo_id' => $tipoTransporte?->id ?? 1, 'estatus' => 'en_mantenimiento', 'kilometraje_actual' => 67340],
            ['marca' => 'Toyota', 'modelo' => 'Hilux', 'anio' => 2019, 'n_serie' => 'AHTFR22G500123456', 'placas' => 'VWX234K', 'tipo_activo_id' => $tipoTransporte?->id ?? 1, 'estatus' => 'fuera_de_servicio', 'kilometraje_actual' => 145680],
            ['marca' => 'Caterpillar', 'modelo' => 'Generador XQ230', 'anio' => 2022, 'n_serie' => 'CAT230XQ789456', 'placas' => 'SPLACAS01', 'tipo_activo_id' => $tipoSinKm?->id ?? 1, 'estatus' => 'disponible', 'kilometraje_actual' => null],
            ['marca' => 'BOMAG', 'modelo' => 'Compactador BW211', 'anio' => 2020, 'n_serie' => 'BOMAG211D567890', 'placas' => 'SPLACAS03', 'tipo_activo_id' => $tipoSinKm?->id ?? 1, 'estatus' => 'disponible', 'kilometraje_actual' => null],
        ];

        $created = 0;
        foreach ($vehiculos as $vehiculoData) {
            try {
                Vehiculo::create($vehiculoData);
                $created++;
            } catch (\Exception $e) {
                $this->command->warn("Error: " . $e->getMessage());
            }
        }
        
        $this->command->info("✅ Se crearon {$created} vehículos.");
    }
}
