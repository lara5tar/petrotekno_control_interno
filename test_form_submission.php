<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PersonalManagementController;
use App\Http\Requests\CreatePersonalRequest;
use App\Models\CategoriaPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

echo "=== PRUEBA DE FORMULARIO WEB ===\n";

// 1. Simular datos del formulario
$formData = [
    'nombre_completo' => 'María González Test',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'ine' => '9876543210987654',
    'curp_numero' => 'GOMA850215MDFNRR05',
    'rfc' => 'GOMA850215XYZ',
    'nss' => '98765432109',
    'no_licencia' => 'LIC987654',
    'direccion' => 'Avenida Principal 456, Ciudad Test'
];

echo "\n--- DATOS DEL FORMULARIO ---\n";
foreach ($formData as $key => $value) {
    echo "{$key}: {$value}\n";
}

// 2. Probar validación
echo "\n--- PROBANDO VALIDACIÓN ---\n";

try {
    $request = new CreatePersonalRequest();
    $rules = $request->rules();
    
    echo "Reglas de validación encontradas: " . count($rules) . "\n";
    
    $validator = Validator::make($formData, $rules);
    
    if ($validator->fails()) {
        echo "❌ ERRORES DE VALIDACIÓN:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - {$error}\n";
        }
    } else {
        echo "✅ Validación exitosa\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error en validación: {$e->getMessage()}\n";
}

// 3. Probar el controlador directamente
echo "\n--- PROBANDO CONTROLADOR ---\n";

try {
    // Crear una instancia del request con los datos
    $request = Request::create('/personal', 'POST', $formData);
    
    // Simular autenticación (necesaria para el controlador)
    $user = \App\Models\User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        echo "✓ Usuario autenticado: {$user->email}\n";
    } else {
        echo "⚠️ No hay usuarios en la base de datos\n";
    }
    
    // Verificar que el método existe
    $controller = new PersonalManagementController();
    if (method_exists($controller, 'storeWeb')) {
        echo "✓ Método storeWeb encontrado\n";
    } else {
        echo "❌ Método storeWeb NO encontrado\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error en controlador: {$e->getMessage()}\n";
}

// 4. Verificar configuración de archivos
echo "\n--- CONFIGURACIÓN DE ARCHIVOS ---\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Habilitado' : 'Deshabilitado') . "\n";

// 5. Verificar ruta
echo "\n--- VERIFICANDO RUTA ---\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$personalStoreRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'personal.store') {
        $personalStoreRoute = $route;
        break;
    }
}

if ($personalStoreRoute) {
    echo "✓ Ruta 'personal.store' encontrada\n";
    echo "  URI: {$personalStoreRoute->uri()}\n";
    echo "  Métodos: " . implode(', ', $personalStoreRoute->methods()) . "\n";
    echo "  Acción: {$personalStoreRoute->getActionName()}\n";
} else {
    echo "❌ Ruta 'personal.store' NO encontrada\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";