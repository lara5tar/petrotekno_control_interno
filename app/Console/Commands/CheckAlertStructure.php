<?php

namespace App\Console\Commands;

use App\Services\AlertasMantenimientoService;
use Illuminate\Console\Command;

class CheckAlertStructure extends Command
{
    protected $signature = 'check:alert-structure';
    protected $description = 'Revisar estructura completa de alertas';

    public function handle()
    {
        $resultado = AlertasMantenimientoService::verificarTodosLosVehiculos();
        $alertas = $resultado['alertas'] ?? [];
        
        if (!empty($alertas)) {
            $this->info('Estructura completa de la primera alerta:');
            $this->line(json_encode($alertas[0], JSON_PRETTY_PRINT));
            
            $this->info("\nEstructura de historial_mantenimientos:");
            $this->line(json_encode($alertas[0]['historial_mantenimientos'], JSON_PRETTY_PRINT));
        } else {
            $this->warn('No hay alertas disponibles');
        }
    }
}
