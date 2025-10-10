<?php

// Test de operadores obras con campos corregidos
require_once __DIR__ . '/vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== Test de Operadores con Obras (Campos Corregidos) ===\n\n";
    
    // 1. Verificar estructura de tabla personal
    echo "1. Verificando campos de tabla personal:\n";
    $campos = Illuminate\Support\Facades\Schema::getColumnListing('personal');
    foreach($campos as $campo) {
        echo "   - $campo\n";
    }
    echo "\n";
    
    // 2. Test de filtros básicos
    echo "2. Test de filtros con campos reales:\n";
    
    // Probar búsqueda por nombre completo
    $operadores = \App\Models\Personal::whereHas('historialOperadorVehiculo', function ($subquery) {
        $subquery->whereNotNull('obra_id');
    })
    ->where('nombre_completo', 'like', '%a%')
    ->limit(3)
    ->get();
    
    echo "   Operadores encontrados por nombre: " . $operadores->count() . "\n";
    
    // Probar filtro por estatus
    $operadoresActivos = \App\Models\Personal::whereHas('historialOperadorVehiculo', function ($subquery) {
        $subquery->whereNotNull('obra_id');
    })
    ->where('estatus', 'activo')
    ->limit(3)
    ->get();
    
    echo "   Operadores activos: " . $operadoresActivos->count() . "\n";
    
    // 3. Test de rutas
    echo "\n3. Verificando rutas de operadores obras:\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $operadoresRoutes = [];
    
    foreach($routes as $route) {
        $uri = $route->uri();
        if(str_contains($uri, 'operadores/obras-por-operador')) {
            $operadoresRoutes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $uri,
                'name' => $route->getName()
            ];
        }
    }
    
    foreach($operadoresRoutes as $route) {
        echo "   {$route['method']} {$route['uri']} -> {$route['name']}\n";
    }
    
    // 4. Test de estadísticas
    echo "\n4. Test de estadísticas con campos corregidos:\n";
    
    $operadoresConObras = \App\Models\Personal::whereHas('historialOperadorVehiculo', function ($subquery) {
        $subquery->whereNotNull('obra_id');
    })
    ->withCount(['historialOperadorVehiculo as total_asignaciones_obra' => function ($subquery) {
        $subquery->whereNotNull('obra_id');
    }])
    ->get();
    
    $estadisticas = [
        'total_operadores' => $operadoresConObras->count(),
        'total_asignaciones' => $operadoresConObras->sum('total_asignaciones_obra'),
        'operadores_activos' => $operadoresConObras->where('estatus', 'activo')->count(),
    ];
    
    echo "   Total operadores: {$estadisticas['total_operadores']}\n";
    echo "   Total asignaciones: {$estadisticas['total_asignaciones']}\n";
    echo "   Operadores activos: {$estadisticas['operadores_activos']}\n";
    
    // 5. Test de opciones para filtros
    echo "\n5. Test de opciones para filtros:\n";
    
    $estadosOptions = \App\Models\Personal::select('estatus')->distinct()->pluck('estatus', 'estatus');
    echo "   Estados disponibles: " . implode(', ', $estadosOptions->toArray()) . "\n";
    
    $obrasOptions = \App\Models\Obra::select('id', 'nombre_obra')->orderBy('nombre_obra')->limit(5)->get();
    echo "   Obras disponibles (primeras 5): " . $obrasOptions->pluck('nombre_obra')->implode(', ') . "\n";
    
    echo "\n✅ Todos los tests pasaron correctamente!\n";
    echo "Los campos de base de datos han sido corregidos.\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error en el test: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}