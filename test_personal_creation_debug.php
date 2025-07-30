<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Personal;
use App\Models\CategoriaPersonal;
use App\Models\Documento;
use App\Models\CatalogoTipoDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== DIAGNÓSTICO DE CREACIÓN DE PERSONAL ===\n";

// 1. Verificar categorías disponibles
echo "\n--- CATEGORÍAS DISPONIBLES ---\n";
$categorias = CategoriaPersonal::all();
foreach ($categorias as $categoria) {
    echo "ID: {$categoria->id} - {$categoria->nombre_categoria}\n";
}

// 2. Verificar tipos de documento
echo "\n--- TIPOS DE DOCUMENTO ---\n";
$tiposDocumento = CatalogoTipoDocumento::whereIn('id', [7, 8, 9, 10, 11, 28, 30])->get();
foreach ($tiposDocumento as $tipo) {
    echo "ID: {$tipo->id} - {$tipo->nombre_tipo_documento}\n";
}

// 3. Verificar directorio de storage
echo "\n--- VERIFICANDO STORAGE ---\n";
$storagePath = storage_path('app/public');
echo "Storage path: {$storagePath}\n";
echo "Storage exists: " . (is_dir($storagePath) ? 'Sí' : 'No') . "\n";
echo "Storage writable: " . (is_writable($storagePath) ? 'Sí' : 'No') . "\n";

// Crear directorio de personal si no existe
$personalDir = storage_path('app/public/personal');
if (!is_dir($personalDir)) {
    mkdir($personalDir, 0755, true);
    echo "✓ Directorio personal creado\n";
} else {
    echo "✓ Directorio personal existe\n";
}

// 4. Simular creación de personal
echo "\n--- SIMULANDO CREACIÓN DE PERSONAL ---\n";

DB::beginTransaction();

try {
    // Datos de prueba
    $datosPersonal = [
        'nombre_completo' => 'Juan Pérez Test',
        'estatus' => 'activo',
        'categoria_id' => $categorias->first()->id,
        'ine' => '1234567890123456',
        'curp_numero' => 'PEPJ800101HDFRRN01',
        'rfc' => 'PEPJ800101ABC',
        'nss' => '12345678901',
        'no_licencia' => 'LIC123456',
        'direccion' => 'Calle Falsa 123, Ciudad, Estado'
    ];
    
    echo "Creando personal con datos:\n";
    foreach ($datosPersonal as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
    
    $personal = Personal::create($datosPersonal);
    echo "✓ Personal creado con ID: {$personal->id}\n";
    
    // Simular creación de documentos
    echo "\n--- SIMULANDO DOCUMENTOS ---\n";
    
    $documentosData = [
        [
            'tipo_documento_id' => 8, // INE
            'descripcion' => '1234567890123456',
            'ruta_archivo' => 'personal/' . $personal->id . '/documentos/test_ine.pdf'
        ],
        [
            'tipo_documento_id' => 9, // CURP
            'descripcion' => 'PEPJ800101HDFRRN01',
            'ruta_archivo' => 'personal/' . $personal->id . '/documentos/test_curp.pdf'
        ]
    ];
    
    foreach ($documentosData as $docData) {
        $documento = Documento::create([
            'personal_id' => $personal->id,
            'tipo_documento_id' => $docData['tipo_documento_id'],
            'descripcion' => $docData['descripcion'],
            'ruta_archivo' => $docData['ruta_archivo']
        ]);
        
        echo "✓ Documento creado: ID {$documento->id}, tipo {$documento->tipo_documento_id}\n";
    }
    
    DB::commit();
    
    // Verificar resultado
    $personalConDocs = Personal::with('documentos.tipoDocumento')->find($personal->id);
    echo "\n--- VERIFICACIÓN FINAL ---\n";
    echo "Personal: {$personalConDocs->nombre_completo}\n";
    echo "Documentos: {$personalConDocs->documentos->count()}\n";
    
    foreach ($personalConDocs->documentos as $doc) {
        echo "  - {$doc->tipoDocumento->nombre_tipo_documento}: {$doc->descripcion}\n";
    }
    
    echo "\n✅ PRUEBA EXITOSA - El problema NO está en la base de datos\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR EN LA PRUEBA: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}\n";
    echo "Línea: {$e->getLine()}\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";