<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Http\Controllers\MantenimientoAlertasController;

class AlertasComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        try {
            $estadisticas = MantenimientoAlertasController::getEstadisticasAlertas();
            
            $view->with($estadisticas);
            
        } catch (\Exception $e) {
            // En caso de error, no mostrar alertas
            $view->with([
                'alertasCount' => 0,
                'tieneAlertasUrgentes' => false
            ]);
        }
    }
}