<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\CatalogoTipoDocumento;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VERIFICANDO TIPOS DE DOCUMENTO\n";
echo "=================================\n\n";

// Verificar si existe "Tenencia Vehicular"
$tenencia = CatalogoTipoDocumento::where('nombre_tipo_documento', 'Tenencia Vehicular')->first();

if ($tenencia) {
    echo "✅ ENCONTRADO: Tenencia Vehicular (ID: {$tenencia->id})\n";
} else {
    echo "❌ NO ENCONTRADO: Tenencia Vehicular\n";
    echo "🔧 Creando tipo de documento 'Tenencia Vehicular'...\n";
    
    $tenencia = CatalogoTipoDocumento::create([
        'nombre_tipo_documento' => 'Tenencia Vehicular',
        'descripcion' => 'Documento de tenencia vehicular o derecho vehicular'
    ]);
    
    echo "✅ CREADO: Tenencia Vehicular (ID: {$tenencia->id})\n";
}

echo "\n📋 TODOS LOS TIPOS DE DOCUMENTO:\n";
$tipos = CatalogoTipoDocumento::orderBy('id')->get();
foreach ($tipos as $tipo) {
    echo "- ID: {$tipo->id}, Nombre: {$tipo->nombre_tipo_documento}\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";