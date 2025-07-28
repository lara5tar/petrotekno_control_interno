<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\VehiculoController;
use App\Http\Requests\StoreVehiculoRequest;
use App\Models\Vehiculo;
use App\Models\Documento;
use App\Models\CatalogoTipoDocumento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Crear una request simulada
$request = Request::create('/vehiculos', 'POST', [
    'marca' => 'Toyota',
    'modelo' => 'Corolla',
    'anio' => '2023',
    'placas' => 'TEST-123',
    'serie' => '1234567890ABCDEFG',
    'color' => 'Blanco',
    'estatus_id' => 1,
    'numero_tarjeta_circulacion' => 'TC-123456789',
    'fecha_vencimiento_tarjeta_circulacion' => '2025-01-15',
    'numero_poliza_seguro' => 'POL-987654321',
    'aseguradora' => 'Seguros Generales S.A.',
    'fecha_vencimiento_poliza_seguro' => '2025-02-01',
    'numero_factura_compra' => 'FAC-001234',
]);

// Simular archivos subidos
$fotografiaFile = new UploadedFile(
    __DIR__ . '/test_vehicle_image.svg',
    'vehicle_photo.svg',
    'image/svg+xml',
    null,
    true
);

$tarjetaCirculacionFile = new UploadedFile(
    __DIR__ . '/test_tarjeta_circulacion.txt',
    'tarjeta_circulacion.txt',
    'text/plain',
    null,
    true
);

$polizaSeguroFile = new UploadedFile(
    __DIR__ . '/test_poliza_seguro.txt',
    'poliza_seguro.txt',
    'text/plain',
    null,
    true
);

$facturaCompraFile = new UploadedFile(
    __DIR__ . '/test_factura_compra.txt',
    'factura_compra.txt',
    'text/plain',
    null,
    true
);

$request->files->set('fotografia_file', $fotografiaFile);
$request->files->set('tarjeta_circulacion_file', $tarjetaCirculacionFile);
$request->files->set('poliza_seguro_file', $polizaSeguroFile);
$request->files->set('factura_compra_file', $facturaCompraFile);

echo "=== PRUEBA DE CREACIÓN DE VEHÍCULO CON DOCUMENTOS ===\n\n";

try {
    // Procesar la request
    $response = $kernel->handle($request);
    
    echo "Respuesta HTTP: " . $response->getStatusCode() . "\n";
    
    // Verificar si el vehículo se creó
    $vehiculo = Vehiculo::where('placas', 'TEST-123')->first();
    
    if ($vehiculo) {
        echo "✅ VEHÍCULO CREADO EXITOSAMENTE\n";
        echo "ID: " . $vehiculo->id . "\n";
        echo "Marca: " . $vehiculo->marca . "\n";
        echo "Modelo: " . $vehiculo->modelo . "\n";
        echo "Placas: " . $vehiculo->placas . "\n";
        echo "Foto frontal: " . ($vehiculo->foto_frontal ?? 'No asignada') . "\n\n";
        
        // Verificar documentos
        $documentos = Documento::where('vehiculo_id', $vehiculo->id)->get();
        
        echo "📄 DOCUMENTOS ENCONTRADOS: " . $documentos->count() . "\n";
        
        foreach ($documentos as $documento) {
            $tipoDocumento = CatalogoTipoDocumento::find($documento->tipo_documento_id);
            echo "- " . ($tipoDocumento->nombre_tipo_documento ?? 'Tipo desconocido') . "\n";
            echo "  Archivo: " . $documento->ruta_archivo . "\n";
            echo "  Descripción: " . $documento->descripcion . "\n";
            if ($documento->fecha_vencimiento) {
                echo "  Vencimiento: " . $documento->fecha_vencimiento . "\n";
            }
            echo "\n";
        }
        
        // Verificar archivos en storage
        echo "📁 VERIFICACIÓN DE ARCHIVOS EN STORAGE:\n";
        
        if ($vehiculo->foto_frontal && Storage::disk('public')->exists($vehiculo->foto_frontal)) {
            echo "✅ Fotografía del vehículo guardada correctamente\n";
        } else {
            echo "❌ Fotografía del vehículo NO encontrada\n";
        }
        
        foreach ($documentos as $documento) {
            if (Storage::disk('public')->exists($documento->ruta_archivo)) {
                echo "✅ Documento guardado: " . $documento->ruta_archivo . "\n";
            } else {
                echo "❌ Documento NO encontrado: " . $documento->ruta_archivo . "\n";
            }
        }
        
    } else {
        echo "❌ ERROR: El vehículo no se creó\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";