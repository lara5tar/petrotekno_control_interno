<?php

// Script para probar el formulario real usando curl

echo "=== PROBANDO FORMULARIO REAL ===\n";

// Crear archivos temporales reales
$tempFiles = [];
$documentTypes = [
    'identificacion_file' => 'identificacion.pdf',
    'curp_file' => 'curp.pdf',
    'rfc_file' => 'rfc.pdf',
    'nss_file' => 'nss.pdf',
    'licencia_file' => 'licencia.pdf',
    'comprobante_file' => 'comprobante.pdf',
    'cv_file' => 'cv.pdf'
];

foreach ($documentTypes as $fieldName => $fileName) {
    $tempPath = sys_get_temp_dir() . '/' . $fileName;
    file_put_contents($tempPath, "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n174\n%%EOF");
    $tempFiles[$fieldName] = $tempPath;
    echo "Archivo creado: $tempPath (" . filesize($tempPath) . " bytes)\n";
}

echo "\n=== OBTENIENDO TOKEN CSRF ===\n";

// Primero obtener el token CSRF del formulario
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/personal/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/cookies.txt');
$response = curl_exec($ch);

if (curl_error($ch)) {
    echo "Error al obtener formulario: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit(1);
}

// Extraer token CSRF
preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/', $response, $matches);
if (!isset($matches[1])) {
    echo "No se pudo obtener el token CSRF\n";
    echo "Respuesta del servidor:\n";
    echo substr($response, 0, 1000) . "...\n";
    curl_close($ch);
    exit(1);
}

$csrfToken = $matches[1];
echo "Token CSRF obtenido: $csrfToken\n";

echo "\n=== ENVIANDO FORMULARIO ===\n";

// Preparar datos del formulario
$postFields = [
    '_token' => $csrfToken,
    'nombre_completo' => 'Test Real Browser',
    'estatus' => 'activo',
    'categoria_personal_id' => '1',
    'crear_usuario' => '1',
    'email_usuario' => 'test.real.browser@example.com',
    'password_type' => 'random',
    'no_identificacion' => 'REAL123456789',
    'curp_numero' => 'REAL031105MTSRRNA2',
    'rfc' => 'REAL031105ABC',
    'nss' => '12345678901',
    'no_licencia' => 'LIC123456',
    'comprobante' => 'Calle Real 123, Colonia Real',
    'cv' => 'Curriculum Vitae Real Test'
];

// Agregar archivos
foreach ($tempFiles as $fieldName => $filePath) {
    $postFields[$fieldName] = new CURLFile($filePath, 'application/pdf', basename($filePath));
}

// Configurar curl para envío
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/personal');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // No seguir redirecciones para ver la respuesta
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "Código HTTP: $httpCode\n";
echo "Headers:\n$headers\n";

if (curl_error($ch)) {
    echo "Error en curl: " . curl_error($ch) . "\n";
} else {
    if ($httpCode == 302) {
        echo "¡ÉXITO! Redirección detectada (formulario procesado correctamente)\n";
        
        // Extraer URL de redirección
        if (preg_match('/Location: (.+)/', $headers, $matches)) {
            echo "Redirigiendo a: " . trim($matches[1]) . "\n";
        }
    } elseif ($httpCode == 200) {
        echo "Respuesta 200 - Verificando si hay errores de validación...\n";
        
        if (strpos($body, 'alert-danger') !== false || strpos($body, 'is-invalid') !== false) {
            echo "ERRORES DE VALIDACIÓN DETECTADOS:\n";
            
            // Extraer mensajes de error
            preg_match_all('/<div[^>]*alert-danger[^>]*>([^<]+)<\/div>/', $body, $errorMatches);
            foreach ($errorMatches[1] as $error) {
                echo "  - " . trim($error) . "\n";
            }
            
            // También buscar errores de campo específicos
            preg_match_all('/<div[^>]*invalid-feedback[^>]*>([^<]+)<\/div>/', $body, $fieldErrorMatches);
            foreach ($fieldErrorMatches[1] as $error) {
                echo "  - " . trim($error) . "\n";
            }
        } else {
            echo "No se detectaron errores obvios en la respuesta\n";
            echo "Primeros 500 caracteres de la respuesta:\n";
            echo substr($body, 0, 500) . "...\n";
        }
    } else {
        echo "Código HTTP inesperado: $httpCode\n";
        echo "Respuesta:\n" . substr($body, 0, 1000) . "...\n";
    }
}

curl_close($ch);

// Limpiar archivos temporales
foreach ($tempFiles as $tempPath) {
    if (file_exists($tempPath)) {
        unlink($tempPath);
    }
}

// Limpiar archivo de cookies
if (file_exists(sys_get_temp_dir() . '/cookies.txt')) {
    unlink(sys_get_temp_dir() . '/cookies.txt');
}

echo "\n=== PRUEBA COMPLETADA ===\n";