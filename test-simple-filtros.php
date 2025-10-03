<?php
// Test simple de filtros
echo "=== TEST SIMPLE DE FILTROS ===" . PHP_EOL;

// Simular request con parámetros
echo "1. Simulando request con filtros..." . PHP_EOL;

$_GET['estatus'] = 'activo';
$_GET['categoria_id'] = '1';

// Crear request simulado
$request = new \Illuminate\Http\Request($_GET);

echo "Parámetros del request:" . PHP_EOL;
echo "- estatus: " . $request->get('estatus') . PHP_EOL;
echo "- categoria_id: " . $request->get('categoria_id') . PHP_EOL;
echo "- filled estatus: " . ($request->filled('estatus') ? 'true' : 'false') . PHP_EOL;
echo "- filled categoria_id: " . ($request->filled('categoria_id') ? 'true' : 'false') . PHP_EOL;

echo PHP_EOL . "2. Probando query con filtros..." . PHP_EOL;

$query = \App\Models\Personal::with('categoria');

if ($request->filled('categoria_id')) {
    $query->where('categoria_id', $request->categoria_id);
    echo "✅ Filtro categoria_id aplicado" . PHP_EOL;
}

if ($request->filled('estatus')) {
    $query->where('estatus', $request->estatus);
    echo "✅ Filtro estatus aplicado" . PHP_EOL;
}

$resultados = $query->get();
echo "Resultados encontrados: " . $resultados->count() . PHP_EOL;

foreach ($resultados as $p) {
    echo "- " . $p->nombre_completo . " (estatus: " . $p->estatus . ", categoria: " . $p->categoria->nombre_categoria . ")" . PHP_EOL;
}

echo PHP_EOL . "3. Verificando controlador PersonalController..." . PHP_EOL;

// Verificar si el método index existe y es correcto
$controller = new \App\Http\Controllers\PersonalController();
$reflection = new ReflectionMethod($controller, 'index');
echo "Método index existe: ✅" . PHP_EOL;

// Verificar rutas
echo PHP_EOL . "4. Verificando rutas..." . PHP_EOL;
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$personalRoute = $routes->getByName('personal.index');
if ($personalRoute) {
    echo "Ruta personal.index existe: ✅" . PHP_EOL;
    echo "URI: " . $personalRoute->uri() . PHP_EOL;
    echo "Métodos: " . implode(', ', $personalRoute->methods()) . PHP_EOL;
} else {
    echo "❌ Ruta personal.index NO existe" . PHP_EOL;
}

echo PHP_EOL . "=== FIN DEL TEST ===" . PHP_EOL;
?>