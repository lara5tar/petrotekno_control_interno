<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Personal;

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICANDO ÚLTIMO PERSONAL CREADO ===\n";

$personal = Personal::latest()->first();

if ($personal) {
    echo "Último personal creado: {$personal->nombre_completo} (ID: {$personal->id})\n";
    echo "Documentos asociados: {$personal->documentos->count()}\n";
    
    if ($personal->documentos->count() > 0) {
        echo "\nDocumentos:\n";
        foreach ($personal->documentos as $documento) {
            echo "  - {$documento->tipoDocumento->nombre}: {$documento->descripcion}\n";
            echo "    Archivo: {$documento->ruta_archivo}\n";
        }
    }
} else {
    echo "No se encontró ningún personal en la base de datos.\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";