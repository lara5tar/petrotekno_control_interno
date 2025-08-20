#!/bin/bash

echo "=== Verificación Manual de Campanita ==="
echo ""

echo "1. Probando método estático directamente:"
php artisan tinker --execute="echo 'Alertas: ' . json_encode(App\Http\Controllers\MantenimientoAlertasController::getEstadisticasAlertas());"

echo ""
echo "2. Probando endpoint de debug:"
curl -s http://127.0.0.1:8003/debug-composer | jq '.'

echo ""
echo "3. Estado del ViewComposer:"
echo "El ViewComposer AlertasComposer está configurado para usar MantenimientoAlertasController::getEstadisticasAlertas()"

echo ""
echo "4. Verificación del layout:"
echo "El archivo resources/views/layouts/app.blade.php utiliza las variables \$alertasCount y \$tieneAlertasUrgentes"

echo ""
echo "=== Resultado esperado en campanita: 8 alertas ==="
