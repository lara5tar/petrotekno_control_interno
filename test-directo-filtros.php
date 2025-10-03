<?php
// Test simplificado con curl directo
echo "=== TEST DIRECTO DE FILTROS PERSONAL ===" . PHP_EOL;

$baseUrl = "http://127.0.0.1:8000";

// Array de tests
$tests = [
    "Sin filtros" => "/personal",
    "Filtro estatus=activo" => "/personal?estatus=activo",
    "Filtro categoria_id=1" => "/personal?categoria_id=1",
    "Filtros combinados" => "/personal?estatus=activo&categoria_id=1",
    "Test controller debug" => "/test-personal-filtros?estatus=activo&categoria_id=1"
];

foreach ($tests as $nombre => $endpoint) {
    echo PHP_EOL . "🧪 $nombre" . PHP_EOL;
    echo "URL: $baseUrl$endpoint" . PHP_EOL;
    
    $command = "curl -s -w 'HTTP_CODE:%{http_code}' '$baseUrl$endpoint' -b cookies.txt";
    $output = shell_exec($command);
    
    if (preg_match('/HTTP_CODE:(\d+)$/', $output, $matches)) {
        $httpCode = $matches[1];
        $response = str_replace("HTTP_CODE:$httpCode", "", $output);
        
        echo "Status: $httpCode" . PHP_EOL;
        
        if ($httpCode == 200) {
            if (strpos($endpoint, 'test-personal-filtros') !== false) {
                // Es el endpoint de debug, mostrar JSON
                $data = json_decode($response, true);
                if ($data) {
                    echo "✅ Respuesta JSON válida" . PHP_EOL;
                    echo "Filtros aplicados: " . implode(', ', $data['filtros_aplicados'] ?? []) . PHP_EOL;
                    echo "Total resultados: " . ($data['total_resultados'] ?? 0) . PHP_EOL;
                } else {
                    echo "❌ Respuesta JSON inválida" . PHP_EOL;
                }
            } else {
                // Es una página normal, verificar contenido
                if (strpos($response, 'hover:bg-gray-50') !== false) {
                    $filas = substr_count($response, 'hover:bg-gray-50');
                    echo "✅ Página cargada - $filas filas de personal" . PHP_EOL;
                    
                    // Verificar si los filtros están seleccionados
                    if (strpos($endpoint, 'estatus=activo') !== false) {
                        $selected = strpos($response, 'value="activo" selected') !== false;
                        echo "Estatus 'activo' seleccionado: " . ($selected ? "✅" : "❌") . PHP_EOL;
                    }
                    
                    if (strpos($endpoint, 'categoria_id=1') !== false) {
                        $selected = strpos($response, 'value="1" selected') !== false;
                        echo "Categoría '1' seleccionada: " . ($selected ? "✅" : "❌") . PHP_EOL;
                    }
                } else {
                    echo "❌ Página no contiene tabla de personal (posible redirección)" . PHP_EOL;
                }
            }
        } else {
            echo "❌ Error HTTP: $httpCode" . PHP_EOL;
            if ($httpCode == 302) {
                echo "   (Posible redirección - verificar autenticación)" . PHP_EOL;
            }
        }
    } else {
        echo "❌ Error ejecutando curl" . PHP_EOL;
    }
    
    echo str_repeat("-", 50) . PHP_EOL;
}

echo PHP_EOL . "📋 INSTRUCCIONES:" . PHP_EOL;
echo "1. Si ves 'HTTP 302' necesitas autenticarte primero" . PHP_EOL;
echo "2. Ve a: http://127.0.0.1:8000/test-filtros.html" . PHP_EOL;
echo "3. Prueba los enlaces directos" . PHP_EOL;
echo "4. Usa el formulario de prueba manual" . PHP_EOL;
?>