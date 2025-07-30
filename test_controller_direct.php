<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Http\Controllers\PersonalManagementController;
use App\Models\Personal;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== PRUEBA DIRECTA DEL CONTROLADOR ===\n";

// 1. Crear archivos reales
echo "\n--- CREANDO ARCHIVOS REALES ---\n";
$pdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\nxref\n0 2\n0000000000 65535 f \n0000000009 00000 n \ntrailer\n<<\n/Size 2\n/Root 1 0 R\n>>\nstartxref\n50\n%%EOF";

$testFiles = [];
$fileNames = ['ine_test.pdf', 'curp_test.pdf'];

foreach ($fileNames as $fileName) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    file_put_contents($filePath, $pdfContent);
    echo "✓ Archivo creado: {$fileName} (" . filesize($filePath) . " bytes)\n";
}

// 2. Probar creación manual sin controlador
echo "\n--- CREACIÓN MANUAL SIN CONTROLADOR ---\n";

try {
    DB::beginTransaction();
    
    // Crear personal directamente (solo campos básicos)
    $personalData = [
        'nombre_completo' => 'Test Directo',
        'estatus' => 'activo',
        'categoria_id' => 1
    ];
    
    echo "Datos a insertar: " . json_encode($personalData) . "\n";
    
    echo "Creando personal...\n";
    $personal = Personal::create($personalData);
    echo "✓ Personal creado: ID {$personal->id}\n";
    
    // Simular subida de archivos
    echo "\nSimulando subida de archivos...\n";
    
    $storageDir = storage_path("app/public/personal/{$personal->id}/documentos");
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
        echo "✓ Directorio creado: {$storageDir}\n";
    }
    
    // Copiar archivos al storage
    $uploadedPaths = [];
    foreach ($fileNames as $index => $fileName) {
        $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
        $targetFileName = time() . "_{$index}_" . $fileName;
        $targetPath = $storageDir . DIRECTORY_SEPARATOR . $targetFileName;
        
        copy($sourcePath, $targetPath);
        $relativePath = "personal/{$personal->id}/documentos/{$targetFileName}";
        $uploadedPaths[] = $relativePath;
        
        echo "✓ Archivo copiado: {$fileName} -> {$relativePath}\n";
        
        // Verificar archivo
        if (file_exists($targetPath)) {
            echo "  - Archivo físico existe: " . filesize($targetPath) . " bytes\n";
        }
    }
    
    // Crear documentos en BD
    echo "\nCreando documentos en BD...\n";
    
    $documentos = [
        [
            'personal_id' => $personal->id,
            'tipo_documento_id' => 8, // INE
            'descripcion' => 'INE Test Document',
            'ruta_archivo' => $uploadedPaths[0]
        ],
        [
            'personal_id' => $personal->id,
            'tipo_documento_id' => 9, // CURP
            'descripcion' => 'CURP Test Document',
            'ruta_archivo' => $uploadedPaths[1]
        ]
    ];
    
    foreach ($documentos as $docData) {
        $documento = Documento::create($docData);
        echo "✓ Documento creado: ID {$documento->id} - {$documento->descripcion}\n";
    }
    
    DB::commit();
    
    echo "\n✅ CREACIÓN MANUAL EXITOSA\n";
    
    // Verificación final
    $personalFinal = Personal::with(['documentos.tipoDocumento', 'categoria'])->find($personal->id);
    
    echo "\n--- VERIFICACIÓN FINAL ---\n";
    echo "Personal: {$personalFinal->nombre_completo}\n";
    echo "Categoría: {$personalFinal->categoria->nombre_categoria}\n";
    echo "Documentos: {$personalFinal->documentos->count()}\n";
    
    foreach ($personalFinal->documentos as $doc) {
        echo "  - {$doc->tipoDocumento->nombre_tipo_documento}: {$doc->descripcion}\n";
        echo "    Archivo: {$doc->ruta_archivo}\n";
        
        $fullPath = storage_path('app/public/' . $doc->ruta_archivo);
        echo "    Existe: " . (file_exists($fullPath) ? 'Sí (' . filesize($fullPath) . ' bytes)' : 'No') . "\n";
    }
    
    // Probar acceso web
    echo "\n--- PROBANDO ACCESO WEB ---\n";
    $webUrl = "http://127.0.0.1:8000/storage/{$uploadedPaths[0]}";
    echo "URL de prueba: {$webUrl}\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

// Limpiar archivos temporales
echo "\n--- LIMPIANDO ARCHIVOS TEMPORALES ---\n";
foreach ($fileNames as $fileName) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
        echo "✓ Archivo temporal eliminado: {$fileName}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";