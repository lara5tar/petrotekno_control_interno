<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\CatalogoEstatus;
use App\Models\CatalogoTipoDocumento;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNÓSTICO DEL FORMULARIO DE VEHÍCULOS\n";
echo "==========================================\n\n";

// 1. Verificar tipos de documento disponibles
echo "📋 TIPOS DE DOCUMENTO DISPONIBLES:\n";
$tiposDocumento = CatalogoTipoDocumento::all();
foreach ($tiposDocumento as $tipo) {
    echo "- ID: {$tipo->id}, Nombre: {$tipo->nombre_tipo_documento}\n";
}

echo "\n🔍 VERIFICANDO MAPEO DE DOCUMENTOS EN EL CONTROLADOR:\n";
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

foreach ($documentosEstructurados as $campo => $config) {
    $tipoEncontrado = CatalogoTipoDocumento::where('nombre_tipo_documento', $config['tipo'])->first();
    if ($tipoEncontrado) {
        echo "✅ {$campo} -> {$config['tipo']} (ID: {$tipoEncontrado->id})\n";
    } else {
        echo "❌ {$campo} -> {$config['tipo']} (NO ENCONTRADO)\n";
    }
}

echo "\n🧪 SIMULANDO ENVÍO DE FORMULARIO:\n";

// Simular datos del formulario
$datosFormulario = [
    'marca' => 'Toyota',
    'modelo' => 'Corolla',
    'anio' => 2023,
    'n_serie' => 'TEST-FORM-' . time(),
    'placas' => 'FORM-' . rand(100, 999),
    'estatus_id' => 1,
    'kilometraje_actual' => 15000,
    'observaciones' => 'Vehículo de prueba desde formulario',
    
    // Datos de documentos
    'no_tarjeta_circulacion' => 'TC-FORM-123456',
    'fecha_vencimiento_tarjeta' => '2025-06-15',
    'no_poliza_seguro' => 'POL-FORM-789',
    'fecha_vencimiento_seguro' => '2025-03-20',
    'aseguradora' => 'Seguros Formulario S.A.',
    'no_factura_pedimento' => 'FAC-FORM-456'
];

echo "📝 Datos del formulario:\n";
foreach ($datosFormulario as $campo => $valor) {
    echo "  {$campo}: {$valor}\n";
}

// Crear archivos de prueba simulados
$archivosSimulados = [
    'tarjeta_circulacion_file' => 'tarjeta_circulacion_test.txt',
    'poliza_seguro_file' => 'poliza_seguro_test.txt',
    'factura_pedimento_file' => 'factura_compra_test.txt'
];

echo "\n📁 Creando archivos de prueba:\n";
foreach ($archivosSimulados as $campo => $nombreArchivo) {
    $contenido = "Contenido de prueba para {$campo} - " . date('Y-m-d H:i:s');
    Storage::disk('public')->put("vehiculos/documentos/{$nombreArchivo}", $contenido);
    echo "✅ Creado: {$nombreArchivo}\n";
}

try {
    DB::beginTransaction();
    
    // Crear vehículo
    echo "\n🚗 Creando vehículo...\n";
    $vehiculo = Vehiculo::create([
        'marca' => $datosFormulario['marca'],
        'modelo' => $datosFormulario['modelo'],
        'anio' => $datosFormulario['anio'],
        'n_serie' => $datosFormulario['n_serie'],
        'placas' => $datosFormulario['placas'],
        'estatus_id' => $datosFormulario['estatus_id'],
        'kilometraje_actual' => $datosFormulario['kilometraje_actual'],
        'observaciones' => $datosFormulario['observaciones']
    ]);
    
    echo "✅ Vehículo creado con ID: {$vehiculo->id}\n";
    
    // Simular procesamiento de documentos
    echo "\n📄 Procesando documentos estructurados...\n";
    
    foreach ($documentosEstructurados as $campoArchivo => $config) {
        if (isset($archivosSimulados[$campoArchivo])) {
            echo "\n🔍 Procesando {$campoArchivo}:\n";
            
            // Buscar tipo de documento
            $tipoDocumento = CatalogoTipoDocumento::where('nombre_tipo_documento', $config['tipo'])->first();
            
            if ($tipoDocumento) {
                echo "  ✅ Tipo de documento encontrado: {$tipoDocumento->nombre_tipo_documento} (ID: {$tipoDocumento->id})\n";
                
                // Preparar contenido
                $contenido = [];
                if ($config['numero_campo'] && isset($datosFormulario[$config['numero_campo']])) {
                    $contenido['numero'] = $datosFormulario[$config['numero_campo']];
                    echo "  📝 Número: {$datosFormulario[$config['numero_campo']]}\n";
                }
                if (isset($config['extra_campo']) && isset($datosFormulario[$config['extra_campo']])) {
                    $contenido['aseguradora'] = $datosFormulario[$config['extra_campo']];
                    echo "  🏢 Aseguradora: {$datosFormulario[$config['extra_campo']]}\n";
                }
                
                // Crear documento
                $documento = Documento::create([
                    'tipo_documento_id' => $tipoDocumento->id,
                    'descripcion' => $config['tipo'] . ' del vehículo',
                    'ruta_archivo' => 'vehiculos/documentos/' . $archivosSimulados[$campoArchivo],
                    'fecha_vencimiento' => $config['fecha_campo'] && isset($datosFormulario[$config['fecha_campo']]) 
                        ? $datosFormulario[$config['fecha_campo']] 
                        : null,
                    'vehiculo_id' => $vehiculo->id,
                    'contenido' => !empty($contenido) ? $contenido : null,
                ]);
                
                echo "  ✅ Documento creado con ID: {$documento->id}\n";
                if ($documento->fecha_vencimiento) {
                    echo "  📅 Fecha de vencimiento: {$documento->fecha_vencimiento}\n";
                }
                if ($documento->contenido) {
                    echo "  📋 Contenido JSON: " . json_encode($documento->contenido) . "\n";
                }
            } else {
                echo "  ❌ Tipo de documento NO encontrado: {$config['tipo']}\n";
            }
        }
    }
    
    DB::commit();
    
    echo "\n🎉 PRUEBA COMPLETADA EXITOSAMENTE\n";
    echo "Vehículo creado: {$vehiculo->placas} (ID: {$vehiculo->id})\n";
    
    // Verificar documentos creados
    $documentosCreados = Documento::where('vehiculo_id', $vehiculo->id)->get();
    echo "Documentos asociados: {$documentosCreados->count()}\n";
    
    foreach ($documentosCreados as $doc) {
        $tipo = CatalogoTipoDocumento::find($doc->tipo_documento_id);
        echo "- {$tipo->nombre_tipo_documento} (ID: {$doc->id})\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

// Limpiar archivos de prueba
echo "\n🧹 Limpiando archivos de prueba...\n";
foreach ($archivosSimulados as $nombreArchivo) {
    if (Storage::disk('public')->exists("vehiculos/documentos/{$nombreArchivo}")) {
        Storage::disk('public')->delete("vehiculos/documentos/{$nombreArchivo}");
        echo "🗑️ Eliminado: {$nombreArchivo}\n";
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";