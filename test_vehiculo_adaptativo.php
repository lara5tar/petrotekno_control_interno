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
use Illuminate\Support\Facades\Schema;

echo "=== VERIFICACIÓN Y PRUEBA DE VEHÍCULOS CON DOCUMENTOS ===\n\n";

try {
    // Verificar columnas de la tabla vehiculos
    echo "📋 COLUMNAS DISPONIBLES EN LA TABLA VEHICULOS:\n";
    $columnas = Schema::getColumnListing('vehiculos');
    foreach ($columnas as $columna) {
        echo "- $columna\n";
    }
    echo "\n";
    
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
    
    // Crear el vehículo con solo campos básicos
    $placas = 'TEST-' . rand(100, 999);
    
    $vehiculo = new Vehiculo();
    $vehiculo->marca = 'Toyota';
    $vehiculo->modelo = 'Corolla';
    $vehiculo->anio = 2023;
    $vehiculo->placas = $placas;
    $vehiculo->n_serie = '1234567890ABCDEFG';
    $vehiculo->estatus_id = $estatus->id;
    
    // Solo agregar campos de foto si existen en la tabla
    if (in_array('fotografia_vehiculo', $columnas)) {
        $fotoPath = 'vehiculos/fotos/' . $placas . '_vehiculo.svg';
        Storage::disk('public')->put($fotoPath, file_get_contents(__DIR__ . '/test_vehicle_image.svg'));
        $vehiculo->fotografia_vehiculo = $fotoPath;
        echo "📸 Campo fotografia_vehiculo disponible - agregando foto\n";
    } elseif (in_array('foto_frontal', $columnas)) {
        $fotoPath = 'vehiculos/fotos/' . $placas . '_frontal.svg';
        Storage::disk('public')->put($fotoPath, file_get_contents(__DIR__ . '/test_vehicle_image.svg'));
        $vehiculo->foto_frontal = $fotoPath;
        echo "📸 Campo foto_frontal disponible - agregando foto\n";
    } else {
        echo "⚠️ No se encontraron campos de fotografía en la tabla\n";
    }
    
    $vehiculo->save();
    
    echo "✅ VEHÍCULO CREADO EXITOSAMENTE\n";
    echo "ID: " . $vehiculo->id . "\n";
    echo "Marca: " . $vehiculo->marca . "\n";
    echo "Modelo: " . $vehiculo->modelo . "\n";
    echo "Placas: " . $vehiculo->placas . "\n";
    
    if (isset($vehiculo->fotografia_vehiculo)) {
        echo "Fotografía del vehículo: " . $vehiculo->fotografia_vehiculo . "\n";
    } elseif (isset($vehiculo->foto_frontal)) {
        echo "Foto frontal: " . $vehiculo->foto_frontal . "\n";
    }
    echo "\n";
    
    // Crear documentos estructurados
    $documentosCreados = [];
    
    // 1. Tarjeta de Circulación
    $tipoTarjeta = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Tarjeta de Circulación')->first();
    if ($tipoTarjeta) {
        $rutaTarjeta = 'vehiculos/documentos/' . $placas . '_tarjeta_circulacion.txt';
        Storage::disk('public')->put($rutaTarjeta, file_get_contents(__DIR__ . '/test_tarjeta_circulacion.txt'));
        
        $documento = new Documento();
        $documento->tipo_documento_id = $tipoTarjeta->id;
        $documento->vehiculo_id = $vehiculo->id;
        $documento->descripcion = 'Tarjeta de Circulación - TC-123456789';
        $documento->ruta_archivo = $rutaTarjeta;
        $documento->fecha_vencimiento = '2025-01-15';
        
        // Solo agregar contenido si el campo existe
        $columnasDocumentos = Schema::getColumnListing('documentos');
        if (in_array('contenido', $columnasDocumentos)) {
            $documento->contenido = json_encode([
                'numero_documento' => 'TC-123456789',
                'fecha_expedicion' => '2024-01-15'
            ]);
        }
        
        $documento->save();
        $documentosCreados[] = $documento;
    }
    
    // 2. Póliza de Seguro
    $tipoPoliza = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Póliza de Seguro')->first();
    if ($tipoPoliza) {
        $rutaPoliza = 'vehiculos/documentos/' . $placas . '_poliza_seguro.txt';
        Storage::disk('public')->put($rutaPoliza, file_get_contents(__DIR__ . '/test_poliza_seguro.txt'));
        
        $documento = new Documento();
        $documento->tipo_documento_id = $tipoPoliza->id;
        $documento->vehiculo_id = $vehiculo->id;
        $documento->descripcion = 'Póliza de Seguro - POL-987654321';
        $documento->ruta_archivo = $rutaPoliza;
        $documento->fecha_vencimiento = '2025-02-01';
        
        if (in_array('contenido', $columnasDocumentos)) {
            $documento->contenido = json_encode([
                'numero_poliza' => 'POL-987654321',
                'aseguradora' => 'Seguros Generales S.A.',
                'prima_anual' => 15000
            ]);
        }
        
        $documento->save();
        $documentosCreados[] = $documento;
    }
    
    // 3. Factura de Compra
    $tipoFactura = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Factura de Compra')->first();
    if ($tipoFactura) {
        $rutaFactura = 'vehiculos/documentos/' . $placas . '_factura_compra.txt';
        Storage::disk('public')->put($rutaFactura, file_get_contents(__DIR__ . '/test_factura_compra.txt'));
        
        $documento = new Documento();
        $documento->tipo_documento_id = $tipoFactura->id;
        $documento->vehiculo_id = $vehiculo->id;
        $documento->descripcion = 'Factura de Compra - FAC-001234';
        $documento->ruta_archivo = $rutaFactura;
        
        if (in_array('contenido', $columnasDocumentos)) {
            $documento->contenido = json_encode([
                'numero_factura' => 'FAC-001234',
                'vendedor' => 'Distribuidora Automotriz del Norte S.A.',
                'precio_venta' => 350000,
                'iva' => 56000,
                'total' => 406000
            ]);
        }
        
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
        if (isset($documento->contenido)) {
            echo "  Contenido JSON: " . $documento->contenido . "\n";
        }
        echo "\n";
    }
    
    // Verificar archivos en storage
    echo "📁 VERIFICACIÓN DE ARCHIVOS EN STORAGE:\n";
    
    $fotoField = null;
    if (isset($vehiculo->fotografia_vehiculo)) {
        $fotoField = $vehiculo->fotografia_vehiculo;
    } elseif (isset($vehiculo->foto_frontal)) {
        $fotoField = $vehiculo->foto_frontal;
    }
    
    if ($fotoField && Storage::disk('public')->exists($fotoField)) {
        echo "✅ Fotografía del vehículo guardada correctamente: " . $fotoField . "\n";
    } elseif ($fotoField) {
        echo "❌ Fotografía del vehículo NO encontrada: " . $fotoField . "\n";
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
    
    echo "\n🎉 PRUEBA COMPLETADA EXITOSAMENTE\n";
    echo "Se creó el vehículo con placas: {$placas}\n";
    echo "Se guardaron " . count($documentosCreados) . " documentos estructurados\n";
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";