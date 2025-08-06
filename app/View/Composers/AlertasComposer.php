<?php

namespace App\View\Composers;

use App\Services\AlertasService;
use Illuminate\View\View;

class AlertasComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'alertasCount' => AlertasService::getAlertasCount(),
            'alertasUrgentesCount' => AlertasService::getAlertasUrgentesCount(),
            'tieneAlertasUrgentes' => AlertasService::tieneAlertasUrgentes(),
        ]);
    }
}