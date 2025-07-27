#!/bin/bash

echo "=== DIAGNÓSTICO LOCAL CI/CD ==="
echo "Directorio: $(pwd)"
echo "Timestamp: $(date)"
echo ""

echo "🔍 1. Laravel Pint..."
echo "------"
if ./vendor/bin/pint --test; then
    echo "✅ PINT: PASS"
else
    echo "❌ PINT: FAIL - Aplicando correcciones..."
    ./vendor/bin/pint
    echo "✅ PINT: Correcciones aplicadas"
fi

echo ""
echo "🔍 2. PHPStan..."
echo "------"
if ./vendor/bin/phpstan analyse --memory-limit=1G --no-progress; then
    echo "✅ PHPSTAN: PASS"
else
    echo "❌ PHPSTAN: FAIL"
fi

echo ""
echo "🔍 3. Migraciones..."
echo "------"
if php artisan migrate:fresh --env=testing; then
    echo "✅ MIGRATIONS: PASS"
else
    echo "❌ MIGRATIONS: FAIL"
fi

echo ""
echo "🔍 4. Tests..."
echo "------"
if php artisan test --stop-on-failure; then
    echo "✅ TESTS: PASS"
else
    echo "❌ TESTS: FAIL"
fi

echo ""
echo "=== FIN DIAGNÓSTICO ==="
