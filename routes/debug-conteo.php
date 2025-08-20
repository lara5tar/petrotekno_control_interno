<?php

use App\Http\Controllers\MantenimientoAlertasController;

Route::get('/debug-conteo', function () {
    $controller = new MantenimientoAlertasController();
    
    // Obtener datos del método unificada
    $vista = $controller->unificada();
    $datosVista = $vista->getData();
    $alertasUnificadas = $datosVista['alertasUnificadas'];
    $estadisticasVista = $datosVista['estadisticas'];
    
    // Obtener datos del método estático
    $estadisticasEstatico = MantenimientoAlertasController::getEstadisticasAlertas();
    
    return response()->json([
        'metodo_unificada' => [
            'total_alertas_array' => count($alertasUnificadas),
            'estadisticas' => $estadisticasVista,
            'alertas_detalle' => collect($alertasUnificadas)->map(function($alerta) {
                return [
                    'tipo' => $alerta['tipo'],
                    'estado' => $alerta['estado'],
                    'vehiculo' => $alerta['vehiculo_info']['placas'] ?? 'N/A',
                    'descripcion' => substr($alerta['descripcion'], 0, 50) . '...'
                ];
            })
        ],
        'metodo_estatico' => $estadisticasEstatico,
        'diferencia' => [
            'vista_total' => $estadisticasVista['total'],
            'estatico_total' => $estadisticasEstatico['alertasCount'],
            'diferencia' => $estadisticasEstatico['alertasCount'] - $estadisticasVista['total']
        ]
    ], JSON_PRETTY_PRINT);
});
