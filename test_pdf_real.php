<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\StoreVehiculoRequest;
use App\Models\Vehiculo;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 PRUEBA CON ARCHIVOS PDF REALES\n";
echo "=================================\n\n";

// 1. Autenticar usuario
$user = User::first();
Auth::login($user);
echo "✅ Usuario autenticado: {$user->name}\n\n";

// 2. Crear archivos PDF reales usando una librería simple
echo "📁 CREANDO ARCHIVOS PDF REALES:\n";
$uploadDir = storage_path('app/test_uploads');
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function createSimplePDF($content, $filename) {
    $pdf = "%PDF-1.4\n";
    $pdf .= "1 0 obj\n";
    $pdf .= "<<\n";
    $pdf .= "/Type /Catalog\n";
    $pdf .= "/Pages 2 0 R\n";
    $pdf .= ">>\n";
    $pdf .= "endobj\n";
    $pdf .= "2 0 obj\n";
    $pdf .= "<<\n";
    $pdf .= "/Type /Pages\n";
    $pdf .= "/Kids [3 0 R]\n";
    $pdf .= "/Count 1\n";
    $pdf .= ">>\n";
    $pdf .= "endobj\n";
    $pdf .= "3 0 obj\n";
    $pdf .= "<<\n";
    $pdf .= "/Type /Page\n";
    $pdf .= "/Parent 2 0 R\n";
    $pdf .= "/MediaBox [0 0 612 792]\n";
    $pdf .= "/Contents 4 0 R\n";
    $pdf .= ">>\n";
    $pdf .= "endobj\n";
    $pdf .= "4 0 obj\n";
    $pdf .= "<<\n";
    $pdf .= "/Length " . strlen($content) . "\n";
    $pdf .= ">>\n";
    $pdf .= "stream\n";
    $pdf .= "BT\n";
    $pdf .= "/F1 12 Tf\n";
    $pdf .= "100 700 Td\n";
    $pdf .= "($content) Tj\n";
    $pdf .= "ET\n";
    $pdf .= "endstream\n";
    $pdf .= "endobj\n";
    $pdf .= "xref\n";
    $pdf .= "0 5\n";
    $pdf .= "0000000000 65535 f \n";
    $pdf .= "0000000009 65535 n \n";
    $pdf .= "0000000074 65535 n \n";
    $pdf .= "0000000120 65535 n \n";
    $pdf .= "0000000179 65535 n \n";
    $pdf .= "trailer\n";
    $pdf .= "<<\n";
    $pdf .= "/Size 5\n";
    $pdf .= "/Root 1 0 R\n";
    $pdf .= ">>\n";
    $pdf .= "startxref\n";
    $pdf .= "238\n";
    $pdf .= "%%EOF\n";
    
    file_put_contents($filename, $pdf);
}

$testFiles = [];
$documentTypes = [
    'tarjeta_circulacion_file' => 'Tarjeta de Circulación',
    'derecho_vehicular_file' => 'Derecho Vehicular', 
    'poliza_seguro_file' => 'Póliza de Seguro',
    'factura_pedimento_file' => 'Factura de Compra'
];

foreach ($documentTypes as $fieldName => $docType) {
    $fileName = "test_{$fieldName}.pdf";
    $filePath = $uploadDir . '/' . $fileName;
    
    // Crear PDF real
    createSimplePDF("Documento: {$docType} - Fecha: " . date('Y-m-d H:i:s'), $filePath);
    
    $testFiles[$fieldName] = new UploadedFile(
        $filePath,
        $fileName,
        'application/pdf',
        null,
        true
    );
    
    echo "   - {$fieldName}: {$fileName} (PDF real)\n";
}

// 3. Preparar datos del formulario
echo "\n📝 PREPARANDO DATOS DEL FORMULARIO:\n";

