<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Vehiculo;
use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use App\Models\CatalogoEstatus;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DEPURACIÓN DEL FORMULARIO DE VEHÍCULOS\n";
echo "==========================================\n\n";

// 1. Verificar tipos de documento disponibles
echo "1. TIPOS DE DOCUMENTO DISPONIBLES:\n";
$tipos = CatalogoTipoDocumento::orderBy('id')->get();
foreach ($tipos as $tipo) {
    echo "   - ID: {$tipo->id}, Nombre: '{$tipo->nombre_tipo_documento}'\n";
}

// 2. Verificar el mapeo en el controlador
echo "\n2. MAPEO EN EL CONTROLADOR:\n";
$documentosEstructurados = [
    'tarjeta_circulacion_file' => [
        'tipo' => 'Tarjeta de Circulación',
        'numero_campo' => 'no_tarjeta_circulacion',
        'fecha_campo' => 'fecha_vencimiento_tarjeta'
    ],
    'derecho_vehicular_file' => [
        'tipo' => 'Tenencia Vehicular',
        'numero_campo' => 'no_derecho_vehicular',
        'fecha_campo' => 'fecha_vencimiento_derecho'
    ],
    'poliza_seguro_file' => [
        'tipo' => 'Póliza de Seguro',
        'numero_campo' => 'no_poliza_seguro',
        'fecha_campo' => 'fecha_vencimiento_seguro',
        'extra_campo' => 'aseguradora'
    ],
    'factura_pedimento_file' => [
        'tipo' => 'Factura de Compra',
        'numero_campo' => 'no_factura_pedimento',
        'fecha_campo' => null
    ]
];

foreach ($documentosEstructurados as $campoArchivo => $config) {
    echo "   - Campo: '{$campoArchivo}' -> Tipo: '{$config['tipo']}'\n";
    
    // Verificar si existe el tipo en la BD
    $tipoEnBD = CatalogoTipoDocumento::where('nombre_tipo_documento', $config['tipo'])->first();
    if ($tipoEnBD) {
        echo "     ✅ Tipo encontrado en BD (ID: {$tipoEnBD->id})\n";
    } else {
        echo "     ❌ Tipo NO encontrado en BD\n";
    }
}

// 3. Simular datos del formulario
echo "\n3. SIMULANDO DATOS DEL FORMULARIO:\n";

// Crear archivos de prueba
$testFiles = [];
$uploadDir = storage_path('app/test_uploads');
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

foreach ($documentosEstructurados as $campoArchivo => $config) {
    $fileName = "test_{$campoArchivo}.pdf";
    $filePath = $uploadDir . '/' . $fileName;
    file_put_contents($filePath, "Contenido de prueba para {$config['tipo']}");
    
    $testFiles[$campoArchivo] = new UploadedFile(
        $filePath,
        $fileName,
        'application/pdf',
        null,
        true
    );
    
    echo "   - Archivo creado: {$campoArchivo} -> {$fileName}\n";
}

// 4. Simular request con datos
echo "\n4. SIMULANDO REQUEST:\n";

$requestData = [
    'marca' => 'TOYOTA',
    'modelo' => 'COROLLA',
    'anio' => 2020,
    'placas' => 'DEBUG-001',
    'n_serie' => 'DEBUG123456789',
    'color' => 'BLANCO',
    'estatus_id' => 1,
    'no_tarjeta_circulacion' => 'TC123456',
    'fecha_vencimiento_tarjeta' => '2025-12-31',
    'no_derecho_vehicular' => 'DV789012',
    'fecha_vencimiento_derecho' => '2025-12-31',
    'no_poliza_seguro' => 'PS345678',
    'fecha_vencimiento_seguro' => '2025-12-31',
    'aseguradora' => 'SEGUROS TEST',
    'no_factura_pedimento' => 'FP901234'
];

