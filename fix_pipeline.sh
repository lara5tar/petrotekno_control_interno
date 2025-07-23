#!/bin/bash

echo "=== CORRECCIÓN FINAL CI/CD ==="
echo "Resolviendo problemas de pipeline..."
echo ""

# 1. Aplicar Laravel Pint para corregir estilo de código
echo "🎨 Aplicando Laravel Pint..."
./vendor/bin/pint

# 2. Verificar que todas las migraciones estén en orden correcto
echo ""
echo "🔍 Verificando orden de migraciones..."
ls -la database/migrations/ | grep -E "(mantenimientos|documentos|catalogo_tipos_servicio)"

echo ""
echo "✅ Correcciones aplicadas"
echo "=== FIN CORRECCIÓN ==="