$formData = [
    'marca' => 'NISSAN',
    'modelo' => 'FRONTIER',
    'anio' => 2023,
    'n_serie' => 'PDFTEST123456789',
    'placas' => 'PDF-001',
    'kilometraje_actual' => 8000,
    'estatus_id' => 1,
    'no_tarjeta_circulacion' => 'TC-PDF-001',
    'fecha_vencimiento_tarjeta' => '2025-12-31',
    'no_derecho_vehicular' => 'DV-PDF-001',
    'fecha_vencimiento_derecho' => '2025-12-31',
    'no_poliza_seguro' => 'PS-PDF-001',
    'fecha_vencimiento_seguro' => '2025-12-31',
    'aseguradora' => 'SEGUROS PDF TEST',
    'no_factura_pedimento' => 'FP-PDF-001'
];

foreach ($formData as $field => $value) {
    echo "   - {$field}: {$value}\n";
}

// 4. Probar validación con archivos PDF reales
echo "\n✅ PROBANDO VALIDACIÓN CON PDF REALES:\n";
try {
    $validator = \Validator::make(
        array_merge($formData, $testFiles), 
        (new StoreVehiculoRequest())->rules()
    );
    
    if ($validator->fails()) {
        echo "❌ ERRORES DE VALIDACIÓN:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - {$error}\n";
        }
    } else {
        echo "✅ Validación exitosa con archivos PDF\n";
    }
} catch (\Exception $e) {
    echo "❌ Error en validación: " . $e->getMessage() . "\n";
}

// 5. Crear StoreVehiculoRequest real
echo "\n🎮 PROBANDO CON STOREREQUEST REAL:\n";
try {
    // Crear request real
    $storeRequest = StoreVehiculoRequest::create('/vehiculos', 'POST', $formData, [], $testFiles);
    
    // Contar antes
    $vehiculosAntes = Vehiculo::count();
    $documentosAntes = Documento::count();
    
    echo "   Estado antes:\n";
    echo "     - Vehículos: {$vehiculosAntes}\n";
    echo "     - Documentos: {$documentosAntes}\n";
    
    // Llamar al controlador
    $controller = new \App\Http\Controllers\VehiculoController();
    $response = $controller->store($storeRequest);
    
    // Contar después
    $vehiculosDespues = Vehiculo::count();
    $documentosDespues = Documento::count();
    
    echo "\n   Estado después:\n";
    echo "     - Vehículos: {$vehiculosDespues} (+" . ($vehiculosDespues - $vehiculosAntes) . ")\n";
    echo "     - Documentos: {$documentosDespues} (+" . ($documentosDespues - $documentosAntes) . ")\n";
    
    // Verificar el último vehículo creado
    $ultimoVehiculo = Vehiculo::latest()->first();
    if ($ultimoVehiculo && $ultimoVehiculo->placas === 'PDF-001') {
        echo "\n   ✅ Vehículo creado exitosamente:\n";
        echo "     - ID: {$ultimoVehiculo->id}\n";
        echo "     - Placas: {$ultimoVehiculo->placas}\n";
        
        // Verificar documentos asociados
        $documentosVehiculo = Documento::where('vehiculo_id', $ultimoVehiculo->id)->get();
        echo "\n   📄 Documentos asociados: {$documentosVehiculo->count()}\n";
        
        foreach ($documentosVehiculo as $doc) {
            $tipo = \App\Models\CatalogoTipoDocumento::find($doc->tipo_documento_id);
            echo "     - {$tipo->nombre_tipo_documento} (ID: {$doc->id})\n";
            echo "       * Archivo: {$doc->ruta_archivo}\n";
            echo "       * Contenido: " . json_encode($doc->contenido) . "\n";
        }
        
        if ($documentosVehiculo->count() > 0) {
            echo "\n   ✅ ¡DOCUMENTOS CREADOS CORRECTAMENTE!\n";
        } else {
            echo "\n   ❌ NO SE CREARON DOCUMENTOS\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
}

// 6. Limpiar archivos
echo "\n🧹 LIMPIEZA:\n";
foreach ($testFiles as $file) {
    if (file_exists($file->getPathname())) {
        unlink($file->getPathname());
        echo "   - Archivo eliminado: {$file->getClientOriginalName()}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";