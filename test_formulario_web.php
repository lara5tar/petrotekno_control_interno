<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\VehiculoController;
use App\Http\Requests\StoreVehiculoRequest;
use App\Models\Vehiculo;
use App\Models\Documento;
use App\Models\CatalogoEstatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 PRUEBA REAL DEL FORMULARIO WEB\n";
echo "=================================\n\n";

// 1. Autenticar usuario
$user = User::first();
if (!$user) {
    echo "❌ No hay usuarios en el sistema\n";
    exit;
}

Auth::login($user);
echo "✅ Usuario autenticado: {$user->name}\n\n";

// 2. Crear archivos de prueba reales
echo "📁 CREANDO ARCHIVOS DE PRUEBA:\n";
$uploadDir = storage_path('app/test_uploads');
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
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
    file_put_contents($filePath, "Contenido de prueba para {$docType} - " . date('Y-m-d H:i:s'));
    
    $testFiles[$fieldName] = new UploadedFile(
        $filePath,
        $fileName,
        'application/pdf',
        null,
        true
    );
    
    echo "   - {$fieldName}: {$fileName}\n";
}

// 3. Preparar datos del formulario exactamente como los envía el navegador
echo "\n📝 PREPARANDO DATOS DEL FORMULARIO:\n";

$formData = [
    'marca' => 'FORD',
    'modelo' => 'F-150',
    'anio' => 2023,
    'n_serie' => 'WEBTEST123456789',
    'placas' => 'WEB-001',
    'kilometraje_actual' => 5000,
    'estatus_id' => 1,
    'no_tarjeta_circulacion' => 'TC-WEB-001',
    'fecha_vencimiento_tarjeta' => '2025-12-31',
    'no_derecho_vehicular' => 'DV-WEB-001',
    'fecha_vencimiento_derecho' => '2025-12-31',
    'no_poliza_seguro' => 'PS-WEB-001',
    'fecha_vencimiento_seguro' => '2025-12-31',
    'aseguradora' => 'SEGUROS WEB TEST',
    'no_factura_pedimento' => 'FP-WEB-001'
];

foreach ($formData as $field => $value) {
    echo "   - {$field}: {$value}\n";
}

// 4. Simular Request HTTP real
echo "\n🌐 SIMULANDO REQUEST HTTP:\n";

// Crear un request simulado
$request = Request::create('/vehiculos', 'POST', $formData, [], $testFiles, [
    'CONTENT_TYPE' => 'multipart/form-data',
    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Test Browser)',
]);

// Verificar que los archivos están en el request
echo "   Archivos en el request:\n";
foreach ($testFiles as $fieldName => $file) {
    $hasFile = $request->hasFile($fieldName);
    echo "     - {$fieldName}: " . ($hasFile ? "✅ SÍ" : "❌ NO") . "\n";
    
    if ($hasFile) {
        $uploadedFile = $request->file($fieldName);
        echo "       * Nombre: {$uploadedFile->getClientOriginalName()}\n";
        echo "       * Tamaño: {$uploadedFile->getSize()} bytes\n";
        echo "       * Tipo: {$uploadedFile->getMimeType()}\n";
    }
}

// 5. Probar validación
echo "\n✅ PROBANDO VALIDACIÓN:\n";
try {
    $storeRequest = new StoreVehiculoRequest();
    $storeRequest->replace($formData);
    $storeRequest->files->replace($testFiles);
    
    $validator = \Validator::make(
        array_merge($formData, $testFiles), 
        $storeRequest->rules()
    );
    
    if ($validator->fails()) {
        echo "❌ ERRORES DE VALIDACIÓN:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - {$error}\n";
        }
    } else {
        echo "✅ Validación exitosa\n";
    }
} catch (\Exception $e) {
    echo "❌ Error en validación: " . $e->getMessage() . "\n";
}

// 6. Probar el controlador directamente
echo "\n🎮 PROBANDO CONTROLADOR:\n";
try {
    $controller = new VehiculoController();
    
    // Contar vehículos y documentos antes
    $vehiculosAntes = Vehiculo::count();
    $documentosAntes = Documento::count();
    
    echo "   Estado antes:\n";
    echo "     - Vehículos: {$vehiculosAntes}\n";
    echo "     - Documentos: {$documentosAntes}\n";
    
    // Llamar al método store
    $response = $controller->store($request);
    
    // Contar después
    $vehiculosDespues = Vehiculo::count();
    $documentosDespues = Documento::count();
    
    echo "\n   Estado después:\n";
    echo "     - Vehículos: {$vehiculosDespues} (+" . ($vehiculosDespues - $vehiculosAntes) . ")\n";
    echo "     - Documentos: {$documentosDespues} (+" . ($documentosDespues - $documentosAntes) . ")\n";
    
    // Verificar el último vehículo creado
    $ultimoVehiculo = Vehiculo::latest()->first();
    if ($ultimoVehiculo && $ultimoVehiculo->placas === 'WEB-001') {
        echo "\n   ✅ Vehículo creado exitosamente:\n";
        echo "     - ID: {$ultimoVehiculo->id}\n";
        echo "     - Placas: {$ultimoVehiculo->placas}\n";
        echo "     - Marca: {$ultimoVehiculo->marca}\n";
        echo "     - Modelo: {$ultimoVehiculo->modelo}\n";
        
        // Verificar documentos asociados
        $documentosVehiculo = Documento::where('vehiculo_id', $ultimoVehiculo->id)->get();
        echo "\n   📄 Documentos asociados: {$documentosVehiculo->count()}\n";
        
        foreach ($documentosVehiculo as $doc) {
            $tipo = \App\Models\CatalogoTipoDocumento::find($doc->tipo_documento_id);
            echo "     - {$tipo->nombre_tipo_documento} (ID: {$doc->id})\n";
            echo "       * Archivo: {$doc->ruta_archivo}\n";
            echo "       * Contenido: " . json_encode($doc->contenido) . "\n";
        }
        
        if ($documentosVehiculo->count() === 0) {
            echo "   ❌ NO SE CREARON DOCUMENTOS - ESTE ES EL PROBLEMA\n";
        }
    }
    
    echo "\n   Respuesta del controlador:\n";
    echo "     - Tipo: " . get_class($response) . "\n";
    if (method_exists($response, 'getStatusCode')) {
        echo "     - Status: " . $response->getStatusCode() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR EN CONTROLADOR: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n";
}

// 7. Limpiar archivos
echo "\n🧹 LIMPIEZA:\n";
foreach ($testFiles as $file) {
    if (file_exists($file->getPathname())) {
        unlink($file->getPathname());
        echo "   - Archivo eliminado: {$file->getClientOriginalName()}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";