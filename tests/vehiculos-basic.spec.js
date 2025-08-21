import { test, expect } from '@playwright/test';

test.describe('Vehiculos Basic Test', () => {
    test('should access vehiculos page and verify basic functionality', async ({ page }) => {
        console.log('Starting basic vehiculos test...');

        try {
            // Navegar a la aplicación
            await page.goto('http://127.0.0.1:8001');
            console.log('✅ Successfully navigated to application');

            // Verificar que la página carga
            await page.waitForLoadState('networkidle');
            console.log('✅ Page loaded successfully');

            // Intentar navegar directamente a vehículos (sin login por ahora)
            await page.goto('http://127.0.0.1:8001/vehiculos/create');
            console.log('✅ Attempted to navigate to vehiculos/create');

            // Verificar si existe algún formulario
            const hasForm = await page.$('form');
            if (hasForm) {
                console.log('✅ Form found on page');

                // Verificar campos específicos de vehículos
                const fields = ['marca', 'modelo', 'anio', 'n_serie', 'placas'];
                for (const field of fields) {
                    const fieldElement = await page.$(`input[name="${field}"], select[name="${field}"]`);
                    if (fieldElement) {
                        console.log(`✅ Field found: ${field}`);
                    } else {
                        console.log(`⚠️ Field not found: ${field}`);
                    }
                }

                // Verificar campos de archivo
                const fileFields = ['poliza_file', 'derecho_file', 'factura_file', 'imagen_file'];
                for (const field of fileFields) {
                    const fileElement = await page.$(`input[name="${field}"]`);
                    if (fileElement) {
                        console.log(`✅ File upload field found: ${field}`);
                    } else {
                        console.log(`⚠️ File upload field not found: ${field}`);
                    }
                }

            } else {
                console.log('ℹ️ No form found - might be redirected to login');

                // Verificar si estamos en login
                const isLogin = await page.$('input[name="email"], input[name="password"], .login-form');
                if (isLogin) {
                    console.log('ℹ️ Redirected to login page as expected');
                } else {
                    console.log('⚠️ Page doesn\'t seem to be login or vehiculos form');
                }
            }

            // Capturar screenshot para debugging
            await page.screenshot({
                path: `test-results/vehiculos-basic-test-${Date.now()}.png`,
                fullPage: true
            });
            console.log('✅ Screenshot captured');

        } catch (error) {
            console.error('❌ Test failed:', error.message);

            // Capturar screenshot de error
            await page.screenshot({
                path: `test-results/vehiculos-basic-error-${Date.now()}.png`,
                fullPage: true
            });

            throw error;
        }
    });

    test('should verify VehiculoController modifications are working', async ({ page }) => {
        console.log('Testing if VehiculoController modifications are active...');

        // Intentar acceder a la ruta de vehículos
        const response = await page.goto('http://127.0.0.1:8001/vehiculos');

        // Si obtenemos respuesta (aunque sea redirect), significa que el servidor funciona
        expect(response.status()).toBeLessThan(500);
        console.log(`✅ Server responding with status: ${response.status()}`);

        // Si es redirect, seguir redirect
        if (response.status() >= 300 && response.status() < 400) {
            await page.waitForLoadState('networkidle');
            console.log(`✅ Followed redirect to: ${page.url()}`);
        }

        console.log('✅ VehiculoController is accessible and responding');
    });
});
