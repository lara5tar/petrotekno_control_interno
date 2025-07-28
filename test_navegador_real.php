<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Documento;
use App\Http\Requests\StoreVehiculoRequest;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🌐 PRUEBA REAL DEL NAVEGADOR - SIMULACIÓN COMPLETA\n";
echo "=================================================\n\n";

try {
    // 1. Autenticar usuario
    echo "🔐 AUTENTICANDO USUARIO:\n";
    $user = User::first();
    if (!$user) {
        throw new Exception("No hay usuarios en la base de datos");
    }
    Auth::login($user);
    echo "   ✅ Usuario autenticado: {$user->email}\n\n";

    // 2. Crear archivo de imagen real
    echo "📁 CREANDO ARCHIVO DE IMAGEN REAL:\n";
    $imagePath = __DIR__ . '/test_navegador_image.png';
    
    // Crear una imagen PNG real usando GD
    $image = imagecreate(100, 100);
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagestring($image, 5, 10, 40, 'TEST', $textColor);
    imagepng($image, $imagePath);
    imagedestroy($image);
    
    echo "   - Archivo creado: test_navegador_image.png\n";
    echo "   - Tamaño: " . filesize($imagePath) . " bytes\n";
    echo "   - Tipo MIME: " . mime_content_type($imagePath) . "\n\n";

    // 3. Crear UploadedFile como lo haría el navegador
    echo "📤 CREANDO UPLOADED FILE COMO EL NAVEGADOR:\n";
    $uploadedFile = new UploadedFile(
        $imagePath,
        'test_navegador_image.png',
        'image/png',
        null,
        true // test mode
    );
    
    echo "   - Nombre original: " . $uploadedFile->getClientOriginalName() . "\n";
    echo "   - Tipo MIME: " . $uploadedFile->getMimeType() . "\n";
    echo "   - Es válido: " . ($uploadedFile->isValid() ? 'SÍ' : 'NO') . "\n\n";

    // 4. Crear request como lo haría Laravel
    echo "🌐 CREANDO REQUEST COMO LARAVEL:\n";
    $timestamp = time();
    $requestData = [
        'marca' => 'TOYOTA',
        'modelo' => 'COROLLA',
        'anio' => '2023',
        'n_serie' => 'NAVEGADOR' . $timestamp,
        'placas' => 'NAV-' . substr($timestamp, -3),
        'kilometraje_actual' => '1500',
        'estatus_id' => '1',
        'no_tarjeta_circulacion' => 'TC-NAV-' . substr($timestamp, -3),
        'fecha_vencimiento_tarjeta' => '2025-12-31',
    ];
    
    $files = [
        'tarjeta_circulacion_file' => $uploadedFile
    ];
    
    $request = Request::create('/vehiculos', 'POST', $requestData, [], $files);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    echo "   - Datos del request: " . json_encode($requestData, JSON_PRETTY_PRINT) . "\n";
    echo "   - Archivos en request: " . count($files) . "\n";
    echo "   - Archivo tarjeta_circulacion_file: " . ($request->hasFile('tarjeta_circulacion_file') ? 'SÍ' : 'NO') . "\n\n";

    // 5. Crear StoreVehiculoRequest manualmente
    echo "📋 CREANDO STORE VEHICULO REQUEST:\n";
    $storeRequest = StoreVehiculoRequest::createFrom($request);
    $storeRequest->setContainer(app());
    $storeRequest->setRedirector(app('redirect'));
    
    // Ejecutar validación
    echo "   - Ejecutando validación...\n";
    try {
        $storeRequest->validateResolved();
        echo "   ✅ Validación exitosa\n\n";
    } catch (Exception $e) {
        echo "   ❌ Error de validación: " . $e->getMessage() . "\n\n";
        throw $e;
    }

    // 6. Contar registros antes
    echo "📊 CONTANDO REGISTROS ANTES:\n";
    $vehiculosAntes = Vehiculo::count();
    $documentosAntes = Documento::count();
    echo "   - Vehículos: {$vehiculosAntes}\n";
    echo "   - Documentos: {$documentosAntes}\n\n";

    // 7. Llamar al controlador
    echo "🚗 LLAMANDO AL CONTROLADOR:\n";
    $controller = new \App\Http\Controllers\VehiculoController();
    
    try {
        $response = $controller->store($storeRequest);
        echo "   ✅ Controlador ejecutado exitosamente\n";
        echo "   - Tipo de respuesta: " . get_class($response) . "\n";
        
        if (method_exists($response, 'getStatusCode')) {
            echo "   - Código de estado: " . $response->getStatusCode() . "\n";
        }
        
        if (method_exists($response, 'getTargetUrl')) {
            echo "   - URL de redirección: " . $response->getTargetUrl() . "\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error en controlador: " . $e->getMessage() . "\n";
        echo "   - Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        throw $e;
    }

    // 8. Contar registros después
    echo "\n📊 CONTANDO REGISTROS DESPUÉS:\n";
    $vehiculosDespues = Vehiculo::count();
    $documentosDespues = Documento::count();
    echo "   - Vehículos: {$vehiculosAntes} → {$vehiculosDespues} (+" . ($vehiculosDespues - $vehiculosAntes) . ")\n";
    echo "   - Documentos: {$documentosAntes} → {$documentosDespues} (+" . ($documentosDespues - $documentosAntes) . ")\n\n";

    // 9. Verificar el último vehículo creado
    echo "🔍 VERIFICANDO ÚLTIMO VEHÍCULO CREADO:\n";
    $ultimoVehiculo = Vehiculo::latest()->first();
    if ($ultimoVehiculo) {
        echo "   - ID: {$ultimoVehiculo->id}\n";
        echo "   - Placas: {$ultimoVehiculo->placas}\n";
        echo "   - Marca: {$ultimoVehiculo->marca}\n";
        echo "   - Modelo: {$ultimoVehiculo->modelo}\n";
        
        // Verificar documentos asociados
        $documentos = $ultimoVehiculo->documentos;
        echo "   - Documentos asociados: " . $documentos->count() . "\n";
        
        foreach ($documentos as $doc) {
            echo "     * {$doc->tipoDocumento->nombre_tipo} (ID: {$doc->id})\n";
            echo "       - Archivo: {$doc->ruta_archivo}\n";
            echo "       - Contenido: " . (is_array($doc->contenido) ? json_encode($doc->contenido) : $doc->contenido) . "\n";
        }
    }

    echo "\n✅ ¡PRUEBA COMPLETADA EXITOSAMENTE!\n";

} catch (Exception $e) {
    echo "\n❌ ERROR EN LA PRUEBA:\n";
    echo "   - Mensaje: " . $e->getMessage() . "\n";
    echo "   - Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   - Trace: " . $e->getTraceAsString() . "\n";
} finally {
    // Limpiar archivos de prueba
    echo "\n🧹 LIMPIEZA:\n";
    if (file_exists($imagePath)) {
        unlink($imagePath);
        echo "   - Archivo eliminado: test_navegador_image.png\n";
    }
}

echo "\n=== PRUEBA NAVEGADOR COMPLETADA ===\n";