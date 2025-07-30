<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\PersonalManagementController;
use App\Http\Requests\CreatePersonalRequest;
use App\Models\Personal;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== PRUEBA DE VALIDACIÓN WEB ===\n";

// 1. Crear archivos de prueba
echo "\n--- CREANDO ARCHIVOS DE PRUEBA ---\n";
$pdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\nxref\n0 2\n0000000000 65535 f \n0000000009 00000 n \ntrailer\n<<\n/Size 2\n/Root 1 0 R\n>>\nstartxref\n50\n%%EOF";

$testFiles = [];
$fileNames = ['ine_web.pdf', 'curp_web.pdf'];

foreach ($fileNames as $fileName) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    file_put_contents($filePath, $pdfContent);
    echo "✓ Archivo creado: {$fileName} (" . filesize($filePath) . " bytes)\n";
}

// 2. Datos del formulario
$formData = [
    'nombre_completo' => 'Test Web Validation',
    'estatus' => 'activo',
    'categoria_personal_id' => 1,
    'ine' => 'WEB123456789',
    'curp_numero' => 'WEB031105MTSRRNA2',
    'direccion' => 'Calle Web 123'
];

echo "\n--- DATOS DEL FORMULARIO ---\n";
foreach ($formData as $key => $value) {
    echo "{$key}: {$value}\n";
}

// 3. Probar validación directa
echo "\n--- PROBANDO VALIDACIÓN DIRECTA ---\n";

try {
    // Crear un request simulado
    $request = Request::create('/personal', 'POST', $formData);
    
    // Crear instancia del FormRequest
    $formRequest = new CreatePersonalRequest();
    
    // Simular el proceso de validación
    $formRequest->setContainer(app());
    $formRequest->setRedirector(app('redirect'));
    
    // Preparar el request
    $formRequest->merge($formData);
    
    echo "Request preparado con datos\n";
    
    // Obtener reglas de validación
    $rules = $formRequest->rules();
    echo "Reglas de validación obtenidas: " . count($rules) . " reglas\n";
    
    // Validar manualmente
    $validator = Validator::make($formData, $rules);
    
    if ($validator->fails()) {
        echo "❌ ERRORES DE VALIDACIÓN:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - {$error}\n";
        }
    } else {
        echo "✅ Validación exitosa\n";
        
        // Probar creación directa con controlador
        echo "\n--- PROBANDO CONTROLADOR DIRECTAMENTE ---\n";
        
        $controller = new PersonalManagementController();
        
        // Simular request validado
        $validatedRequest = new CreatePersonalRequest();
        $validatedRequest->merge($formData);
        
        // Crear un mock del método validated()
        $validatedRequest = new class($formData) extends CreatePersonalRequest {
            private $data;
            
            public function __construct($data) {
                $this->data = $data;
            }
            
            public function validated($key = null, $default = null) {
                if ($key === null) {
                    return $this->data;
                }
                return $this->data[$key] ?? $default;
            }
            
            public function authorize(): bool {
                return true;
            }
        };
        
        try {
            $result = $controller->storeWeb($validatedRequest);
            echo "✅ Controlador ejecutado exitosamente\n";
            
            // Verificar resultado
            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                echo "Resultado: Redirección exitosa\n";
            } else {
                echo "Resultado: " . json_encode($result) . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ ERROR EN CONTROLADOR: {$e->getMessage()}\n";
            echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR EN VALIDACIÓN: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
}

// 4. Verificar último personal creado
echo "\n--- VERIFICANDO ÚLTIMO PERSONAL CREADO ---\n";

try {
    $ultimoPersonal = Personal::with(['documentos.tipoDocumento', 'categoria'])
        ->orderBy('id', 'desc')
        ->first();
    
    if ($ultimoPersonal) {
        echo "Último personal: {$ultimoPersonal->nombre_completo} (ID: {$ultimoPersonal->id})\n";
        echo "Categoría: {$ultimoPersonal->categoria->nombre_categoria}\n";
        echo "Documentos: {$ultimoPersonal->documentos->count()}\n";
        
        foreach ($ultimoPersonal->documentos as $doc) {
            echo "  - {$doc->tipoDocumento->nombre_tipo_documento}: {$doc->descripcion}\n";
            if ($doc->ruta_archivo) {
                $fullPath = storage_path('app/public/' . $doc->ruta_archivo);
                echo "    Archivo: {$doc->ruta_archivo}\n";
                echo "    Existe: " . (file_exists($fullPath) ? 'Sí (' . filesize($fullPath) . ' bytes)' : 'No') . "\n";
            }
        }
    } else {
        echo "No se encontró ningún personal en la base de datos\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR AL VERIFICAR: {$e->getMessage()}\n";
}

// Limpiar archivos temporales
echo "\n--- LIMPIANDO ARCHIVOS TEMPORALES ---\n";
foreach ($fileNames as $fileName) {
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
        echo "✓ Archivo temporal eliminado: {$fileName}\n";
    }
}

echo "\n=== PRUEBA COMPLETADA ===\n";