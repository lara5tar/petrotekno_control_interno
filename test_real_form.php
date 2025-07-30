<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PersonalManagementController;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

echo "=== PRUEBA DIRECTA DEL CONTROLADOR ===\n";

// 1. Crear archivos temporales reales
echo "\n--- CREANDO ARCHIVOS TEMPORALES ---\n";

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
    file_put_contents($tempPath, '%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000058 00000 n \n0000000115 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n174\n%%EOF');
    $tempFiles[$fieldName] = $tempPath;
    echo "✓ Creado: {$fileName} (" . filesize($tempPath) . " bytes)\n";
}

try {
    // 2. Crear UploadedFile objects reales
    echo "\n--- CREANDO UPLOADED FILES ---\n";
    $uploadedFiles = [];
    foreach ($tempFiles as $fieldName => $tempPath) {
        $uploadedFiles[$fieldName] = new UploadedFile(
            $tempPath,
            basename($tempPath),
            'application/pdf',
            null,
            true // test mode
        );
        echo "✓ UploadedFile para {$fieldName}: {$uploadedFiles[$fieldName]->getClientOriginalName()}\n";
        echo "  - MIME: {$uploadedFiles[$fieldName]->getMimeType()}\n";
        echo "  - Tamaño: {$uploadedFiles[$fieldName]->getSize()} bytes\n";
        echo "  - Es válido: " . ($uploadedFiles[$fieldName]->isValid() ? 'Sí' : 'No') . "\n";
    }
    
    // 3. Datos del formulario
    $formData = [
        'nombre_completo' => 'Test Directo Form',
        'estatus' => 'activo',
        'categoria_personal_id' => 1,
        'crear_usuario' => false,
        'no_identificacion' => 'TEST123456789',
        'curp_numero' => 'TEST031105MTSRRNA2',
        'rfc' => 'TEST031105ABC',
        'nss' => '12345678901',
        'no_licencia' => 'LIC123456',
        'direccion' => 'Calle Test 123, Colonia Test'
    ];
    
    // 4. Crear request simulado
    echo "\n--- CREANDO REQUEST ---\n";
    $request = Request::create('/personal', 'POST', $formData, [], $uploadedFiles);
    $request->headers->set('Content-Type', 'multipart/form-data');
    
    // Verificar que los archivos están en el request
    echo "\n--- VERIFICANDO ARCHIVOS EN REQUEST ---\n";
    foreach ($documentTypes as $fieldName => $fileName) {
        if ($request->hasFile($fieldName)) {
            echo "✓ {$fieldName}: Archivo detectado\n";
            $file = $request->file($fieldName);
            echo "  - Nombre: {$file->getClientOriginalName()}\n";
            echo "  - MIME: {$file->getMimeType()}\n";
            echo "  - Válido: " . ($file->isValid() ? 'Sí' : 'No') . "\n";
        } else {
            echo "❌ {$fieldName}: NO detectado\n";
        }
    }
    
    // 5. Validar manualmente usando las reglas del FormRequest
    echo "\n--- VALIDACIÓN MANUAL ---\n";
    
    $rules = [
        'nombre_completo' => 'required|string|max:255|min:3',
        'estatus' => 'required|string|in:activo,inactivo',
        'categoria_personal_id' => 'required|integer|exists:categorias_personal,id',
        'identificacion_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'curp_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'rfc_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'nss_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'licencia_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'comprobante_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
    ];
    
    $validator = Validator::make($request->all(), $rules);
    
    if ($validator->fails()) {
        echo "❌ Validación falló:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - {$error}\n";
        }
        return;
    }
    
    echo "✓ Validación exitosa\n";
    
    // 6. Probar método del controlador directamente
    echo "\n--- PROBANDO MÉTODO STOREWEB ---\n";
    
    // Crear una instancia del FormRequest válida
    $createPersonalRequest = new \App\Http\Requests\CreatePersonalRequest();
    
    // Usar reflection para establecer los datos validados
    $reflection = new \ReflectionClass($createPersonalRequest);
    $validatorProperty = $reflection->getProperty('validator');
    $validatorProperty->setAccessible(true);
    $validatorProperty->setValue($createPersonalRequest, $validator);
    
    // Establecer el request
    $createPersonalRequest->setContainer(app());
    $createPersonalRequest->setRedirector(app('redirect'));
    $createPersonalRequest->replace($request->all());
    $createPersonalRequest->files = $request->files;
    
    $controller = new PersonalManagementController();
    $result = $controller->storeWeb($createPersonalRequest);
    
    echo "✓ storeWeb ejecutado exitosamente\n";
    echo "Tipo de resultado: " . get_class($result) . "\n";
    
    if (method_exists($result, 'getTargetUrl')) {
        echo "URL de redirección: {$result->getTargetUrl()}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}\n";
    echo "Línea: {$e->getLine()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
} finally {
    // Limpiar archivos temporales
    echo "\n--- LIMPIANDO ARCHIVOS TEMPORALES ---\n";
    foreach ($tempFiles as $tempPath) {
        if (file_exists($tempPath)) {
            unlink($tempPath);
            echo "✓ Eliminado: " . basename($tempPath) . "\n";
        }
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";