echo "   Datos del vehículo:\n";
foreach ($requestData as $campo => $valor) {
    echo "     - {$campo}: {$valor}\n";
}

echo "\n   Archivos enviados:\n";
foreach ($testFiles as $campo => $archivo) {
    echo "     - {$campo}: {$archivo->getClientOriginalName()}\n";
}

// 5. Simular procesamiento
echo "\n5. SIMULANDO PROCESAMIENTO:\n";

try {
    // Crear vehículo
    $vehiculo = Vehiculo::create($requestData);
    echo "   ✅ Vehículo creado (ID: {$vehiculo->id})\n";
    
    // Procesar documentos
    $documentosCreados = 0;
    foreach ($documentosEstructurados as $campoArchivo => $config) {
        if (isset($testFiles[$campoArchivo])) {
            // Obtener el tipo de documento
            $tipoDocumento = CatalogoTipoDocumento::where('nombre_tipo_documento', $config['tipo'])->first();
            
            if ($tipoDocumento) {
                echo "   📄 Procesando {$campoArchivo}:\n";
                echo "      - Tipo encontrado: {$tipoDocumento->nombre_tipo_documento} (ID: {$tipoDocumento->id})\n";
                
                // Simular subida de archivo
                $archivo = $testFiles[$campoArchivo];
                $nombreArchivo = time() . '_' . str_replace(' ', '_', strtolower($config['tipo'])) . '_' . $archivo->getClientOriginalName();
                $rutaArchivo = 'vehiculos/documentos/' . $nombreArchivo;
                
                // Preparar contenido adicional
                $contenido = [];
                if ($config['numero_campo'] && isset($requestData[$config['numero_campo']])) {
                    $contenido['numero'] = $requestData[$config['numero_campo']];
                }
                if (isset($config['extra_campo']) && isset($requestData[$config['extra_campo']])) {
                    $contenido['aseguradora'] = $requestData[$config['extra_campo']];
                }
                
                // Crear documento
                $documento = Documento::create([
                    'tipo_documento_id' => $tipoDocumento->id,
                    'descripcion' => $config['tipo'] . ' del vehículo',
                    'ruta_archivo' => $rutaArchivo,
                    'fecha_vencimiento' => $config['fecha_campo'] && isset($requestData[$config['fecha_campo']]) 
                        ? $requestData[$config['fecha_campo']] 
                        : null,
                    'vehiculo_id' => $vehiculo->id,
                    'contenido' => !empty($contenido) ? $contenido : null,
                ]);
                
                echo "      - Documento creado (ID: {$documento->id})\n";
                echo "      - Ruta: {$rutaArchivo}\n";
                echo "      - Contenido: " . json_encode($contenido) . "\n";
                
                $documentosCreados++;
            } else {
                echo "   ❌ Tipo de documento '{$config['tipo']}' no encontrado\n";
            }
        }
    }
    
    echo "\n   📊 RESUMEN:\n";
    echo "      - Vehículo ID: {$vehiculo->id}\n";
    echo "      - Placas: {$vehiculo->placas}\n";
    echo "      - Documentos creados: {$documentosCreados}\n";
    
    // Verificar documentos en BD
    $documentosEnBD = Documento::where('vehiculo_id', $vehiculo->id)->get();
    echo "      - Documentos en BD: {$documentosEnBD->count()}\n";
    
    foreach ($documentosEnBD as $doc) {
        $tipo = CatalogoTipoDocumento::find($doc->tipo_documento_id);
        echo "        * {$tipo->nombre_tipo_documento} (ID: {$doc->id})\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// Limpiar archivos de prueba
echo "\n6. LIMPIEZA:\n";
foreach ($testFiles as $archivo) {
    if (file_exists($archivo->getPathname())) {
        unlink($archivo->getPathname());
        echo "   - Archivo eliminado: {$archivo->getClientOriginalName()}\n";
    }
}

echo "\n=== DEPURACIÓN COMPLETADA ===\n";