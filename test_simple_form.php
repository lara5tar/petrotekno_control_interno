<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\PersonalManagementController;
use App\Models\Personal;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== PRUEBA SIMPLE DEL FORMULARIO ===\n";

// 1. Crear archivos de prueba
echo "\n--- CREANDO ARCHIVOS DE PRUEBA ---\n";
$testFiles = [];

$documentTypes = [
    'identificacion_file' => 'ine_simple.pdf',
    'curp_file' => 'curp_simple.pdf'
];

$pdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\nxref\n0 2\n0000000000 65535 f \n0000000009 00000 n \ntrailer\n<<\n/Size 2\n/Root 1 0 R\n>>\nstartxref\n50\n%%EOF";

foreach ($documentTypes as $fieldName => $fileName) {
    // Crear archivo en directorio actual
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    file_put_contents($filePath, $pdfContent);
    
    // Crear UploadedFile simulando un upload real
    $testFiles[$fieldName] = new UploadedFile(
        $filePath,
        $fileName,
        'application/pdf',
        filesize($filePath),
        UPLOAD_ERR_OK,
        true
    );
    
    echo "✓ Archivo creado: {$fileName} (" . filesize($filePath) . " bytes)\n";
}

// 2. Datos mínimos del formulario
$formData = [
    'nombre_completo' => 'Test Simple',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'ine' => 'TEST123456789',
    'curp_numero' => 'TEST031105MTSRRNA2'
];

echo "\n--- DATOS DEL FORMULARIO ---\n";
foreach ($formData as $key => $value) {
    echo "{$key}: {$value}\n";
}

// 3. Probar validación manual
echo "\n--- PROBANDO VALIDACIÓN ---\n";

$rules = [
    'nombre_completo' => 'required|string|max:255',
    'estatus' => 'required|string|in:activo,inactivo',
    'categoria_personal_id' => 'required|integer|exists:categorias_personal,id',
    'ine' => 'nullable|string|max:20',
    'curp_numero' => 'nullable|string|max:18',
    'identificacion_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    'curp_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
];

$validator = Validator::make(array_merge($formData, $testFiles), $rules);

if ($validator->fails()) {
    echo "❌ ERRORES DE VALIDACIÓN:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - {$error}\n";
    }
    exit(1);
} else {
    echo "✓ Validación exitosa\n";
}

// 4. Probar creación manual paso a paso
echo "\n--- CREACIÓN MANUAL PASO A PASO ---\n";

try {
    DB::beginTransaction();
    
    // Paso 1: Crear personal
    echo "Paso 1: Creando personal...\n";
    $personal = Personal::create([
        'nombre_completo' => $formData['nombre_completo'],
        'estatus' => $formData['estatus'],
        'categoria_id' => $formData['categoria_personal_id'],
        'ine' => $formData['ine'],
        'curp_numero' => $formData['curp_numero']
    ]);
    
    echo "✓ Personal creado: ID {$personal->id}\n";
    
    // Paso 2: Subir archivos
    echo "\nPaso 2: Subiendo archivos...\n";
    $uploadedFiles = [];
    
    foreach ($testFiles as $fieldName => $uploadedFile) {
        $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
        $path = $uploadedFile->storeAs("personal/{$personal->id}/documentos", $fileName, 'public');
        $uploadedFiles[$fieldName] = $path;
        
        echo "✓ Archivo subido: {$fieldName} -> {$path}\n";
        
        // Verificar archivo físico
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            echo "  - Archivo físico existe: " . filesize($fullPath) . " bytes\n";
        } else {
            echo "  - ❌ Archivo físico NO existe\n";
        }
    }
    
    // Paso 3: Crear documentos en BD
    echo "\nPaso 3: Creando documentos en BD...\n";
    $tiposDocumento = [
        'identificacion_file' => 8, // INE
        'curp_file' => 9 // CURP
    ];
    
    foreach ($uploadedFiles as $fieldName => $path) {
        $descripcion = match($fieldName) {
            'identificacion_file' => $formData['ine'],
            'curp_file' => $formData['curp_numero'],
            default => 'Documento'
        };
        
        $documento = Documento::create([
            'personal_id' => $personal->id,
            'tipo_documento_id' => $tiposDocumento[$fieldName],
            'descripcion' => $descripcion,
            'ruta_archivo' => $path
        ]);
        
        echo "✓ Documento creado: ID {$documento->id} - {$descripcion}\n";
    }
    
    DB::commit();
    
    echo "\n✅ CREACIÓN COMPLETADA EXITOSAMENTE\n";
    
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
        echo "    Existe: " . (file_exists($fullPath) ? 'Sí' : 'No') . "\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
}

// Limpiar archivos temporales
foreach ($documentTypes as $fieldName => $fileName) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
        echo "✓ Archivo temporal eliminado: {$fileName}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";