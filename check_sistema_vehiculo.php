<?php

$file = '/Users/ederjahirgonzalezbravo/Herd/petrotekno/tests/Feature/MantenimientoControllerHybridTest.php';
$content = file_get_contents($file);

// Búsqueda manual de todas las instancias que necesitan sistema_vehiculo
preg_match_all('/(\$mantenimientoData.*? = \[.*?\];|\$updateData.*? = \[.*?\];|Mantenimiento::factory.*?create\(\[.*?\]\);)/s', $content, $matches);

echo "Encontramos " . count($matches[0]) . " bloques de datos\n";

foreach ($matches[0] as $i => $match) {
    if (strpos($match, 'sistema_vehiculo') === false && strpos($match, 'tipo_servicio') !== false) {
        echo "FALTA sistema_vehiculo en bloque " . ($i + 1) . ":\n";
        echo substr($match, 0, 200) . "...\n\n";
    }
}

// Buscar específicamente los factory()->count
preg_match_all('/Mantenimiento::factory\(\)->count\(\d+\)->create\(\[.*?\]\);/s', $content, $countMatches);
echo "\nBloques factory()->count: " . count($countMatches[0]) . "\n";

foreach ($countMatches[0] as $i => $match) {
    if (strpos($match, 'sistema_vehiculo') === false) {
        echo "FALTA sistema_vehiculo en count " . ($i + 1) . ":\n";
        echo $match . "\n\n";
    }
}
