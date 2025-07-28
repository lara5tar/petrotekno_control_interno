<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Vehiculo;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNÓSTICO FINAL - SIMULACIÓN REAL DEL NAVEGADOR\n";
echo "====================================================\n\n";

// 1. Autenticar usuario
$user = User::first();
Auth::login($user);
echo "✅ Usuario autenticado: {$user->name}\n\n";

// 2. Verificar que el problema no esté en los permisos
echo "🔐 VERIFICANDO PERMISOS:\n";
$tienePermiso = Auth::user()->hasPermission('crear_vehiculos');
echo "   - Permiso 'crear_vehiculos': " . ($tienePermiso ? "✅ SÍ" : "❌ NO") . "\n";

if (!$tienePermiso) {
    echo "\n❌ PROBLEMA ENCONTRADO: El usuario no tiene permisos para crear vehículos\n";
    echo "   Esto explicaría por qué no se procesan los documentos.\n\n";
    
    // Intentar dar permisos al usuario
    echo "🔧 INTENTANDO ASIGNAR PERMISOS:\n";
    try {
        $permiso = \App\Models\Permission::where('nombre_permiso', 'crear_vehiculos')->first();
        if ($permiso) {
            // Verificar si ya tiene el permiso
            $tienePermisoDirecto = $user->permisos()->where('permiso_id', $permiso->id)->exists();
            if (!$tienePermisoDirecto) {
                $user->permisos()->attach($permiso->id);
                echo "   ✅ Permiso asignado directamente al usuario\n";
            } else {
                echo "   ℹ️ El usuario ya tiene el permiso directamente\n";
            }
            
            // Verificar roles
            $roles = $user->roles;
            echo "   📋 Roles del usuario:\n";
            foreach ($roles as $rol) {
                echo "     - {$rol->nombre_rol}\n";
                $permisosRol = $rol->permisos()->where('nombre_permiso', 'crear_vehiculos')->count();
                echo "       * Tiene permiso crear_vehiculos: " . ($permisosRol > 0 ? "SÍ" : "NO") . "\n";
            }
        }
    } catch (\Exception $e) {
        echo "   ❌ Error al asignar permisos: " . $e->getMessage() . "\n";
    }
    
    // Verificar de nuevo
    $tienePermisoAhora = Auth::user()->hasPermission('crear_vehiculos');
    echo "\n   🔄 Permiso después del ajuste: " . ($tienePermisoAhora ? "✅ SÍ" : "❌ NO") . "\n";
}

// 3. Crear un archivo de imagen real para probar
echo "\n📁 CREANDO ARCHIVO DE IMAGEN REAL:\n";
$uploadDir = storage_path('app/test_uploads');
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Crear una imagen PNG simple (1x1 pixel transparente)
$imagePath = $uploadDir . '/test_image.png';
$imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAHGbKdMWwAAAABJRU5ErkJggg==');
file_put_contents($imagePath, $imageData);

$testFile = new UploadedFile(
    $imagePath,
    'test_image.png',
    'image/png',
    null,
    true
);

echo "   - Archivo creado: test_image.png (PNG real)\n";
echo "   - Tamaño: " . filesize($imagePath) . " bytes\n";
echo "   - Tipo MIME: image/png\n";

// 4. Probar solo con un documento para aislar el problema
echo "\n🧪 PRUEBA AISLADA - SOLO TARJETA DE CIRCULACIÓN:\n";

$formData = [
    'marca' => 'HONDA',
    'modelo' => 'CIVIC',
    'anio' => 2023,
    'n_serie' => 'ISOLATED123456789',
    'placas' => 'ISO-001',
    'kilometraje_actual' => 1000,
    'estatus_id' => 1,
    'no_tarjeta_circulacion' => 'TC-ISO-001',
    'fecha_vencimiento_tarjeta' => '2025-12-31'
];

$files = [
    'tarjeta_circulacion_file' => $testFile
];

echo "   Datos del vehículo:\n";
foreach ($formData as $field => $value) {
    echo "     - {$field}: {$value}\n";
}

echo "\n   Archivo enviado:\n";
echo "     - tarjeta_circulacion_file: {$testFile->getClientOriginalName()}\n";

// 5. Simular el request exactamente como lo hace Laravel
echo "\n🌐 SIMULANDO REQUEST REAL:\n";

