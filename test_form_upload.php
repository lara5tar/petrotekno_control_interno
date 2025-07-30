<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Personal;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

echo "=== DIAGNÓSTICO DEL PROBLEMA DE SUBIDA DE ARCHIVOS ===\n";

// 1. Verificar configuración de archivos
echo "\n--- VERIFICANDO CONFIGURACIÓN ---\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Habilitado' : 'Deshabilitado') . "\n";

// 2. Verificar directorio de storage
echo "\n--- VERIFICANDO DIRECTORIOS ---\n";
$storagePath = storage_path('app');
echo "Storage path: {$storagePath}\n";
echo "Storage writable: " . (is_writable($storagePath) ? 'Sí' : 'No') . "\n";

$publicPath = storage_path('app/public');
if (!is_dir($publicPath)) {
    mkdir($publicPath, 0755, true);
    echo "✓ Directorio public creado\n";
} else {
    echo "✓ Directorio public existe\n";
}

// 3. Verificar tipos de documento
echo "\n--- VERIFICANDO TIPOS DE DOCUMENTO ---\n";
$tiposDocumento = [
    8 => 'Identificación Oficial',
    9 => 'CURP',
    10 => 'RFC',
    11 => 'NSS',
    7 => 'Licencia de Conducir',
    30 => 'Comprobante de Domicilio',
    28 => 'CV Profesional'
];

foreach ($tiposDocumento as $id => $nombre) {
    $existe = DB::table('catalogo_tipos_documento')->where('id', $id)->exists();
    echo ($existe ? '✓' : '❌') . " Tipo {$id}: {$nombre}\n";
}

// 4. Probar creación manual de personal con documentos
echo "\n--- PROBANDO CREACIÓN MANUAL ---\n";

DB::beginTransaction();

try {
    // Crear personal
    $personal = Personal::create([
        'nombre_completo' => 'Test Manual Upload',
        'estatus' => 'activo',
        'categoria_id' => 1,
    ]);
    
    echo "✓ Personal creado: ID {$personal->id}\n";
    
    // Simular archivos guardados
    $documentosData = [
        [
            'tipo_documento_id' => 8,
            'descripcion' => 'MANUAL123456789',
            'ruta_archivo' => "personal/{$personal->id}/documentos/identificacion_manual.pdf"
        ],
        [
            'tipo_documento_id' => 9,
            'descripcion' => 'MANU031105MTSRRNA2',
            'ruta_archivo' => "personal/{$personal->id}/documentos/curp_manual.pdf"
        ],
        [
            'tipo_documento_id' => 30,
            'descripcion' => 'Calle Manual 123',
            'ruta_archivo' => "personal/{$personal->id}/documentos/comprobante_manual.pdf"
        ]
    ];
    
    foreach ($documentosData as $docData) {
        $documento = Documento::create([
            'personal_id' => $personal->id,
            'tipo_documento_id' => $docData['tipo_documento_id'],
            'descripcion' => $docData['descripcion'],
            'ruta_archivo' => $docData['ruta_archivo'],
        ]);
        
        echo "✓ Documento creado: ID {$documento->id}, tipo {$documento->tipo_documento_id}\n";
    }
    
    DB::commit();
    
    // Verificar
    $personalConDocs = Personal::with('documentos.tipoDocumento')->find($personal->id);
    echo "\n--- VERIFICACIÓN ---\n";
    echo "Personal: {$personalConDocs->nombre_completo}\n";
    echo "Documentos: {$personalConDocs->documentos->count()}\n";
    
    foreach ($personalConDocs->documentos as $doc) {
        echo "  - {$doc->tipoDocumento->nombre_tipo_documento}: {$doc->descripcion}\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
}

// 5. Verificar el método storeWeb del controlador
echo "\n--- ANALIZANDO CONTROLADOR ---\n";

$controllerPath = app_path('Http/Controllers/PersonalManagementController.php');
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    
    // Buscar el método storeWeb
    if (strpos($content, 'function storeWeb') !== false) {
        echo "✓ Método storeWeb encontrado\n";
        
        // Verificar si procesa archivos
        if (strpos($content, 'hasFile') !== false) {
            echo "✓ Código de procesamiento de archivos encontrado\n";
        } else {
            echo "❌ NO se encontró código de procesamiento de archivos\n";
        }
        
        // Verificar mapeo de tipos de documento
        if (strpos($content, 'tiposDocumento') !== false) {
            echo "✓ Mapeo de tipos de documento encontrado\n";
        } else {
            echo "❌ NO se encontró mapeo de tipos de documento\n";
        }
        
    } else {
        echo "❌ Método storeWeb NO encontrado\n";
    }
} else {
    echo "❌ Archivo del controlador no encontrado\n";
}

// 6. Verificar ruta del formulario
echo "\n--- VERIFICANDO RUTAS ---\n";
$routesPath = base_path('routes/web.php');
if (file_exists($routesPath)) {
    $routes = file_get_contents($routesPath);
    
    if (strpos($routes, "Route::post('/personal'") !== false || strpos($routes, "personal.store") !== false) {
        echo "✓ Ruta POST para personal encontrada\n";
    } else {
        echo "❌ Ruta POST para personal NO encontrada\n";
    }
} else {
    echo "❌ Archivo de rutas no encontrado\n";
}

echo "\n=== DIAGNÓSTICO COMPLETADO ===\n";
echo "\nPOSIBLES PROBLEMAS:\n";
echo "1. Verificar que el formulario tenga enctype='multipart/form-data'\n";
echo "2. Verificar que los campos de archivo tengan los nombres correctos\n";
echo "3. Verificar que el controlador procese los archivos correctamente\n";
echo "4. Verificar permisos de escritura en storage\n";
echo "5. Verificar configuración PHP para subida de archivos\n";