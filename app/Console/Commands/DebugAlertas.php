<?php

namespace App\Console\Commands;

use App\Models\Vehiculo;
use App\Models\Mantenimiento;
use Illuminate\Console\Command;

class DebugAlertas extends Command
{
    protected $signature = 'debug:alertas {vehiculo_id?}';
    protected $description = 'Depurar alertas para un vehículo específico';

    public function handle()
    {
        $vehiculoId = $this->argument('vehiculo_id');
        
        if (!$vehiculoId) {
            // Mostrar vehículos disponibles
            $vehiculos = Vehiculo::whereIn('estatus', ['disponible', 'asignado'])
                ->get(['id', 'marca', 'modelo', 'placas', 'kilometraje_actual']);
                
            $this->info('Vehículos disponibles:');
            $filas = [];
            foreach ($vehiculos as $vehiculo) {
                $filas[] = [
                    $vehiculo->id,
                    $vehiculo->marca . ' ' . $vehiculo->modelo,
                    $vehiculo->placas,
                    number_format($vehiculo->kilometraje_actual)
                ];
            }
            $this->table(['ID', 'Vehículo', 'Placas', 'KM Actual'], $filas);
            
            $vehiculoId = $this->ask('Ingresa el ID del vehículo a depurar');
        }
        
        $vehiculo = Vehiculo::find($vehiculoId);
        if (!$vehiculo) {
            $this->error("Vehículo con ID {$vehiculoId} no encontrado.");
            return;
        }
        
        $this->info("Depurando vehículo: {$vehiculo->marca} {$vehiculo->modelo} ({$vehiculo->placas})");
        $this->info("Kilometraje actual: " . number_format($vehiculo->kilometraje_actual));
        $estatusTexto = $vehiculo->estatus instanceof \App\Enums\EstadoVehiculo 
            ? $vehiculo->estatus->value 
            : (string) $vehiculo->estatus;
        $this->info("Estatus: {$estatusTexto}");
        
        $sistemas = ['motor', 'transmision', 'hidraulico'];
        
        foreach ($sistemas as $sistema) {
            $this->info("\n--- Sistema: " . strtoupper($sistema) . " ---");
            
            $campo_intervalo = "intervalo_km_{$sistema}";
            $intervalo = $vehiculo->$campo_intervalo;
            
            $this->info("Intervalo configurado: " . ($intervalo ? number_format($intervalo) : 'No configurado'));
            
            if (!$intervalo || $intervalo <= 0) {
                $this->warn("Sistema omitido (sin intervalo configurado)");
                continue;
            }
            
            // Buscar último mantenimiento
            $ultimoMantenimiento = Mantenimiento::where('vehiculo_id', $vehiculo->id)
                ->where('sistema_vehiculo', $sistema)
                ->orderBy('kilometraje_servicio', 'desc')
                ->first();
                
            $kmBase = $ultimoMantenimiento?->kilometraje_servicio ?? 0;
            $proximoMantenimiento = $kmBase + $intervalo;
            
            $this->info("Último mantenimiento: " . ($ultimoMantenimiento ? 
                "KM {$ultimoMantenimiento->kilometraje_servicio} ({$ultimoMantenimiento->created_at->format('d/m/Y')})" : 
                'Ninguno'));
            $this->info("KM base: " . number_format($kmBase));
            $this->info("Próximo mantenimiento en: " . number_format($proximoMantenimiento));
            
            $requiereAlerta = $vehiculo->kilometraje_actual >= $proximoMantenimiento;
            $this->info("¿Requiere alerta? " . ($requiereAlerta ? 'SÍ' : 'NO'));
            
            if ($requiereAlerta) {
                $kmExceso = $vehiculo->kilometraje_actual - $proximoMantenimiento;
                $this->warn("KM de exceso: " . number_format($kmExceso));
            }
        }
    }
}
