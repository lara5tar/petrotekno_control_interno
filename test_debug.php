<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    // Inicializar la aplicación
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Test básico de conexión DB  
    echo "Probando conexión a base de datos...\n";
    $pdo = DB::connection()->getPdo();
    echo "✓ Conexión DB exitosa\n";
    
    // Test consulta obras
    echo "Probando consulta obras...\n";
    $result = DB::select("SELECT id, nombre_obra FROM obras WHERE fecha_eliminacion IS NULL LIMIT 5");
    echo "✓ Consulta obras exitosa, " . count($result) . " obras encontradas\n";
    
    if (count($result) > 0) {
        echo "Ejemplo de obra: ID=" . $result[0]->id . ", Nombre=" . $result[0]->nombre_obra . "\n";
    }
    
    // Test modelo AsignacionObra
    echo "Probando modelo AsignacionObra...\n";
    $asignaciones = \App\Models\AsignacionObra::count();
    echo "✓ Total asignaciones: " . $asignaciones . "\n";
    
    echo "\n✅ Todas las pruebas pasaron exitosamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
