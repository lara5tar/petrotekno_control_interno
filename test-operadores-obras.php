<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test de Operadores con Obras ===\n";

try {
    // Autenticar usuario
    $user = \App\Models\User::where('email', 'admin@test.com')->first();
    if (!$user) {
        throw new Exception("Usuario admin@test.com no encontrado");
    }
    \Illuminate\Support\Facades\Auth::login($user);
    echo "✅ Usuario autenticado\n";
    
    // Verificar rutas
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $rutasEncontradas = [];
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'operadores/obras-por-operador') !== false && strpos($uri, 'descargar') !== false) {
            $rutasEncontradas[] = $uri;
        }
    }
    echo "✅ Rutas de exportación encontradas: " . implode(', ', $rutasEncontradas) . "\n";
    
    // Crear request simulado
    $request = new \Illuminate\Http\Request();
    echo "✅ Request creado\n";
    
    // Crear controlador
    $controller = new \App\Http\Controllers\OperadorObraController();
    echo "✅ Controlador creado\n";
    
    // Verificar que el trait está disponible
    $traits = class_uses($controller);
    if (in_array('App\Traits\PdfGeneratorTrait', $traits)) {
        echo "✅ PdfGeneratorTrait está disponible\n";
    } else {
        echo "❌ PdfGeneratorTrait NO está disponible\n";
    }
    
    // Verificar métodos
    if (method_exists($controller, 'descargarReportePdf')) {
        echo "✅ Método descargarReportePdf existe\n";
    } else {
        echo "❌ Método descargarReportePdf NO existe\n";
    }
    
    if (method_exists($controller, 'descargarReporteExcel')) {
        echo "✅ Método descargarReporteExcel existe\n";
    } else {
        echo "❌ Método descargarReporteExcel NO existe\n";
    }
    
    // Verificar que la clase Export existe
    if (class_exists('App\Exports\OperadoresObrasFiltradosExport')) {
        echo "✅ Clase OperadoresObrasFiltradosExport existe\n";
    } else {
        echo "❌ Clase OperadoresObrasFiltradosExport NO existe\n";
    }
    
    // Probar método index con filtros
    $request->merge(['buscar' => 'test', 'estado' => 'activo']);
    $response = $controller->index($request);
    echo "✅ Método index con filtros funciona\n";
    
    echo "✅ Test completado exitosamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test completado ===\n";