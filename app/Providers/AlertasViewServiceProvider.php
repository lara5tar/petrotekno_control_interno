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
                    // Usar el mismo método que AlertasComposer para consistencia
                    $estadisticas = \App\Http\Controllers\MantenimientoAlertasController::getEstadisticasAlertas();
                    
                    $view->with([
                        'alertasCount' => $estadisticas['alertasCount'],
                        'tieneAlertasUrgentes' => $estadisticas['tieneAlertasUrgentes']
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
