import { test, expect } from '@playwright/test';

test.describe('Test Corrección Error Dropdown', () => {
    test('verificar que el formulario carga sin errores de null pointer', async ({ page }) => {
        console.log('=== TESTING CORRECCIÓN ERROR DROPDOWN ===');

        // Escuchar errores de consola
        const consoleErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleErrors.push(msg.text());
                console.log(`❌ CONSOLE ERROR: ${msg.text()}`);
            }
        });

        // Escuchar errores de response
        const responseErrors = [];
        page.on('response', response => {
            if (response.status() >= 400) {
                responseErrors.push(`${response.status()} ${response.url()}`);
                console.log(`❌ RESPONSE ERROR: ${response.status()} ${response.url()}`);
            }
        });

        // Login
        console.log('🔐 Iniciando login...');
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('**/home');
        console.log('✅ Login exitoso');

        // Ir al formulario de crear obra
        console.log('📝 Navegando a formulario...');
        await page.goto('http://localhost:8000/obras/create');

        // Esperar a que la página cargue completamente
        await page.waitForSelector('#createObraForm', { timeout: 10000 });
        console.log('✅ Formulario cargado');

        // Verificar que no hay errores
        console.log('🔍 Verificando errores...');

        // Verificar si hay mensajes de error en la página
        const errorMessages = await page.locator('.bg-red-100, .alert-danger').count();
        console.log(`📊 Mensajes de error en página: ${errorMessages}`);

        // Verificar dropdowns importantes
        console.log('🔍 Verificando dropdowns...');

        const encargadoSelect = await page.locator('select[name="encargado_id"]').count();
        console.log(`👥 Dropdown encargado presente: ${encargadoSelect > 0 ? '✅ SÍ' : '❌ NO'}`);

        if (encargadoSelect > 0) {
            const encargadoOptions = await page.locator('select[name="encargado_id"] option').count();
            console.log(`👥 Opciones de encargado: ${encargadoOptions}`);
        }

        // Verificar botón de agregar vehículo
        const addVehicleButton = await page.locator('button:has-text("Agregar Vehículo")').count();
        console.log(`🚗 Botón agregar vehículo: ${addVehicleButton > 0 ? '✅ SÍ' : '❌ NO'}`);

        // Verificar Alpine.js
        const alpineLoaded = await page.evaluate(() => {
            return typeof window.Alpine !== 'undefined';
        });
        console.log(`🔍 Alpine.js cargado: ${alpineLoaded ? '✅ SÍ' : '❌ NO'}`);

        console.log('\n📋 === RESUMEN ===');
        console.log(`📊 Errores de consola: ${consoleErrors.length}`);
        console.log(`📊 Errores de response: ${responseErrors.length}`);
        console.log(`📊 Mensajes de error en página: ${errorMessages}`);
        console.log(`📊 Formulario funcional: ${errorMessages === 0 && consoleErrors.length === 0 && responseErrors.length === 0}`);

        // Si hay errores, mostrarlos
        if (consoleErrors.length > 0) {
            console.log('\n❌ ERRORES DE CONSOLA:');
            consoleErrors.forEach((error, index) => {
                console.log(`${index + 1}. ${error}`);
            });
        }

        if (responseErrors.length > 0) {
            console.log('\n❌ ERRORES DE RESPONSE:');
            responseErrors.forEach((error, index) => {
                console.log(`${index + 1}. ${error}`);
            });
        }

        // El test pasa si no hay errores
        expect(consoleErrors.length).toBe(0);
        expect(responseErrors.length).toBe(0);
        expect(errorMessages).toBe(0);

        console.log('\n🎉 ¡TEST EXITOSO! - Formulario carga sin errores');
    });
});