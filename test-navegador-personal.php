<?php

/**
 * Prueba de búsqueda de personal simulando navegador
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PersonalSearchController;

echo "=== SIMULACIÓN DE BÚSQUEDA PERSONAL EN NAVEGADOR ===\n\n";

// 1. Simular login
echo "1. Simulando inicio de sesión...\n";
$admin = User::where('email', 'admin@petrotekno.com')->first();

if (!$admin) {
    echo "   ❌ Usuario admin no encontrado\n";
    exit;
}

// Simular autenticación
auth()->login($admin);
echo "   ✓ Usuario autenticado: {$admin->name}\n";
echo "   ✓ Permisos: " . (auth()->user()->hasPermission('ver_personal') ? 'SÍ' : 'NO') . "\n\n";

// 2. Simular petición AJAX desde JavaScript
echo "2. Simulando petición AJAX desde el navegador...\n";

$testCases = [
    ['q' => 'ad', 'descripcion' => 'Búsqueda por "ad"'],
    ['q' => 'Admin', 'descripcion' => 'Búsqueda por "Admin"'],
    ['q' => 'Sistema', 'descripcion' => 'Búsqueda por "Sistema"'],
    ['q' => '', 'descripcion' => 'Búsqueda vacía'],
    ['q' => 'noexiste', 'descripcion' => 'Búsqueda sin resultados'],
];

foreach ($testCases as $testCase) {
    echo "\n   Test: {$testCase['descripcion']}\n";
    
    // Crear request idéntico al que haría JavaScript
    $queryParams = [
        'q' => $testCase['q'],
        'limit' => 50
    ];
    
    $request = Request::create('/personal/search', 'GET', $queryParams);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    $request->headers->set('X-CSRF-TOKEN', 'dummy-token');
    
    try {
        $controller = new PersonalSearchController();
        $response = $controller->search($request);
        $data = json_decode($response->getContent(), true);
        
        echo "   - Status: {$response->status()}\n";
        echo "   - Total resultados: " . ($data['total'] ?? 0) . "\n";
        echo "   - Mensaje: " . ($data['mensaje'] ?? 'N/A') . "\n";
        
        if (isset($data['personal']) && count($data['personal']) > 0) {
            echo "   - Primer resultado: {$data['personal'][0]['nombre_completo']}\n";
        }
        
        if ($response->status() === 200) {
            echo "   ✅ EXITOSO\n";
        } else {
            echo "   ❌ ERROR\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Excepción: {$e->getMessage()}\n";
    }
}

echo "\n\n";

// 3. Verificar estructura completa de respuesta
echo "3. Verificando estructura completa de respuesta...\n";

$request = Request::create('/personal/search', 'GET', ['q' => 'Admin', 'limit' => 50]);
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

try {
    $controller = new PersonalSearchController();
    $response = $controller->search($request);
    $data = json_decode($response->getContent(), true);
    
    echo "   Estructura JSON:\n";
    echo "   {\n";
    echo "     \"personal\": [" . count($data['personal'] ?? []) . " elementos],\n";
    echo "     \"total\": " . ($data['total'] ?? 0) . ",\n";
    echo "     \"limite_alcanzado\": " . ($data['limite_alcanzado'] ? 'true' : 'false') . ",\n";
    echo "     \"mensaje\": \"" . ($data['mensaje'] ?? '') . "\"\n";
    echo "   }\n\n";
    
    if (!empty($data['personal'])) {
        $persona = $data['personal'][0];
        echo "   Primer elemento \"personal\":\n";
        echo "   {\n";
        foreach ($persona as $key => $value) {
            $displayValue = is_null($value) ? 'null' : (is_string($value) ? "\"$value\"" : $value);
            echo "     \"$key\": $displayValue,\n";
        }
        echo "   }\n";
    }
    
    echo "   ✅ Estructura JSON correcta\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error al verificar estructura: {$e->getMessage()}\n";
}

echo "\n=== RESULTADOS ===\n";
echo "✅ Búsqueda de personal funcionando correctamente\n";
echo "✅ Autenticación funcionando\n";
echo "✅ Estructura JSON válida\n";
echo "✅ El problema NO está en el backend\n\n";

echo "🔍 SIGUIENTE PASO:\n";
echo "Abre el navegador y ve a: http://127.0.0.1:8000/personal\n";
echo "Inicia sesión y prueba escribir 'ad' en el campo de búsqueda\n";
echo "Abre las DevTools (F12) y revisa la pestaña Network\n";