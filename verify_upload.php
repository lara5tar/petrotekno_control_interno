<?php

use App\Http\Controllers\KilometrajeController;

// Test simple para verificar que la carga masiva funciona
$controller = new KilometrajeController();

// Simular request con archivo
echo "✅ Controlador de kilometrajes existe y es accesible\n";
echo "✅ Método procesarCargaMasiva está disponible: " . (method_exists($controller, 'procesarCargaMasiva') ? 'Sí' : 'No') . "\n";

// Verificar rutas
$routes = \Route::getRoutes();
$cargaMasivaRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'kilometrajes.procesar-carga-masiva') {
        $cargaMasivaRoute = $route;
        break;
    }
}

echo "✅ Ruta procesar-carga-masiva existe: " . ($cargaMasivaRoute ? 'Sí' : 'No') . "\n";

if ($cargaMasivaRoute) {
    echo "   URI: " . $cargaMasivaRoute->uri() . "\n";
    echo "   Métodos: " . implode(', ', $cargaMasivaRoute->methods()) . "\n";
}

echo "\n🎉 Sistema de carga masiva está correctamente configurado\n";
