<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug CatalogoEstatus ===\n";

try {
    // Verificar si el modelo funciona
    $estatusOptions = \App\Models\CatalogoEstatus::select('id', 'nombre_estatus')->get();
    echo "Registros encontrados: " . $estatusOptions->count() . "\n";
    
    foreach ($estatusOptions as $estatus) {
        echo "ID: {$estatus->id}, Nombre: {$estatus->nombre_estatus}\n";
    }
    
    echo "\n=== Verificando controlador ===\n";
    
    // Simular lo que hace el controlador
    $estatusOptions = \App\Models\CatalogoEstatus::select('id', 'nombre_estatus')->get();
    echo "Variable \$estatusOptions creada correctamente\n";
    echo "Tipo: " . gettype($estatusOptions) . "\n";
    echo "Clase: " . get_class($estatusOptions) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}