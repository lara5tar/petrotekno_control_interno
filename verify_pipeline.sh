#!/bin/bash

# Script de verificación del pipeline CI/CD
echo "=== VERIFICACIÓN PIPELINE CI/CD ==="
echo ""

# 1. Laravel Pint
echo "1️⃣ Verificando Laravel Pint..."
./vendor/bin/pint --test
pint_exit_code=$?

echo ""

# 2. PHPStan
echo "2️⃣ Verificando PHPStan..."
./vendor/bin/phpstan analyse --memory-limit=1G --no-progress
phpstan_exit_code=$?

echo ""

# 3. Tests
echo "3️⃣ Ejecutando Tests..."
php artisan test --stop-on-failure
tests_exit_code=$?

echo ""
echo "=== RESUMEN ==="
echo "Laravel Pint: $([ $pint_exit_code -eq 0 ] && echo "✅ PASS" || echo "❌ FAIL")"
echo "PHPStan: $([ $phpstan_exit_code -eq 0 ] && echo "✅ PASS" || echo "❌ FAIL")"
echo "Tests: $([ $tests_exit_code -eq 0 ] && echo "✅ PASS" || echo "❌ FAIL")"

# Exit con el código de error más alto
max_exit_code=$(( pint_exit_code > phpstan_exit_code ? pint_exit_code : phpstan_exit_code ))
max_exit_code=$(( max_exit_code > tests_exit_code ? max_exit_code : tests_exit_code ))
exit $max_exit_code