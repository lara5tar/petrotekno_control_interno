<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\PersonalManagementController;
use App\Http\Requests\CreatePersonalRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== SIMULACIÓN DE NAVEGADOR ===\n";

// Crear archivos temporales reales
$tempFiles = [];
$documentTypes = [
    'identificacion_file' => 'identificacion.pdf',
    'curp_file' => 'curp.pdf',
    'rfc_file' => 'rfc.pdf',
    'nss_file' => 'nss.pdf',
    'licencia_file' => 'licencia.pdf',
    'comprobante_file' => 'comprobante.pdf',
    'cv_file' => 'cv.pdf'
];

foreach ($documentTypes as $fieldName => $fileName) {
    $tempPath = sys_get_temp_dir() . '/' . $fileName;
    file_put_contents($tempPath, "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n174\n%%EOF");
    $tempFiles[$fieldName] = $tempPath;
    echo "Archivo creado: $tempPath (" . filesize($tempPath) . " bytes)\n";
}

// Simular datos del formulario exactamente como los enviaría el navegador
$formData = [
    '_token' => csrf_token(),
    'nombre_completo' => 'Test Browser Simulation',
    'estatus' => 'activo',
    'categoria_personal_id' => '1', // Asumiendo que existe la categoría 1
    'crear_usuario' => '1',
    'email_usuario' => 'test.browser@example.com',
    'password_type' => 'random',
    'no_identificacion' => 'BROWSER123456789',
    'curp_numero' => 'BRWS031105MTSRRNA2',
    'rfc' => 'BRWS031105ABC',
    'nss' => '12345678901',
    'no_licencia' => 'LIC123456',
    'comprobante' => 'Calle Test 123, Colonia Test',
    'cv' => 'Curriculum Vitae Browser Test'
];

// Crear UploadedFile objects como lo haría Laravel
$uploadedFiles = [];
foreach ($tempFiles as $fieldName => $tempPath) {
    $uploadedFiles[$fieldName] = new UploadedFile(
        $tempPath,
        basename($tempPath),
        'application/pdf',
        null,
        true // test mode
    );
    echo "UploadedFile creado para $fieldName: " . $uploadedFiles[$fieldName]->getClientOriginalName() . "\n";
}

echo "\n=== SIMULANDO REQUEST DEL NAVEGADOR ===\n";

// Crear request exactamente como lo haría el navegador
$request = Request::create('/personal', 'POST', $formData, [], $uploadedFiles, [
    'CONTENT_TYPE' => 'multipart/form-data',
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
]);

echo "Request creado con método: " . $request->method() . "\n";
echo "Files en request: " . count($request->allFiles()) . "\n";

// Verificar cada archivo
foreach ($uploadedFiles as $fieldName => $file) {
    echo "$fieldName: " . ($request->hasFile($fieldName) ? 'PRESENTE' : 'AUSENTE') . "\n";
    if ($request->hasFile($fieldName)) {
        $uploadedFile = $request->file($fieldName);
        echo "  - Nombre: " . $uploadedFile->getClientOriginalName() . "\n";
        echo "  - Tipo MIME: " . $uploadedFile->getMimeType() . "\n";
        echo "  - Tamaño: " . $uploadedFile->getSize() . " bytes\n";
        echo "  - Es válido: " . ($uploadedFile->isValid() ? 'SÍ' : 'NO') . "\n";
    }
}

echo "\n=== VALIDANDO CON CreatePersonalRequest ===\n";

// Crear una instancia de CreatePersonalRequest
$formRequest = new CreatePersonalRequest();

// Obtener las reglas de validación
$rules = $formRequest->rules();
echo "Reglas de validación obtenidas: " . count($rules) . " reglas\n";

// Validar manualmente
$validator = Validator::make($request->all(), $rules, $formRequest->messages());

if ($validator->fails()) {
    echo "ERRORES DE VALIDACIÓN:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "VALIDACIÓN EXITOSA\n";
    
    echo "\n=== PROBANDO DIRECTAMENTE EL CONTROLLER ===\n";
    
    try {
        // Usar reflexión para probar el controller directamente
        $controller = new PersonalManagementController();
        $validatedData = $validator->validated();
        
        // Crear request con archivos
        $testRequest = Request::create('/personal', 'POST', $validatedData, [], $uploadedFiles);
        
        // Usar reflexión para llamar al método privado si es necesario
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('storeWeb');
        
        // Crear un FormRequest simulado
        $formRequest = new CreatePersonalRequest();
        $formRequest->replace($validatedData);
        $formRequest->files->replace($uploadedFiles);
        
        echo "Intentando ejecutar storeWeb...\n";
        
        // Llamar directamente al método con datos validados
        $response = $controller->storeWeb($formRequest);
        
        echo "Controller ejecutado exitosamente\n";
        echo "Tipo de respuesta: " . get_class($response) . "\n";
        
        if (method_exists($response, 'getTargetUrl')) {
            echo "Redirección a: " . $response->getTargetUrl() . "\n";
        }
        
    } catch (Exception $e) {
        echo "ERROR EN CONTROLLER: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

// Limpiar archivos temporales
foreach ($tempFiles as $tempPath) {
    if (file_exists($tempPath)) {
        unlink($tempPath);
    }
}

echo "\n=== SIMULACIÓN COMPLETADA ===\n";