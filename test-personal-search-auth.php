<?php

/**
 * Script de prueba para búsqueda de personal con autenticación simulada
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PersonalSearchController;

echo "=== TEST DE BÚSQUEDA DE PERSONAL CON AUTENTICACIÓN ===\n\n";

// 1. Verificar usuario administrador
echo "1. Verificando usuario administrador...\n";
$admin = User::where('email', 'admin@petrotekno.com')->first();

if (!$admin) {
    echo "   ❌ No se encontró usuario administrador\n";
    echo "   Creando usuario admin de prueba...\n";
    
    $admin = User::create([
        'name' => 'Administrador',
        'email' => 'admin@petrotekno.com',
        'password' => bcrypt('password'),
        'rol_id' => 1
    ]);
    echo "   ✓ Usuario admin creado\n";
} else {
    echo "   ✓ Usuario admin encontrado: {$admin->name} ({$admin->email})\n";
}
echo "\n";

// 2. Simular autenticación
echo "2. Simulando autenticación...\n";
auth()->login($admin);
echo "   ✓ Usuario autenticado: " . auth()->user()->name . "\n";
echo "\n";

// 3. Verificar permisos
echo "3. Verificando permisos...\n";
$hasPermission = auth()->user()->hasPermission('ver_personal');
echo "   Permiso 'ver_personal': " . ($hasPermission ? '✓ SÍ' : '❌ NO') . "\n";
echo "\n";

// 4. Probar búsqueda directa
echo "4. Probando búsqueda con diferentes parámetros...\n";

$testCases = [
    ['q' => 'Admin', 'descripcion' => 'Búsqueda con parámetro "q"'],
    ['buscar' => 'Admin', 'descripcion' => 'Búsqueda con parámetro "buscar"'],
    ['q' => '', 'descripcion' => 'Búsqueda vacía'],
];

foreach ($testCases as $testCase) {
    echo "\n   Test: {$testCase['descripcion']}\n";
    
    // Crear request simulado
    $request = Request::create('/personal/search', 'GET', $testCase);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
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
        
        echo "   ✓ Búsqueda exitosa\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
    }
}

echo "\n\n";

// 5. Probar búsqueda sin autenticación
echo "5. Probando búsqueda SIN autenticación...\n";
auth()->logout();

$request = Request::create('/personal/search', 'GET', ['q' => 'Admin']);
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

try {
    $controller = new PersonalSearchController();
    $response = $controller->search($request);
    $data = json_decode($response->getContent(), true);
    
    echo "   - Status: {$response->status()}\n";
    echo "   - Mensaje: " . ($data['mensaje'] ?? 'N/A') . "\n";
    
    if ($response->status() === 403) {
        echo "   ✓ Correctamente bloqueado sin autenticación\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n";
}

echo "\n";
echo "=== TESTS COMPLETADOS ===\n";
