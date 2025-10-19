<?php

namespace App\Console\Commands;

use App\Services\AlertasMantenimientoService;
use Illuminate\Console\Command;

class ShowAlertStructure extends Command
{
    protected $signature = 'show:alert-structure';
    protected $description = 'Mostrar estructura de alertas';

    public function handle()
    {
        $resultado = AlertasMantenimientoService::verificarTodosLosVehiculos();
        $alertas = $resultado['alertas'] ?? [];
        
        if (!empty($alertas)) {
            $this->info('Estructura de la primera alerta:');
            $this->line(json_encode($alertas[0], JSON_PRETTY_PRINT));
        } else {
            $this->warn('No hay alertas disponibles');
        }
    }
}
