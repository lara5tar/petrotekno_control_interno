<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Personal;

echo "=== VERIFICANDO DATOS CREADOS ===\n";

$personal = Personal::with('documentos.tipoDocumento')->find(24);

if ($personal) {
    echo "Personal: {$personal->nombre_completo}\n";
    echo "Documentos: {$personal->documentos->count()}\n";
    
    foreach ($personal->documentos as $doc) {
        echo "  - {$doc->tipoDocumento->nombre_tipo_documento}: {$doc->descripcion}\n";
        echo "    Archivo: {$doc->ruta_archivo}\n";
    }
} else {
    echo "Personal no encontrado\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";