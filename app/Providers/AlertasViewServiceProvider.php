<?php

namespace App\Providers;

use App\Services\AlertasMantenimientoService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AlertasViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartir datos de alertas solo con el layout principal
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                try {
                    $resumenCompleto = AlertasMantenimientoService::obtenerResumenAlertas();
                    $resumen = $resumenCompleto['resumen'] ?? [];
                    
                    $totalAlertas = $resumen['total_alertas'] ?? 0;
                    $alertasCriticas = $resumen['por_urgencia']['critica'] ?? 0;
                    
                    $view->with([
                        'alertasCount' => $totalAlertas,
                        'tieneAlertasUrgentes' => $alertasCriticas > 0
                    ]);
                } catch (\Exception $e) {
                    // En caso de error, no mostrar alertas
                    $view->with([
                        'alertasCount' => 0,
                        'tieneAlertasUrgentes' => false
                    ]);
                }
            } else {
                $view->with([
                    'alertasCount' => 0,
                    'tieneAlertasUrgentes' => false
                ]);
            }
        });
    }
}
