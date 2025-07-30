<?php

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Crear una request simulada
$request = Illuminate\Http\Request::create('/personal', 'POST', [
    'nombre_completo' => 'Juan Pérez Test',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'crear_usuario' => false,
    'no_identificacion' => 'TEST123456',
]);

// Simular archivo
$uploadedFile = new Illuminate\Http\UploadedFile(
    __DIR__ . '/test_file.pdf', // Archivo temporal
    'identificacion.pdf',
    'application/pdf',
    null,
    true // test mode
);

// Agregar archivo a la request
$request->files->set('identificacion_file', $uploadedFile);

echo "=== TESTING PERSONAL CREATION WITH DOCUMENTS ===\n";
echo "Request data: " . json_encode($request->all()) . "\n";
echo "Files: " . json_encode($request->files->all()) . "\n";

try {
    // Crear el FormRequest manualmente
    $formRequest = new App\Http\Requests\CreatePersonalRequest();
    $formRequest->setContainer($app);
    $formRequest->setRedirector($app->make('redirect'));
    
    // Reemplazar la request actual
    $formRequest->replace($request->all());
    $formRequest->files = $request->files;
    
    echo "\n=== VALIDATION ===\n";
    $validator = $formRequest->getValidatorInstance();
    
    if ($validator->fails()) {
        echo "Validation failed:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "- $error\n";
        }
    } else {
        echo "Validation passed!\n";
        
        // Probar el controlador
        echo "\n=== CONTROLLER TEST ===\n";
        $controller = new App\Http\Controllers\PersonalManagementController();
        
        // Verificar si el archivo existe
        echo "File exists: " . ($formRequest->hasFile('identificacion_file') ? 'YES' : 'NO') . "\n";
        echo "File valid: " . ($formRequest->file('identificacion_file')->isValid() ? 'YES' : 'NO') . "\n";
        
        // Intentar procesar
        $result = $controller->storeWeb($formRequest);
        echo "Controller result: " . get_class($result) . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";