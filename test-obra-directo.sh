#!/bin/bash

echo "🔧 Probando creación de obra directamente con artisan tinker..."

php artisan tinker --execute="
try {
    \$obra = App\Models\Obra::create([
        'nombre_obra' => 'Test Obra ' . now(),
        'estatus' => 'en_progreso',
        'avance' => 50,
        'fecha_inicio' => '2025-08-21',
        'fecha_fin' => '2025-08-31',
        'observaciones' => 'Test de observaciones funcionando',
        'encargado_id' => 1
    ]);
    echo '✅ ÉXITO: Obra creada con ID: ' . \$obra->id;
    echo 'Observaciones: ' . \$obra->observaciones;
} catch (Exception \$e) {
    echo '❌ ERROR: ' . \$e->getMessage();
}
"
