<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vehiculo;
use App\Models\Personal;

echo "🧪 Probando relación operador en vehículos...\n\n";

try {
    // 1. Buscar vehículos con operador asignado
    $vehiculosConOperador = Vehiculo::whereNotNull('operador_id')->with('operador')->get();
    
    echo "🚗 Vehículos con operador asignado: {$vehiculosConOperador->count()}\n\n";
    
    foreach ($vehiculosConOperador as $vehiculo) {
        echo "📋 Vehículo: {$vehiculo->marca} {$vehiculo->modelo} (ID: {$vehiculo->id})\n";
        echo "   Placas: {$vehiculo->placas}\n";
        echo "   Operador ID: {$vehiculo->operador_id}\n";
        echo "   Operador: " . ($vehiculo->operador ? $vehiculo->operador->nombre_completo : 'No asignado') . "\n\n";
    }
    
    // 2. Si no hay vehículos con operador, crear uno de prueba
    if ($vehiculosConOperador->count() === 0) {
        echo "ℹ️ No hay vehículos con operador asignado. Creando uno de prueba...\n";
        
        // Buscar un personal activo
        $personal = Personal::activos()->first();
        if (!$personal) {
            echo "❌ No hay personal activo en el sistema\n";
            exit(1);
        }
        
        // Buscar un vehículo sin operador
        $vehiculo = Vehiculo::whereNull('operador_id')->first();
        if (!$vehiculo) {
            echo "❌ No hay vehículos sin operador asignado\n";
            exit(1);
        }
        
        // Asignar operador
        $vehiculo->operador_id = $personal->id;
        $vehiculo->save();
        
        echo "✅ Operador asignado:\n";
        echo "   Vehículo: {$vehiculo->marca} {$vehiculo->modelo} (ID: {$vehiculo->id})\n";
        echo "   Operador: {$personal->nombre_completo} (ID: {$personal->id})\n\n";
    }
    
    // 3. Probar el primer vehículo con operador
    $vehiculoPrueba = Vehiculo::whereNotNull('operador_id')->with('operador')->first();
    
    if ($vehiculoPrueba) {
        echo "🔍 Datos de prueba del vehículo ID: {$vehiculoPrueba->id}\n";
        echo "   operador_id: " . ($vehiculoPrueba->operador_id ?? 'NULL') . "\n";
        echo "   operador->nombre_completo: " . ($vehiculoPrueba->operador->nombre_completo ?? 'No asignado') . "\n";
        echo "   personal_id (campo incorrecto): " . ($vehiculoPrueba->personal_id ?? 'Campo no existe') . "\n\n";
        
        echo "✅ El campo correcto es 'operador_id', no 'personal_id'\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
}
