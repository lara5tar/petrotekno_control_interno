<?php
// Test completo de filtros de personal
echo "=== VERIFICACIÓN COMPLETA DE FILTROS DE PERSONAL ===" . PHP_EOL . PHP_EOL;

$baseUrl = "http://127.0.0.1:8000";

// 1. Primero hacer login
echo "1. Iniciando sesión..." . PHP_EOL;
$loginUrl = $baseUrl . "/login";

// Obtener el formulario de login para obtener el token CSRF
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$loginPage = curl_exec($ch);
curl_close($ch);

// Extraer token CSRF
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? '';

if (!$csrfToken) {
    preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/', $loginPage, $matches);
    $csrfToken = $matches[1] ?? '';
}

echo "Token CSRF obtenido: " . substr($csrfToken, 0, 10) . "..." . PHP_EOL;

// Hacer login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $csrfToken,
    'email' => 'admin@petrotekno.com',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($loginHttpCode === 200) {
    echo "✅ Login exitoso" . PHP_EOL;
} else {
    echo "❌ Error en login: HTTP $loginHttpCode" . PHP_EOL;
    exit(1);
}

echo PHP_EOL;

// 2. Verificar datos disponibles
echo "2. Verificando datos disponibles..." . PHP_EOL;
$personalUrl = $baseUrl . "/personal";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $personalUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$personalPage = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Error accediendo a personal: HTTP $httpCode" . PHP_EOL;
    exit(1);
}

// Contar filas de personal sin filtros
$filasSinFiltros = substr_count($personalPage, 'hover:bg-gray-50');
echo "Personal total sin filtros: $filasSinFiltros filas" . PHP_EOL;

// Verificar que los selects estén presentes
$tieneSelectEstado = strpos($personalPage, 'name="estatus"') !== false;
$tieneSelectCategoria = strpos($personalPage, 'name="categoria_id"') !== false;

echo "Select de Estado presente: " . ($tieneSelectEstado ? "✅ SÍ" : "❌ NO") . PHP_EOL;
echo "Select de Categoría presente: " . ($tieneSelectCategoria ? "✅ SÍ" : "❌ NO") . PHP_EOL;

echo PHP_EOL;

// 3. Test de filtro por estatus
echo "3. Probando filtro por estatus (activo)..." . PHP_EOL;
$filtroEstatusUrl = $personalUrl . "?estatus=activo";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $filtroEstatusUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $estatusSelected = strpos($response, 'value="activo" selected') !== false;
    $filasConFiltro = substr_count($response, 'hover:bg-gray-50');
    
    echo "URL consultada: $filtroEstatusUrl" . PHP_EOL;
    echo "Estado 'activo' seleccionado: " . ($estatusSelected ? "✅ SÍ" : "❌ NO") . PHP_EOL;
    echo "Filas con filtro: $filasConFiltro" . PHP_EOL;
    
    if ($estatusSelected) {
        echo "✅ FILTRO DE ESTATUS FUNCIONANDO" . PHP_EOL;
    } else {
        echo "❌ FILTRO DE ESTATUS NO FUNCIONANDO" . PHP_EOL;
    }
} else {
    echo "❌ Error HTTP: $httpCode" . PHP_EOL;
}

echo PHP_EOL;

// 4. Test de filtro por categoría
echo "4. Probando filtro por categoría (ID 1 - Admin)..." . PHP_EOL;
$filtroCategoriaUrl = $personalUrl . "?categoria_id=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $filtroCategoriaUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $categoriaSelected = strpos($response, 'value="1" selected') !== false;
    $filasConFiltro = substr_count($response, 'hover:bg-gray-50');
    
    echo "URL consultada: $filtroCategoriaUrl" . PHP_EOL;
    echo "Categoría '1' seleccionada: " . ($categoriaSelected ? "✅ SÍ" : "❌ NO") . PHP_EOL;
    echo "Filas con filtro: $filasConFiltro" . PHP_EOL;
    
    if ($categoriaSelected) {
        echo "✅ FILTRO DE CATEGORÍA FUNCIONANDO" . PHP_EOL;
    } else {
        echo "❌ FILTRO DE CATEGORÍA NO FUNCIONANDO" . PHP_EOL;
    }
} else {
    echo "❌ Error HTTP: $httpCode" . PHP_EOL;
}

echo PHP_EOL;

// 5. Test de filtros combinados
echo "5. Probando filtros combinados (activo + categoría 1)..." . PHP_EOL;
$filtroCombinadoUrl = $personalUrl . "?estatus=activo&categoria_id=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $filtroCombinadoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $estatusSelected = strpos($response, 'value="activo" selected') !== false;
    $categoriaSelected = strpos($response, 'value="1" selected') !== false;
    $filasConFiltro = substr_count($response, 'hover:bg-gray-50');
    
    echo "URL consultada: $filtroCombinadoUrl" . PHP_EOL;
    echo "Estado 'activo' seleccionado: " . ($estatusSelected ? "✅ SÍ" : "❌ NO") . PHP_EOL;
    echo "Categoría '1' seleccionada: " . ($categoriaSelected ? "✅ SÍ" : "❌ NO") . PHP_EOL;
    echo "Filas con filtro: $filasConFiltro" . PHP_EOL;
    
    if ($estatusSelected && $categoriaSelected) {
        echo "✅ FILTROS COMBINADOS FUNCIONANDO" . PHP_EOL;
    } else {
        echo "❌ FILTROS COMBINADOS NO FUNCIONANDO" . PHP_EOL;
    }
} else {
    echo "❌ Error HTTP: $httpCode" . PHP_EOL;
}

echo PHP_EOL;

// 6. Test de búsqueda AJAX
echo "6. Probando búsqueda AJAX..." . PHP_EOL;
$searchUrl = $baseUrl . "/personal/search?q=ad";

// Obtener CSRF token de la página actual
preg_match('/<meta name="csrf-token" content="([^"]+)"/', $personalPage, $matches);
$csrfToken = $matches[1] ?? '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $searchUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: ' . $csrfToken
]);
$searchResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $searchData = json_decode($searchResponse, true);
    if ($searchData && isset($searchData['personal'])) {
        $resultados = count($searchData['personal']);
        echo "✅ BÚSQUEDA AJAX FUNCIONANDO - $resultados resultados encontrados" . PHP_EOL;
        
        if ($resultados > 0) {
            echo "Primer resultado: " . $searchData['personal'][0]['nombre_completo'] . PHP_EOL;
        }
    } else {
        echo "❌ BÚSQUEDA AJAX - Respuesta inválida" . PHP_EOL;
        echo "Respuesta: " . substr($searchResponse, 0, 200) . "..." . PHP_EOL;
    }
} else {
    echo "❌ BÚSQUEDA AJAX - Error HTTP: $httpCode" . PHP_EOL;
}

echo PHP_EOL . "=== RESUMEN DE VERIFICACIÓN ===" . PHP_EOL;
echo "- Acceso a personal: ✅" . PHP_EOL;
echo "- Controles de filtro presentes: " . ($tieneSelectEstado && $tieneSelectCategoria ? "✅" : "❌") . PHP_EOL;
echo "- Personal total disponible: $filasSinFiltros filas" . PHP_EOL;
echo PHP_EOL . "Revisa los resultados arriba para detalles específicos." . PHP_EOL;
?>