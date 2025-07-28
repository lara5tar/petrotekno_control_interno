<?php

require_once __DIR__ . '/vendor/autoload.php';

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vehiculo;
use App\Models\Documento;
use App\Models\CatalogoTipoDocumento;
use App\Models\CatalogoEstatus;
use Illuminate\Support\Facades\Storage;

echo "=== PRUEBA DIRECTA DE CREACIÓN DE VEHÍCULO CON DOCUMENTOS ===\n\n";

try {
    // Verificar que existan los catálogos necesarios
    $estatus = CatalogoEstatus::first();
    if (!$estatus) {
        echo "❌ ERROR: No hay estatus disponibles en el catálogo\n";
        exit(1);
    }
    
    $tiposDocumento = CatalogoTipoDocumento::all();
    echo "📋 Tipos de documento disponibles:\n";
    foreach ($tiposDocumento as $tipo) {
        echo "- ID: {$tipo->id}, Nombre: {$tipo->nombre_tipo_documento}\n";
    }
    echo "\n";
    
    // Crear el vehículo
    $vehiculo = new Vehiculo();
    $vehiculo->marca = 'Toyota';
    $vehiculo->modelo = 'Corolla';
    $vehiculo->anio = 2023;
    $vehiculo->placas = 'TEST-' . rand(100, 999);
    $vehiculo->n_serie = '1234567890ABCDEFG';
    $vehiculo->estatus_id = $estatus->id;
    
    // Simular la carga de la fotografía
    $fotoPath = 'vehiculos/fotos/' . $vehiculo->placas . '_vehiculo.svg';
    Storage::disk('public')->put($fotoPath, file_get_contents(__DIR__ . '/test_vehicle_image.svg'));
    $vehiculo->fotografia_vehiculo = $fotoPath;
    
    $vehiculo->save();
    
    echo "✅ VEHÍCULO CREADO EXITOSAMENTE\n";
    echo "ID: " . $vehiculo->id . "\n";
    echo "Marca: " . $vehiculo->marca . "\n";
    echo "Modelo: " . $vehiculo->modelo . "\n";
    echo "Placas: " . $vehiculo->placas . "\n";
    echo "Fotografía del vehículo: " . $vehiculo->fotografia_vehiculo . "\n\n";
    
    // Crear documentos estructurados
    $documentosCreados = [];
    
    // 1. Tarjeta de Circulación
    $tipoTarjeta = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Tarjeta de Circulación')->first();
    if ($tipoTarjeta) {
        $rutaTarjeta = 'vehiculos/documentos/' . $vehiculo->placas . '_tarjeta_circulacion.txt';
        Storage::disk('public')->put($rutaTarjeta, file_get_contents(__DIR__ . '/test_tarjeta_circulacion.txt'));
        
        $documento = new Documento();
        $documento->tipo_documento_id = $tipoTarjeta->id;
        $documento->vehiculo_id = $vehiculo->id;
        $documento->descripcion = 'Tarjeta de Circulación - TC-123456789';
        $documento->ruta_archivo = $rutaTarjeta;
        $documento->fecha_vencimiento = '2025-01-15';
        $documento->contenido = json_encode([
            'numero_documento' => 'TC-123456789',
            'fecha_expedicion' => '2024-01-15'
        ]);
        $documento->save();
        $documentosCreados[] = $documento;
    }
    
    // 2. Póliza de Seguro
    $tipoPoliza = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Póliza de Seguro')->first();
    if ($tipoPoliza) {
        $rutaPoliza = 'vehiculos/documentos/' . $vehiculo->placas . '_poliza_seguro.txt';
        Storage::disk('public')->put($rutaPoliza, file_get_contents(__DIR__ . '/test_poliza_seguro.txt'));
        
        $documento = new Documento();
        $documento->tipo_documento_id = $tipoPoliza->id;
        $documento->vehiculo_id = $vehiculo->id;
        $documento->descripcion = 'Póliza de Seguro - POL-987654321';
        $documento->ruta_archivo = $rutaPoliza;
        $documento->fecha_vencimiento = '2025-02-01';
        $documento->contenido = json_encode([
            'numero_poliza' => 'POL-987654321',
            'aseguradora' => 'Seguros Generales S.A.',
            'prima_anual' => 15000
        ]);
        $documento->save();
        $documentosCreados[] = $documento;
    }
    
    // 3. Factura de Compra
    $tipoFactura = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Factura de Compra')->first();
    if ($tipoFactura) {
        $rutaFactura = 'vehiculos/documentos/' . $vehiculo->placas . '_factura_compra.txt';
        Storage::disk('public')->put($rutaFactura, file_get_contents(__DIR__ . '/test_factura_compra.txt'));
        
        $documento = new Documento();
        $documento->tipo_documento_id = $tipoFactura->id;
        $documento->vehiculo_id = $vehiculo->id;
        $documento->descripcion = 'Factura de Compra - FAC-001234';
        $documento->ruta_archivo = $rutaFactura;
        $documento->contenido = json_encode([
            'numero_factura' => 'FAC-001234',
            'vendedor' => 'Distribuidora Automotriz del Norte S.A.',
            'precio_venta' => 350000,
            'iva' => 56000,
            'total' => 406000
        ]);
        $documento->save();
        $documentosCreados[] = $documento;
    }
    
    echo "📄 DOCUMENTOS CREADOS: " . count($documentosCreados) . "\n\n";
    
    foreach ($documentosCreados as $documento) {
        $tipoDocumento = CatalogoTipoDocumento::find($documento->tipo_documento_id);
        echo "- " . $tipoDocumento->nombre_tipo_documento . "\n";
        echo "  Archivo: " . $documento->ruta_archivo . "\n";
        echo "  Descripción: " . $documento->descripcion . "\n";
        if ($documento->fecha_vencimiento) {
            echo "  Vencimiento: " . $documento->fecha_vencimiento . "\n";
        }
        echo "  Contenido JSON: " . $documento->contenido . "\n";
        echo "\n";
    }
    
    // Verificar archivos en storage
    echo "📁 VERIFICACIÓN DE ARCHIVOS EN STORAGE:\n";
    
    if ($vehiculo->fotografia_vehiculo && Storage::disk('public')->exists($vehiculo->fotografia_vehiculo)) {
        echo "✅ Fotografía del vehículo guardada correctamente: " . $vehiculo->fotografia_vehiculo . "\n";
    } else {
        echo "❌ Fotografía del vehículo NO encontrada: " . $vehiculo->fotografia_vehiculo . "\n";
    }
    
    foreach ($documentosCreados as $documento) {
        if (Storage::disk('public')->exists($documento->ruta_archivo)) {
            echo "✅ Documento guardado: " . $documento->ruta_archivo . "\n";
        } else {
            echo "❌ Documento NO encontrado: " . $documento->ruta_archivo . "\n";
        }
    }
    
    // Verificar en base de datos
    echo "\n🗄️ VERIFICACIÓN EN BASE DE DATOS:\n";
    
    $vehiculoDb = Vehiculo::with('documentos.tipoDocumento')->find($vehiculo->id);
    echo "Vehículo en DB: " . ($vehiculoDb ? 'ENCONTRADO' : 'NO ENCONTRADO') . "\n";
    echo "Documentos asociados: " . $vehiculoDb->documentos->count() . "\n";
    
    foreach ($vehiculoDb->documentos as $doc) {
        echo "- " . $doc->tipoDocumento->nombre_tipo_documento . " (ID: {$doc->id})\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";