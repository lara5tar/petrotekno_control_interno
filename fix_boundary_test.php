<?php

$file = '/Users/ederjahirgonzalezbravo/Herd/petrotekno/tests/Feature/MantenimientoBoundaryTest.php';
$content = file_get_contents($file);

// Patrón para encontrar postJson con mantenimientos que no tengan sistema_vehiculo
$pattern = '/(\$response = \$this->postJson\(\'\/api\/mantenimientos\', \[\s*\n(?:[^]]*?)\'tipo_servicio\' => \'[^\']+\',)(\s*\n)/';

$replacement = '$1' . "\n" . '            \'sistema_vehiculo\' => \'motor\',' . '$2';

$newContent = preg_replace($pattern, $replacement, $content);

// Verificar que hubo cambios
if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Archivo actualizado exitosamente\n";
} else {
    echo "No se encontraron coincidencias para actualizar\n";
}
