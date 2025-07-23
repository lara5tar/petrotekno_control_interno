#!/bin/bash

echo "=== DIAGNÓSTICO CI/CD PIPELINE ==="
echo "Directorio actual: $(pwd)"
echo "Usuario actual: $(whoami)"
echo "PHP Version: $(php -v | head -n 1)"
echo ""

echo "🔍 Verificando archivos de configuración..."
echo "- pint.json: $(test -f pint.json && echo "✅ Existe" || echo "❌ No existe")"
echo "- phpunit.xml: $(test -f phpunit.xml && echo "✅ Existe" || echo "❌ No existe")"
echo "- phpstan.neon: $(test -f phpstan.neon && echo "✅ Existe" || echo "❌ No existe")"
echo "- composer.json: $(test -f composer.json && echo "✅ Existe" || echo "❌ No existe")"
echo ""

echo "🔍 Verificando vendor..."
echo "- vendor/bin/pint: $(test -f vendor/bin/pint && echo "✅ Existe" || echo "❌ No existe")"
echo "- vendor/bin/phpstan: $(test -f vendor/bin/phpstan && echo "✅ Existe" || echo "❌ No existe")"
echo ""

echo "🔍 Verificando sintaxis de archivos principales..."
for file in app/Models/Asignacion.php app/Http/Controllers/AsignacionController.php app/Http/Requests/TransferirAsignacionRequest.php; do
    if [ -f "$file" ]; then
        php -l "$file" > /dev/null 2>&1
        if [ $? -eq 0 ]; then
            echo "- $file: ✅ Sintaxis OK"
        else
            echo "- $file: ❌ Error de sintaxis"
            php -l "$file"
        fi
    else
        echo "- $file: ❌ No existe"
    fi
done

echo ""
echo "🔍 Verificando sintaxis de pint.json..."
cat pint.json | python -m json.tool > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "- pint.json: ✅ JSON válido"
else
    echo "- pint.json: ❌ JSON inválido"
fi

echo ""
echo "🔍 Intentando ejecutar comandos..."

echo "1. Laravel Pint..."
if ./vendor/bin/pint --test > /tmp/pint_output.txt 2>&1; then
    echo "   ✅ Pint: PASS"
else
    echo "   ❌ Pint: FAIL"
    echo "   Error:"
    cat /tmp/pint_output.txt | head -20
fi

echo ""
echo "2. PHPStan..."
if ./vendor/bin/phpstan analyse --memory-limit=1G --no-progress > /tmp/phpstan_output.txt 2>&1; then
    echo "   ✅ PHPStan: PASS"
else
    echo "   ❌ PHPStan: FAIL"
    echo "   Error:"
    cat /tmp/phpstan_output.txt | head -20
fi

echo ""
echo "3. PHPUnit..."
if php artisan test --stop-on-failure > /tmp/phpunit_output.txt 2>&1; then
    echo "   ✅ PHPUnit: PASS"
else
    echo "   ❌ PHPUnit: FAIL"
    echo "   Error:"
    cat /tmp/phpunit_output.txt | head -20
fi

echo ""
echo "=== FIN DIAGNÓSTICO ==="