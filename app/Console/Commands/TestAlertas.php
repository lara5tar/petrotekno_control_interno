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
            $resultadoCompleto = AlertasMantenimientoService::verificarTodosLosVehiculos();
            $alertas = $resultadoCompleto['alertas'] ?? [];
            $resumen = $resultadoCompleto['resumen'] ?? [];
            
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

            if (!empty($alertas)) {
                $this->info("\nDetalles de alertas:");
                $filas = [];
                foreach ($alertas as $index => $alerta) {
                    $filas[] = [
                        $index + 1,
                        $alerta['vehiculo_info']['nombre_completo'] ?? 'N/A',
                        number_format($alerta['kilometraje_actual'] ?? 0),
                        $alerta['sistema_mantenimiento']['nombre_sistema'] ?? 'N/A',
                        number_format($alerta['intervalo_alcanzado']['intervalo_configurado'] ?? 0),
                        number_format($alerta['intervalo_alcanzado']['km_exceso'] ?? 0),
                        $alerta['urgencia'] ?? 'N/A'
                    ];
                }
                
                $this->table(
                    ['#', 'Vehículo', 'KM Actual', 'Sistema', 'Intervalo', 'KM Exceso', 'Urgencia'],
                    $filas
                );
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}
