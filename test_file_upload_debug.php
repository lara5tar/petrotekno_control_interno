<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\PersonalManagementController;
use App\Http\Requests\CreatePersonalRequest;
use App\Models\Personal;
use App\Models\Documento;
use App\Models\CategoriaPersonal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== DEPURACIÓN DE SUBIDA DE ARCHIVOS ===\n";

// 1. Verificar directorios de almacenamiento
echo "\n--- VERIFICANDO DIRECTORIOS ---\n";
$storagePublic = storage_path('app/public');
$personalDir = storage_path('app/public/personal');

echo "Storage público: {$storagePublic}\n";
echo "Existe: " . (is_dir($storagePublic) ? 'Sí' : 'No') . "\n";
echo "Escribible: " . (is_writable($storagePublic) ? 'Sí' : 'No') . "\n";

echo "\nDirectorio personal: {$personalDir}\n";
echo "Existe: " . (is_dir($personalDir) ? 'Sí' : 'No') . "\n";
echo "Escribible: " . (is_writable($personalDir) ? 'Sí' : 'No') . "\n";

// Crear directorio si no existe
if (!is_dir($personalDir)) {
    mkdir($personalDir, 0755, true);
    echo "✓ Directorio personal creado\n";
}

// 2. Crear archivos de prueba
echo "\n--- CREANDO ARCHIVOS DE PRUEBA ---\n";
$tempDir = sys_get_temp_dir();
$testFiles = [];

$documentTypes = [
    'identificacion_file' => 'identificacion_test.pdf',
    'curp_file' => 'curp_test.pdf',
    'rfc_file' => 'rfc_test.pdf'
];

foreach ($documentTypes as $fieldName => $fileName) {
    $filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
    $content = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000074 00000 n \n0000000120 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n174\n%%EOF";
    file_put_contents($filePath, $content);
    
    $testFiles[$fieldName] = new UploadedFile(
        $filePath,
        $fileName,
        'application/pdf',
        filesize($filePath),
        true
    );
    
    echo "✓ Archivo creado: {$fileName} (" . filesize($filePath) . " bytes)\n";
}

// 3. Simular request con archivos
echo "\n--- SIMULANDO REQUEST ---\n";

$formData = [
    'nombre_completo' => 'Test Debug Upload',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'ine' => 'TEST123456789',
    'curp_numero' => 'TEST031105MTSRRNA2',
    'rfc' => 'TEST031105ABC'
];

// Crear request
$request = Request::create('/personal', 'POST', $formData, [], $testFiles);
$request->headers->set('Content-Type', 'multipart/form-data');

echo "Request creado con " . count($testFiles) . " archivos\n";

// Verificar archivos en request
foreach ($documentTypes as $fieldName => $fileName) {
    if ($request->hasFile($fieldName)) {
        $file = $request->file($fieldName);
        echo "✓ {$fieldName}: {$file->getClientOriginalName()} ({$file->getSize()} bytes)\n";
        echo "  - MIME: {$file->getMimeType()}\n";
        echo "  - Válido: " . ($file->isValid() ? 'Sí' : 'No') . "\n";
    } else {
        echo "❌ {$fieldName}: No encontrado en request\n";
    }
}

// 4. Probar el controlador directamente
echo "\n--- PROBANDO CONTROLADOR ---\n";

try {
    DB::beginTransaction();
    
    // Crear personal primero
    $personal = Personal::create([
        'nombre_completo' => $formData['nombre_completo'],
        'estatus' => $formData['estatus'],
        'categoria_id' => $formData['categoria_personal_id'],
        'ine' => $formData['ine'],
        'curp_numero' => $formData['curp_numero'],
        'rfc' => $formData['rfc']
    ]);
    
    echo "✓ Personal creado: ID {$personal->id}\n";
    
    // Probar subida de archivos manualmente
    $controller = new PersonalManagementController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('handleDocumentUpload');
    $method->setAccessible(true);
    
    $uploadedPaths = [];
    
    foreach ($testFiles as $fieldName => $uploadedFile) {
        try {
            $path = $method->invoke($controller, $uploadedFile, $personal->id);
            $uploadedPaths[$fieldName] = $path;
            echo "✓ Archivo subido: {$fieldName} -> {$path}\n";
            
            // Verificar que el archivo existe físicamente
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                echo "  - Archivo físico existe: " . filesize($fullPath) . " bytes\n";
            } else {
                echo "  - ❌ Archivo físico NO existe\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Error subiendo {$fieldName}: {$e->getMessage()}\n";
        }
    }
    
    // Crear documentos en la base de datos
    $tiposDocumento = [
        'identificacion_file' => 8,
        'curp_file' => 9,
        'rfc_file' => 10
    ];
    
    foreach ($uploadedPaths as $fieldName => $path) {
        $documento = Documento::create([
            'personal_id' => $personal->id,
            'tipo_documento_id' => $tiposDocumento[$fieldName],
            'descripcion' => $formData[str_replace('_file', '', $fieldName)] ?? 'Documento de prueba',
            'ruta_archivo' => $path
        ]);
        
        echo "✓ Documento creado en BD: ID {$documento->id}\n";
    }
    
    DB::commit();
    
    echo "\n✅ PRUEBA COMPLETADA EXITOSAMENTE\n";
    
    // Verificación final
    $personalFinal = Personal::with('documentos')->find($personal->id);
    echo "\n--- VERIFICACIÓN FINAL ---\n";
    echo "Personal: {$personalFinal->nombre_completo}\n";
    echo "Documentos en BD: {$personalFinal->documentos->count()}\n";
    
    foreach ($personalFinal->documentos as $doc) {
        echo "  - Documento ID {$doc->id}: {$doc->ruta_archivo}\n";
        $fullPath = storage_path('app/public/' . $doc->ruta_archivo);
        echo "    Archivo existe: " . (file_exists($fullPath) ? 'Sí' : 'No') . "\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

// Limpiar archivos temporales
foreach ($testFiles as $file) {
    if (file_exists($file->getPathname())) {
        unlink($file->getPathname());
    }
}

echo "\n=== DEPURACIÓN COMPLETADA ===\n";