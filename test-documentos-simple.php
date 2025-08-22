<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Obra;
use App\Models\Documento;
use Illuminate\Http\UploadedFile;

echo "🧪 Probando sistema de documentos...\n";

try {
    // 1. Crear una obra de prueba
    $obra = new Obra();
    $obra->nombre_obra = 'Prueba Documentos ' . date('Y-m-d H:i:s');
    $obra->estatus = 'planificada';
    $obra->fecha_inicio = '2024-01-01';
    $obra->encargado_id = 1;
    $obra->save();
    
    echo "✅ Obra creada con ID: {$obra->id}\n";
    
    // 2. Verificar archivo de prueba
    $archivoPath = __DIR__ . '/test_simple.pdf';
    if (!file_exists($archivoPath)) {
        echo "❌ Archivo de prueba no existe: {$archivoPath}\n";
        exit(1);
    }
    
    // 3. Simular un archivo subido
    $archivoFake = new UploadedFile(
        $archivoPath,
        'test_contrato.pdf',
        'application/pdf',
        null,
        true
    );
    
    echo "📄 Archivo de prueba preparado: {$archivoFake->getClientOriginalName()}\n";
    
    // 4. Probar subir contrato
    $resultado = $obra->subirContrato($archivoFake);
    echo "📤 Resultado subir contrato: " . ($resultado ? "✅ SI" : "❌ NO") . "\n";
    
    // 5. Verificar en tabla obras
    $obra->refresh();
    echo "📋 Archivo contrato en obra: " . ($obra->archivo_contrato ? "✅ SI ({$obra->archivo_contrato})" : "❌ NO") . "\n";
    
    // 6. Verificar en tabla documentos
    $documentos = $obra->documentos()->count();
    echo "📑 Documentos en tabla: {$documentos}\n";
    
    if ($documentos > 0) {
        $documento = $obra->documentos()->first();
        echo "📋 Primer documento:\n";
        echo "   - Tipo: {$documento->tipoDocumento->nombre_tipo_documento}\n";
        echo "   - Descripción: {$documento->descripcion}\n";
        echo "   - Ruta: {$documento->ruta_archivo}\n";
        echo "   - Contenido: " . json_encode($documento->contenido) . "\n";
    }
    
    echo "\n🎉 Prueba completada exitosamente!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
}
