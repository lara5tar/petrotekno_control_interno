<?php

$file = '/Users/ederjahirgonzalezbravo/Herd/petrotekno/tests/Feature/MantenimientoControllerHybridTest.php';
$content = file_get_contents($file);

// Patrones para encontrar y reemplazar
$patterns = [
    // Factory creates sin sistema_vehiculo
    '/(\$mantenimiento = Mantenimiento::factory\(\)->create\(\[\s*\'vehiculo_id\' => \$this->vehiculo->id,\s*\'tipo_servicio\' => \'CORRECTIVO\',)(\s*(?!.*sistema_vehiculo))(.*?\]\);)/s' => '$1$2            \'sistema_vehiculo\' => \'motor\',$2$3',

    // Mantenimiento count creates sin sistema_vehiculo  
    '/(Mantenimiento::factory\(\)->count\(\d+\)->create\(\[\s*\'vehiculo_id\' => \$this->vehiculo->id,\s*\'tipo_servicio\' => \'CORRECTIVO\',)(\s*(?!.*sistema_vehiculo))(.*?\]\);)/s' => '$1$2            \'sistema_vehiculo\' => \'motor\',$2$3',

    // Arrays de datos para POST/PUT sin sistema_vehiculo
    '/(\$mantenimientoData(?:Api|Web)? = \[\s*\'vehiculo_id\' => \$this->vehiculo->id,\s*\'tipo_servicio\' => \'CORRECTIVO\',)(\s*(?!.*sistema_vehiculo))(\s*\'proveedor\')/s' => '$1$2            \'sistema_vehiculo\' => \'motor\',$2$3',

    // UpdateData sin sistema_vehiculo
    '/(\$updateData = \[\s*\'vehiculo_id\' => \$this->vehiculo->id,\s*\'tipo_servicio\' => \'CORRECTIVO\',)(\s*(?!.*sistema_vehiculo))(\s*\'proveedor\')/s' => '$1$2            \'sistema_vehiculo\' => \'transmision\',$2$3',
];

foreach ($patterns as $pattern => $replacement) {
    $content = preg_replace($pattern, $replacement, $content);
}

// Escribir el archivo modificado
file_put_contents($file, $content);

echo "Archivo modificado exitosamente\n";
