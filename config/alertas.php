<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Alertas del Sistema
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar los umbrales y parámetros para el sistema de 
    | alertas de mantenimiento y documentos de vehículos.
    |
    */

    /**
     * Umbral de kilómetros para considerar una alerta de mantenimiento como "Próximo"
     * Si faltan menos kilómetros que este umbral, se considera "Próximo a vencer"
     */
    'mantenimiento_km_umbral' => 1000,

    /**
     * Umbral de días para considerar un documento como "Próximo a vencer"
     * Si faltan menos días que este umbral, se considera "Próximo a vencer"
     */
    'vencimiento_documentos_dias' => 30,

    /**
     * Configuración de colores para las alertas
     */
    'colores' => [
        'vencido' => [
            'badge' => 'bg-red-100 text-red-800 border-red-200',
            'dot' => 'bg-red-500'
        ],
        'proximo' => [
            'badge' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'dot' => 'bg-yellow-500'
        ],
        'ok' => [
            'badge' => 'bg-green-100 text-green-800 border-green-200',
            'dot' => 'bg-green-500'
        ],
        'sin_fecha' => [
            'badge' => 'bg-gray-100 text-gray-800 border-gray-200',
            'dot' => 'bg-gray-500'
        ]
    ]
];
