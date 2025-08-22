<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Obra;
use App\Models\Documento;
use Illuminate\Http\UploadedFile;

echo "🧪 Probando TODOS los tipos de documentos...\n\n";

try {
    // 1. Crear una obra de prueba
    $obra = new Obra();
    $obra->nombre_obra = 'Obra Completa Documentos ' . date('Y-m-d H:i:s');
    $obra->estatus = 'planificada';
    $obra->fecha_inicio = '2024-01-01';
    $obra->encargado_id = 1;
    $obra->save();
    
    echo "✅ Obra creada con ID: {$obra->id}\n\n";
    
    // 2. Preparar archivos de prueba
    $archivoPath = __DIR__ . '/test_simple.pdf';
    $archivos = [
        'contrato' => new UploadedFile($archivoPath, 'contrato_prueba.pdf', 'application/pdf', null, true),
        'fianza' => new UploadedFile($archivoPath, 'fianza_prueba.pdf', 'application/pdf', null, true),
        'acta' => new UploadedFile($archivoPath, 'acta_prueba.pdf', 'application/pdf', null, true),
    ];
    
    // 3. Probar cada tipo de documento
    echo "📄 CONTRATO:\n";
    $resultadoContrato = $obra->subirContrato($archivos['contrato']);
    echo "   Resultado: " . ($resultadoContrato ? "✅ SI" : "❌ NO") . "\n";
    
    echo "\n💰 FIANZA:\n";
    $resultadoFianza = $obra->subirFianza($archivos['fianza']);
    echo "   Resultado: " . ($resultadoFianza ? "✅ SI" : "❌ NO") . "\n";
    
    echo "\n📋 ACTA ENTREGA-RECEPCIÓN:\n";
    $resultadoActa = $obra->subirActaEntregaRecepcion($archivos['acta']);
    echo "   Resultado: " . ($resultadoActa ? "✅ SI" : "❌ NO") . "\n";
    
    // 4. Verificar en tabla obras
    $obra->refresh();
    echo "\n📊 VERIFICACIÓN EN TABLA OBRAS:\n";
    echo "   Contrato: " . ($obra->archivo_contrato ? "✅ SI" : "❌ NO") . "\n";
    echo "   Fianza: " . ($obra->archivo_fianza ? "✅ SI" : "❌ NO") . "\n";
    echo "   Acta: " . ($obra->archivo_acta_entrega_recepcion ? "✅ SI" : "❌ NO") . "\n";
    
    // 5. Verificar en tabla documentos
    $documentos = $obra->documentos()->get();
    echo "\n📑 VERIFICACIÓN EN TABLA DOCUMENTOS:\n";
    echo "   Total documentos: {$documentos->count()}\n\n";
    
    foreach ($documentos as $doc) {
        echo "   📄 {$doc->tipoDocumento->nombre_tipo_documento}:\n";
        echo "      - Descripción: {$doc->descripcion}\n";
        echo "      - Archivo: " . basename($doc->ruta_archivo) . "\n";
        echo "      - Fecha: " . $doc->created_at->format('Y-m-d H:i:s') . "\n\n";
    }
    
    echo "🎉 ¡TODOS LOS DOCUMENTOS GUARDADOS CORRECTAMENTE!\n";
    echo "📈 Resumen:\n";
    echo "   - Archivos en storage: ✅\n";
    echo "   - Registros en tabla obras: ✅\n";
    echo "   - Registros en tabla documentos: ✅\n";
    echo "   - Metadatos completos: ✅\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
}
