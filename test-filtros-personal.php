<?php
// Test de filtros de personal
echo "=== PRUEBA DE FILTROS DE PERSONAL ===" . PHP_EOL;

$baseUrl = "http://127.0.0.1:8000/personal";

// 1. Filtro por categoría
echo PHP_EOL . "1. Filtro por categoría (Admin - ID 1):" . PHP_EOL;
$url = $baseUrl . "?categoria_id=1";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml',
    'User-Agent: Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    // Buscar si aparece "selected" en la categoría Admin
    if (strpos($response, 'value="1" selected') !== false) {
        echo "✅ Filtro de categoría aplicado correctamente" . PHP_EOL;
    } else {
        echo "❌ Filtro de categoría NO aplicado" . PHP_EOL;
    }
    
    // Contar cuántas filas de personal aparecen
    $filas = substr_count($response, 'hover:bg-gray-50');
    echo "Filas encontradas: $filas" . PHP_EOL;
} else {
    echo "❌ Error HTTP: $httpCode" . PHP_EOL;
}

// 2. Filtro por estatus
echo PHP_EOL . "2. Filtro por estatus (activo):" . PHP_EOL;
$url = $baseUrl . "?estatus=activo";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml',
    'User-Agent: Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    // Buscar si aparece "selected" en estatus activo
    if (strpos($response, 'value="activo" selected') !== false) {
        echo "✅ Filtro de estatus aplicado correctamente" . PHP_EOL;
    } else {
        echo "❌ Filtro de estatus NO aplicado" . PHP_EOL;
    }
    
    // Contar cuántas filas de personal aparecen
    $filas = substr_count($response, 'hover:bg-gray-50');
    echo "Filas encontradas: $filas" . PHP_EOL;
} else {
    echo "❌ Error HTTP: $httpCode" . PHP_EOL;
}

// 3. Filtro combinado
echo PHP_EOL . "3. Filtro combinado (activo + categoría 1):" . PHP_EOL;
$url = $baseUrl . "?estatus=activo&categoria_id=1";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml',
    'User-Agent: Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $statusSelected = strpos($response, 'value="activo" selected') !== false;
    $categorySelected = strpos($response, 'value="1" selected') !== false;
    
    if ($statusSelected && $categorySelected) {
        echo "✅ Filtros combinados aplicados correctamente" . PHP_EOL;
    } else {
        echo "❌ Filtros combinados NO aplicados (Status: " . ($statusSelected ? 'OK' : 'NO') . ", Category: " . ($categorySelected ? 'OK' : 'NO') . ")" . PHP_EOL;
    }
    
    // Contar cuántas filas de personal aparecen
    $filas = substr_count($response, 'hover:bg-gray-50');
    echo "Filas encontradas: $filas" . PHP_EOL;
} else {
    echo "❌ Error HTTP: $httpCode" . PHP_EOL;
}

echo PHP_EOL . "=== FIN DE PRUEBAS ===" . PHP_EOL;
?>