try {
    // Crear request simulado
    $request = Request::create('/vehiculos', 'POST', $formData, [], $files, [
        'CONTENT_TYPE' => 'multipart/form-data',
        'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    ]);
    
    // Verificar que el archivo está en el request
    $hasFile = $request->hasFile('tarjeta_circulacion_file');
    echo "   - Archivo en request: " . ($hasFile ? "✅ SÍ" : "❌ NO") . "\n";
    
    if ($hasFile) {
        $uploadedFile = $request->file('tarjeta_circulacion_file');
        echo "   - Nombre: {$uploadedFile->getClientOriginalName()}\n";
        echo "   - Tipo MIME: {$uploadedFile->getMimeType()}\n";
        echo "   - Es válido: " . ($uploadedFile->isValid() ? "✅ SÍ" : "❌ NO") . "\n";
    }
    
    // 6. Probar validación manual
    echo "\n✅ PROBANDO VALIDACIÓN MANUAL:\n";
    $rules = [
        'marca' => 'required|string|max:50',
        'modelo' => 'required|string|max:50',
        'anio' => 'required|integer|min:1990|max:2025',
        'n_serie' => 'required|string|max:50|unique:vehiculos,n_serie',
        'placas' => 'required|string|max:20|unique:vehiculos,placas',
        'kilometraje_actual' => 'required|integer|min:0',
        'estatus_id' => 'required|exists:catalogo_estatus,id',
        'tarjeta_circulacion_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'no_tarjeta_circulacion' => 'nullable|string|max:100',
        'fecha_vencimiento_tarjeta' => 'nullable|date'
    ];
    
    $validator = \Validator::make(
        array_merge($formData, $files), 
        $rules
    );
    
    if ($validator->fails()) {
        echo "   ❌ ERRORES DE VALIDACIÓN:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "     - {$error}\n";
        }
    } else {
        echo "   ✅ Validación exitosa\n";
        
        // 7. Probar creación directa del vehículo
        echo "\n🚗 CREANDO VEHÍCULO DIRECTAMENTE:\n";
        
        $vehiculosAntes = Vehiculo::count();
        $documentosAntes = Documento::count();
        
        // Crear vehículo
        $vehiculo = Vehiculo::create($formData);
        echo "   ✅ Vehículo creado (ID: {$vehiculo->id})\n";
        
        // 8. Probar procesamiento de documentos manualmente
        echo "\n📄 PROCESANDO DOCUMENTO MANUALMENTE:\n";
        
        if ($request->hasFile('tarjeta_circulacion_file')) {
            $tipoDocumento = \App\Models\CatalogoTipoDocumento::where('nombre_tipo_documento', 'Tarjeta de Circulación')->first();
            
            if ($tipoDocumento) {
                echo "   ✅ Tipo de documento encontrado: {$tipoDocumento->nombre_tipo_documento} (ID: {$tipoDocumento->id})\n";
                
                // Subir archivo
                $archivo = $request->file('tarjeta_circulacion_file');
                $nombreArchivo = time() . '_tarjeta_circulacion_' . $archivo->getClientOriginalName();
                $rutaArchivo = $archivo->storeAs('vehiculos/documentos', $nombreArchivo, 'public');
                
                echo "   📁 Archivo subido: {$rutaArchivo}\n";
                
                // Crear documento
                $documento = \App\Models\Documento::create([
                    'tipo_documento_id' => $tipoDocumento->id,
                    'descripcion' => 'Tarjeta de Circulación del vehículo',
                    'ruta_archivo' => $rutaArchivo,
                    'fecha_vencimiento' => $request->input('fecha_vencimiento_tarjeta'),
                    'vehiculo_id' => $vehiculo->id,
                    'contenido' => ['numero' => $request->input('no_tarjeta_circulacion')],
                ]);
                
                echo "   ✅ Documento creado (ID: {$documento->id})\n";
                echo "   📋 Contenido: " . json_encode($documento->contenido) . "\n";
                
                $vehiculosDespues = Vehiculo::count();
                $documentosDespues = Documento::count();
                
                echo "\n📊 RESUMEN FINAL:\n";
                echo "   - Vehículos: {$vehiculosAntes} → {$vehiculosDespues} (+" . ($vehiculosDespues - $vehiculosAntes) . ")\n";
                echo "   - Documentos: {$documentosAntes} → {$documentosDespues} (+" . ($documentosDespues - $documentosAntes) . ")\n";
                echo "   ✅ ¡PROCESO COMPLETADO EXITOSAMENTE!\n";
                
            } else {
                echo "   ❌ Tipo de documento 'Tarjeta de Circulación' no encontrado\n";
            }
        } else {
            echo "   ❌ Archivo no encontrado en el request\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 9. Limpiar
echo "\n🧹 LIMPIEZA:\n";
if (file_exists($imagePath)) {
    unlink($imagePath);
    echo "   - Archivo eliminado: test_image.png\n";
}

echo "\n=== DIAGNÓSTICO COMPLETADO ===\n";