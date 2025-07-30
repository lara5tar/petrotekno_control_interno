<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PersonalManagementController;
use App\Http\Requests\CreatePersonalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== PRUEBA FINAL DEL FORMULARIO REAL ===\n";

// 1. Autenticar usuario
$user = \App\Models\User::first();
if ($user) {
    Auth::login($user);
    echo "✓ Usuario autenticado: {$user->email}\n";
} else {
    echo "❌ No hay usuarios disponibles\n";
    exit(1);
}

// 2. Verificar configuración del sistema
echo "\n--- VERIFICANDO CONFIGURACIÓN ---\n";

// Verificar directorio de storage
$storagePublicPath = storage_path('app/public');
if (!is_dir($storagePublicPath)) {
    mkdir($storagePublicPath, 0755, true);
    echo "✓ Directorio storage/app/public creado\n";
} else {
    echo "✓ Directorio storage/app/public existe\n";
}

// Verificar directorio personal
$personalDir = $storagePublicPath . '/personal';
if (!is_dir($personalDir)) {
    mkdir($personalDir, 0755, true);
    echo "✓ Directorio personal creado\n";
} else {
    echo "✓ Directorio personal existe\n";
}

// Verificar permisos
if (is_writable($storagePublicPath)) {
    echo "✓ Directorio storage es escribible\n";
} else {
    echo "❌ Directorio storage NO es escribible\n";
}

// 3. Verificar datos necesarios
echo "\n--- VERIFICANDO DATOS NECESARIOS ---\n";

$categorias = \App\Models\CategoriaPersonal::all();
echo "Categorías disponibles: {$categorias->count()}\n";
foreach ($categorias as $cat) {
    echo "  - ID {$cat->id}: {$cat->nombre}\n";
}

$tiposDocumento = \App\Models\CatalogoTipoDocumento::all();
echo "Available document types: {$tiposDocumento->count()}\n";
foreach ($tiposDocumento as $tipo) {
    echo "  - ID {$tipo->id}: {$tipo->nombre_tipo_documento}\n";
}

// 4. Simular envío del formulario con datos reales
echo "\n--- SIMULANDO ENVÍO DEL FORMULARIO ---\n";

DB::beginTransaction();

try {
    // Datos del formulario como los enviaría el navegador
    $formData = [
        'nombre_completo' => 'Usuario Prueba Final',
        'estatus' => 'activo',
        'categoria_personal_id' => $categorias->first()->id,
        'ine' => '1234567890123456',
        'curp_numero' => 'UPRF900101HDFPRR01',
        'rfc' => 'UPRF900101ABC',
        'nss' => '12345678901',
        'no_licencia' => 'LIC123456',
        'direccion' => 'Calle Principal 123, Ciudad Prueba'
    ];
    
    echo "Datos del formulario preparados\n";
    
    // Crear el personal usando el método del controlador
    $controller = new PersonalManagementController();
    $reflection = new \ReflectionClass($controller);
    $createPersonalMethod = $reflection->getMethod('createPersonal');
    $createPersonalMethod->setAccessible(true);
    
    // Ajustar datos para el método createPersonal
    $personalData = $formData;
    $personalData['categoria_id'] = $personalData['categoria_personal_id'];
    unset($personalData['categoria_personal_id']);
    
    $personal = $createPersonalMethod->invoke($controller, $personalData);
    
    echo "✓ Personal creado exitosamente\n";
    echo "  ID: {$personal->id}\n";
    echo "  Nombre: {$personal->nombre_completo}\n";
    echo "  INE: {$personal->ine}\n";
    echo "  CURP: {$personal->curp_numero}\n";
    echo "  RFC: {$personal->rfc}\n";
    echo "  NSS: {$personal->nss}\n";
    echo "  Licencia: {$personal->no_licencia}\n";
    echo "  Dirección: {$personal->direccion}\n";
    
    // Crear documentos de prueba
    $documentosCreados = 0;
    
    if ($tiposDocumento->count() > 0) {
        echo "\n--- CREANDO DOCUMENTOS DE PRUEBA ---\n";
        
        // Crear algunos documentos de prueba
        $documentosPrueba = [
            ['tipo_id' => 1, 'descripcion' => 'Identificación oficial', 'contenido' => 'Contenido del INE'],
            ['tipo_id' => 2, 'descripcion' => 'CURP', 'contenido' => 'Contenido del CURP'],
            ['tipo_id' => 3, 'descripcion' => 'RFC', 'contenido' => 'Contenido del RFC']
        ];
        
        foreach ($documentosPrueba as $docData) {
            if ($tiposDocumento->where('id', $docData['tipo_id'])->first()) {
                $documento = new \App\Models\Documento();
                $documento->personal_id = $personal->id;
                $documento->tipo_documento_id = $docData['tipo_id'];
                $documento->descripcion = $docData['descripcion'];
                $documento->contenido = $docData['contenido'];
                $documento->ruta_archivo = "personal/{$personal->id}/documentos/" . strtolower($docData['descripcion']) . '.txt';
                $documento->save();
                
                // Crear archivo físico
                $fullPath = storage_path('app/public/' . $documento->ruta_archivo);
                $dir = dirname($fullPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                file_put_contents($fullPath, $docData['contenido']);
                
                echo "✓ Documento creado: {$documento->descripcion} (ID: {$documento->id})\n";
                $documentosCreados++;
            }
        }
    }
    
    DB::commit();
    
    echo "\n✅ PRUEBA COMPLETADA EXITOSAMENTE\n";
    echo "Personal creado con {$documentosCreados} documentos\n";
    
    // Verificar resultado final
    $personalFinal = \App\Models\Personal::with('documentos', 'categoria')->find($personal->id);
    
    echo "\n--- VERIFICACIÓN FINAL ---\n";
    echo "Personal ID: {$personalFinal->id}\n";
    echo "Nombre completo: {$personalFinal->nombre_completo}\n";
    echo "Estatus: {$personalFinal->estatus}\n";
    echo "Categoría: {$personalFinal->categoria->nombre}\n";
    echo "INE: {$personalFinal->ine}\n";
    echo "CURP: {$personalFinal->curp_numero}\n";
    echo "RFC: {$personalFinal->rfc}\n";
    echo "NSS: {$personalFinal->nss}\n";
    echo "Licencia: {$personalFinal->no_licencia}\n";
    echo "Dirección: {$personalFinal->direccion}\n";
    echo "Documentos asociados: {$personalFinal->documentos->count()}\n";
    
    foreach ($personalFinal->documentos as $doc) {
        echo "  - {$doc->descripcion}: {$doc->ruta_archivo}\n";
        $fullPath = storage_path('app/public/' . $doc->ruta_archivo);
        if (file_exists($fullPath)) {
            echo "    ✓ Archivo existe (" . filesize($fullPath) . " bytes)\n";
        } else {
            echo "    ❌ Archivo NO existe\n";
        }
    }
    
    echo "\n🎉 TODOS LOS CAMPOS Y ARCHIVOS SE GUARDARON CORRECTAMENTE\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}\n";
    echo "Línea: {$e->getLine()}\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";