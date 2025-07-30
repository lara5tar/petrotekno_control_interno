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
use Illuminate\Support\Facades\Log;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== SIMULACIÓN COMPLETA DEL FORMULARIO WEB ===\n";

// 1. Verificar categorías disponibles
echo "\n--- VERIFICANDO CATEGORÍAS ---\n";
$categorias = CategoriaPersonal::all();
foreach ($categorias as $categoria) {
    echo "✓ Categoría ID {$categoria->id}: {$categoria->nombre_categoria}\n";
}

// 2. Crear archivos de prueba más realistas
echo "\n--- CREANDO ARCHIVOS DE PRUEBA ---\n";
$tempDir = sys_get_temp_dir();
$testFiles = [];

$documentTypes = [
    'identificacion_file' => 'ine_prueba.pdf',
    'curp_file' => 'curp_prueba.pdf',
    'rfc_file' => 'rfc_prueba.pdf',
    'nss_file' => 'nss_prueba.pdf',
    'licencia_file' => 'licencia_prueba.pdf',
    'comprobante_file' => 'comprobante_prueba.pdf',
    'cv_file' => 'cv_prueba.pdf'
];

// Crear contenido PDF más realista
$pdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 44\n>>\nstream\nBT\n/F1 12 Tf\n72 720 Td\n(Documento de prueba) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000074 00000 n \n0000000120 00000 n \n0000000179 00000 n \ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n274\n%%EOF";

foreach ($documentTypes as $fieldName => $fileName) {
    $filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
    file_put_contents($filePath, $pdfContent);
    
    $testFiles[$fieldName] = new UploadedFile(
        $filePath,
        $fileName,
        'application/pdf',
        filesize($filePath),
        true // test mode
    );
    
    echo "✓ Archivo creado: {$fileName} (" . filesize($filePath) . " bytes)\n";
}

// 3. Datos del formulario completos
echo "\n--- PREPARANDO DATOS DEL FORMULARIO ---\n";
$formData = [
    'nombre_completo' => 'Juan Pérez García',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'ine' => 'PEGJ850315HDFRRN01',
    'curp_numero' => 'PEGJ850315HDFRRN01',
    'rfc' => 'PEGJ850315ABC',
    'nss' => '12345678901',
    'no_licencia' => 'LIC123456789',
    'direccion' => 'Calle Principal 123, Colonia Centro, Ciudad de México, CP 01000'
];

echo "Datos del formulario preparados:\n";
foreach ($formData as $key => $value) {
    echo "  {$key}: {$value}\n";
}

// 4. Simular el request completo como lo haría el navegador
echo "\n--- SIMULANDO REQUEST DEL NAVEGADOR ---\n";

$request = Request::create('/personal', 'POST', $formData, [], $testFiles);
$request->headers->set('Content-Type', 'multipart/form-data');
$request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
$request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

echo "Request creado con " . count($testFiles) . " archivos\n";

// Verificar que todos los archivos están presentes
foreach ($documentTypes as $fieldName => $fileName) {
    if ($request->hasFile($fieldName)) {
        echo "✓ {$fieldName}: Presente\n";
    } else {
        echo "❌ {$fieldName}: Faltante\n";
    }
}

// 5. Probar el controlador completo
echo "\n--- EJECUTANDO CONTROLADOR COMPLETO ---\n";

try {
    // Habilitar logging detallado
    Log::info('Iniciando prueba de formulario web completo');
    
    // Crear una instancia del request de validación
    $createPersonalRequest = CreatePersonalRequest::createFrom($request);
    
    // Ejecutar el controlador
    $controller = new PersonalManagementController();
    
    DB::beginTransaction();
    
    echo "Ejecutando storeWeb...\n";
    $response = $controller->storeWeb($createPersonalRequest);
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✓ Respuesta de redirección recibida\n";
        echo "URL de redirección: {$response->getTargetUrl()}\n";
        
        // Verificar si hay mensajes de sesión
        $session = $response->getSession();
        if ($session && $session->has('success')) {
            echo "✓ Mensaje de éxito: {$session->get('success')}\n";
        }
        if ($session && $session->has('error')) {
            echo "❌ Mensaje de error: {$session->get('error')}\n";
        }
    }
    
    DB::commit();
    
    echo "\n✅ CONTROLADOR EJECUTADO EXITOSAMENTE\n";
    
} catch (\Illuminate\Validation\ValidationException $e) {
    DB::rollBack();
    echo "❌ ERROR DE VALIDACIÓN:\n";
    foreach ($e->errors() as $field => $errors) {
        echo "  {$field}: " . implode(', ', $errors) . "\n";
    }
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR GENERAL: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

// 6. Verificar el resultado final
echo "\n--- VERIFICACIÓN FINAL ---\n";

$ultimoPersonal = Personal::with(['documentos.tipoDocumento', 'categoria'])->latest()->first();

if ($ultimoPersonal) {
    echo "Último personal creado:\n";
    echo "  ID: {$ultimoPersonal->id}\n";
    echo "  Nombre: {$ultimoPersonal->nombre_completo}\n";
    echo "  Categoría: {$ultimoPersonal->categoria->nombre_categoria}\n";
    echo "  Documentos: {$ultimoPersonal->documentos->count()}\n";
    
    if ($ultimoPersonal->documentos->count() > 0) {
        echo "\nDocumentos asociados:\n";
        foreach ($ultimoPersonal->documentos as $doc) {
            echo "  - {$doc->tipoDocumento->nombre_tipo_documento}: {$doc->descripcion}\n";
            echo "    Archivo: {$doc->ruta_archivo}\n";
            
            // Verificar archivo físico
            $fullPath = storage_path('app/public/' . $doc->ruta_archivo);
            if (file_exists($fullPath)) {
                echo "    ✓ Archivo físico existe (" . filesize($fullPath) . " bytes)\n";
            } else {
                echo "    ❌ Archivo físico NO existe\n";
            }
        }
    } else {
        echo "\n❌ NO SE CREARON DOCUMENTOS\n";
    }
} else {
    echo "❌ No se encontró ningún personal en la base de datos\n";
}

// 7. Verificar logs
echo "\n--- VERIFICANDO LOGS ---\n";
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20); // Últimas 20 líneas
    
    echo "Últimas entradas del log:\n";
    foreach ($recentLines as $line) {
        if (strpos($line, 'Processing file') !== false || 
            strpos($line, 'File uploaded') !== false ||
            strpos($line, 'Document data prepared') !== false ||
            strpos($line, 'Creating documents') !== false) {
            echo "  {$line}\n";
        }
    }
}

// Limpiar archivos temporales
foreach ($testFiles as $file) {
    if (file_exists($file->getPathname())) {
        unlink($file->getPathname());
    }
}

echo "\n=== SIMULACIÓN COMPLETADA ===\n";