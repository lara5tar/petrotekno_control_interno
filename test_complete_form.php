<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PersonalManagementController;
use App\Http\Requests\CreatePersonalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

echo "=== PRUEBA COMPLETA DEL FORMULARIO ===\n";

// 1. Autenticar usuario
$user = \App\Models\User::first();
if ($user) {
    Auth::login($user);
    echo "✓ Usuario autenticado: {$user->email}\n";
} else {
    echo "❌ No hay usuarios disponibles\n";
    exit(1);
}

// 2. Crear archivos de prueba temporales con contenido PDF real
echo "\n--- CREANDO ARCHIVOS DE PRUEBA ---\n";

$tempDir = sys_get_temp_dir();
$testFiles = [];

// Crear un PDF básico válido
$pdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000074 00000 n \n0000000120 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n179\n%%EOF";

// Crear archivos de prueba
$fileTypes = [
    'identificacion_file' => 'test_ine.pdf',
    'curp_file' => 'test_curp.pdf',
    'rfc_file' => 'test_rfc.pdf'
];

foreach ($fileTypes as $fieldName => $fileName) {
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

// 3. Preparar datos del formulario
$formData = [
    'nombre_completo' => 'Test Usuario Completo',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'ine' => '1111222233334444',
    'curp_numero' => 'TEUC900101HDFSTR01',
    'rfc' => 'TEUC900101ABC',
    'nss' => '11122233344',
    'no_licencia' => 'LIC111222',
    'direccion' => 'Calle de Prueba 123, Ciudad Test'
];

echo "\n--- DATOS DEL FORMULARIO ---\n";
foreach ($formData as $key => $value) {
    echo "{$key}: {$value}\n";
}

// 4. Crear request simulado
echo "\n--- CREANDO REQUEST SIMULADO ---\n";

try {
    // Crear request con datos y archivos
    $request = Request::create('/personal', 'POST', $formData, [], $testFiles);
    
    // Simular headers necesarios
    $request->headers->set('Content-Type', 'multipart/form-data');
    $request->headers->set('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
    
    echo "✓ Request creado con " . count($testFiles) . " archivos\n";
    
    // Verificar que los archivos están en el request
    foreach ($fileTypes as $fieldName => $fileName) {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);
            echo "✓ Archivo {$fieldName} detectado: {$file->getClientOriginalName()} ({$file->getMimeType()})\n";
        } else {
            echo "❌ Archivo {$fieldName} NO detectado en request\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error creando request: {$e->getMessage()}\n";
    exit(1);
}

// 5. Validar datos manualmente primero
echo "\n--- VALIDANDO DATOS ---\n";

try {
    $personalRequest = new CreatePersonalRequest();
    $rules = $personalRequest->rules();
    
    // Combinar datos del formulario y archivos para validación
    $allData = array_merge($formData, $testFiles);
    
    $validator = Validator::make($allData, $rules);
    
    if ($validator->fails()) {
        echo "❌ Errores de validación:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - {$error}\n";
        }
        
        // Mostrar reglas para archivos
        echo "\nReglas para archivos:\n";
        foreach ($rules as $field => $rule) {
            if (str_contains($field, '_file')) {
                echo "  {$field}: " . (is_array($rule) ? implode('|', $rule) : $rule) . "\n";
            }
        }
        exit(1);
    } else {
        echo "✓ Validación exitosa\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error en validación: {$e->getMessage()}\n";
    exit(1);
}

// 6. Probar el controlador directamente sin FormRequest
echo "\n--- PROBANDO CONTROLADOR DIRECTAMENTE ---\n";

DB::beginTransaction();

try {
    $controller = new PersonalManagementController();
    
    // Usar reflexión para acceder al método createPersonal directamente
    $reflection = new \ReflectionClass($controller);
    $createPersonalMethod = $reflection->getMethod('createPersonal');
    $createPersonalMethod->setAccessible(true);
    
    echo "✓ Método createPersonal accesible\n";
    
    // Crear personal directamente
    $personalData = [
        'nombre_completo' => $formData['nombre_completo'],
        'estatus' => $formData['estatus'],
        'categoria_id' => $formData['categoria_personal_id'], // Nota: cambio de nombre
        'ine' => $formData['ine'] ?? null,
        'curp_numero' => $formData['curp_numero'] ?? null,
        'rfc' => $formData['rfc'] ?? null,
        'nss' => $formData['nss'] ?? null,
        'no_licencia' => $formData['no_licencia'] ?? null,
        'direccion' => $formData['direccion'] ?? null,
    ];
    
    $personal = $createPersonalMethod->invoke($controller, $personalData);
    
    echo "✓ Personal creado con ID: {$personal->id}\n";
    
    // Probar subida de archivos
    $handleDocumentMethod = $reflection->getMethod('handleDocumentUpload');
    $handleDocumentMethod->setAccessible(true);
    
    $documentTypes = [
        'identificacion_file' => 1, // Tipo de documento para identificación
        'curp_file' => 2, // Tipo de documento para CURP
        'rfc_file' => 3, // Tipo de documento para RFC
    ];
    
    foreach ($testFiles as $fieldName => $file) {
        if (isset($documentTypes[$fieldName])) {
            try {
                $documento = $handleDocumentMethod->invoke(
                    $controller,
                    $file,
                    $personal->id,
                    $documentTypes[$fieldName],
                    ucfirst(str_replace('_file', '', $fieldName))
                );
                
                echo "✓ Documento {$fieldName} subido con ID: {$documento->id}\n";
            } catch (\Exception $e) {
                echo "❌ Error subiendo {$fieldName}: {$e->getMessage()}\n";
            }
        }
    }
    
    DB::commit();
    
    // Verificar resultado final
    $personalCreado = \App\Models\Personal::with('documentos')->find($personal->id);
    
    echo "\n✅ RESULTADO FINAL:\n";
    echo "Personal ID: {$personalCreado->id}\n";
    echo "Nombre: {$personalCreado->nombre_completo}\n";
    echo "INE: {$personalCreado->ine}\n";
    echo "CURP: {$personalCreado->curp_numero}\n";
    echo "RFC: {$personalCreado->rfc}\n";
    echo "NSS: {$personalCreado->nss}\n";
    echo "Licencia: {$personalCreado->no_licencia}\n";
    echo "Dirección: {$personalCreado->direccion}\n";
    echo "Documentos: {$personalCreado->documentos->count()}\n";
    
    foreach ($personalCreado->documentos as $doc) {
        echo "  - Documento {$doc->id}: {$doc->descripcion} (Tipo: {$doc->tipo_documento_id})\n";
        echo "    Archivo: {$doc->ruta_archivo}\n";
        
        // Verificar si el archivo existe físicamente
        $fullPath = storage_path('app/public/' . $doc->ruta_archivo);
        if (file_exists($fullPath)) {
            echo "    ✓ Archivo físico existe (" . filesize($fullPath) . " bytes)\n";
        } else {
            echo "    ❌ Archivo físico NO existe\n";
        }
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR EN CONTROLADOR: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}\n";
    echo "Línea: {$e->getLine()}\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// 7. Limpiar archivos temporales
echo "\n--- LIMPIANDO ARCHIVOS TEMPORALES ---\n";
foreach ($fileTypes as $fieldName => $fileName) {
    $filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
        echo "✓ Archivo {$fileName} eliminado\n";
    }
}

echo "\n=== FIN DE LA PRUEBA COMPLETA ===\n";