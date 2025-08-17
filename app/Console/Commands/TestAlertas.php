<?php

namespace App\Console\Commands;

use App\Services\AlertasMantenimientoService;
use Illuminate\Console\Command;

class TestAlertas extends Command
{
    protected $signature = 'test:alertas';
    protected $description = 'Probar el servicio de alertas';

    public function handle()
    {
        $this->info('Probando servicio de alertas...');
        
        try {
            $resumenCompleto = AlertasMantenimientoService::obtenerResumenAlertas();
            $resumen = $resumenCompleto['resumen'] ?? [];
            
            $this->info('Resumen de alertas:');
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Total', $resumen['total_alertas'] ?? 0],
                    ['Vehículos afectados', $resumen['vehiculos_afectados'] ?? 0],
                    ['Críticas', $resumen['por_urgencia']['critica'] ?? 0],
                    ['Altas', $resumen['por_urgencia']['alta'] ?? 0],
                    ['Normales', $resumen['por_urgencia']['normal'] ?? 0]
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}
