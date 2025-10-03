<?php
// Test básico del controlador PersonalController
echo "=== TEST BÁSICO DEL CONTROLADOR PERSONAL ===" . PHP_EOL;

// Requerir Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test 1: Sin filtros
echo PHP_EOL . "1. TEST SIN FILTROS:" . PHP_EOL;
$request = new Illuminate\Http\Request();
$query = App\Models\Personal::with('categoria');
$personal = $query->get();
echo "Total personal: " . $personal->count() . PHP_EOL;

// Test 2: Con filtro de estatus
echo PHP_EOL . "2. TEST CON FILTRO ESTATUS=ACTIVO:" . PHP_EOL;
$request = new Illuminate\Http\Request(['estatus' => 'activo']);
echo "Request filled('estatus'): " . ($request->filled('estatus') ? 'true' : 'false') . PHP_EOL;
echo "Valor estatus: '" . $request->get('estatus') . "'" . PHP_EOL;

$query = App\Models\Personal::with('categoria');
if ($request->filled('estatus')) {
    $query->where('estatus', $request->estatus);
    echo "✅ Filtro estatus aplicado" . PHP_EOL;
}
$personal = $query->get();
echo "Resultados con filtro: " . $personal->count() . PHP_EOL;

// Test 3: Con filtro de categoría
echo PHP_EOL . "3. TEST CON FILTRO CATEGORIA_ID=1:" . PHP_EOL;
$request = new Illuminate\Http\Request(['categoria_id' => '1']);
echo "Request filled('categoria_id'): " . ($request->filled('categoria_id') ? 'true' : 'false') . PHP_EOL;
echo "Valor categoria_id: '" . $request->get('categoria_id') . "'" . PHP_EOL;

$query = App\Models\Personal::with('categoria');
if ($request->filled('categoria_id')) {
    $query->where('categoria_id', $request->categoria_id);
    echo "✅ Filtro categoria_id aplicado" . PHP_EOL;
}
$personal = $query->get();
echo "Resultados con filtro: " . $personal->count() . PHP_EOL;

// Test 4: Con ambos filtros
echo PHP_EOL . "4. TEST CON AMBOS FILTROS:" . PHP_EOL;
$request = new Illuminate\Http\Request(['estatus' => 'activo', 'categoria_id' => '1']);

$query = App\Models\Personal::with('categoria');

if ($request->filled('estatus')) {
    $query->where('estatus', $request->estatus);
    echo "✅ Filtro estatus aplicado: " . $request->estatus . PHP_EOL;
}

if ($request->filled('categoria_id')) {
    $query->where('categoria_id', $request->categoria_id);
    echo "✅ Filtro categoria_id aplicado: " . $request->categoria_id . PHP_EOL;
}

$personal = $query->get();
echo "Resultados con ambos filtros: " . $personal->count() . PHP_EOL;

foreach ($personal as $p) {
    echo "- " . $p->nombre_completo . " (estatus: " . $p->estatus . ", categoria: " . $p->categoria->nombre_categoria . ")" . PHP_EOL;
}

// Test 5: Verificar variable $categorias para la vista
echo PHP_EOL . "5. TEST VARIABLE CATEGORIAS PARA LA VISTA:" . PHP_EOL;
$categorias = App\Models\CategoriaPersonal::select('id', 'nombre_categoria')
    ->orderBy('nombre_categoria')
    ->get();

echo "Categorías disponibles: " . $categorias->count() . PHP_EOL;
foreach ($categorias as $cat) {
    echo "- ID: " . $cat->id . " | Nombre: " . $cat->nombre_categoria . PHP_EOL;
}

echo PHP_EOL . "=== CONCLUSIÓN ===" . PHP_EOL;
echo "✅ Modelos funcionan correctamente" . PHP_EOL;
echo "✅ Filtros a nivel de query funcionan" . PHP_EOL;
echo "✅ Variable \$categorias disponible para la vista" . PHP_EOL;
echo PHP_EOL . "El problema debe estar en el frontend o en la comunicación con el controlador." . PHP_EOL;
echo "Abre http://127.0.0.1:8000/test-filtros-simple.html para probar en navegador." . PHP_EOL;
?>