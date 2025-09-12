<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Obtener todos los vehículos con su tipo de activo
$vehiculos = App\Models\Vehiculo::with('tipoActivo')->get();

echo "=== PRUEBA DE RELACIÓN VEHÍCULO - TIPO ACTIVO ===\n";
echo "Total de vehículos: " . $vehiculos->count() . "\n\n";

foreach ($vehiculos as $v) {
    echo "ID: " . $v->id . 
         " | Marca: " . $v->marca . 
         " | Tipo Activo ID: " . $v->tipo_activo_id . 
         " | Tipo Activo: " . ($v->tipoActivo ? $v->tipoActivo->nombre : 'Sin tipo') . "\n";
}

echo "\n=== PRUEBA ESPECÍFICA VEHÍCULO ID 1 ===\n";
$vehiculo = App\Models\Vehiculo::with('tipoActivo')->find(1);
if ($vehiculo) {
    echo "ID: " . $vehiculo->id . "\n";
    echo "Marca: " . $vehiculo->marca . "\n";
    echo "Tipo Activo ID: " . $vehiculo->tipo_activo_id . "\n";
    echo "Tipo Activo: " . ($vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'Sin tipo') . "\n";
} else {
    echo "No se encontró el vehículo con ID 1\n";
}