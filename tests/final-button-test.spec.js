import { test, expect } from '@playwright/test';

test('VERIFICAR BOTÓN CREAR ROL EN /admin/roles', async ({ page }) => {
    console.log('🚀 INICIANDO VERIFICACIÓN DE BOTÓN CREAR ROL');

    // Login
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[name="email"]', 'admin@petrotekno.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    // Ir a admin/roles
    await page.goto('http://127.0.0.1:8000/admin/roles', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(2000);

    // Screenshot de la página completa
    await page.screenshot({ path: 'FINAL-admin-roles-verification.png', fullPage: true });
    console.log('📸 Screenshot guardado: FINAL-admin-roles-verification.png');

    // Verificar URL
    console.log('🌐 URL actual:', page.url());

    // Buscar el botón específico por ID
    const createButton = page.locator('#btn-crear-rol');
    const isVisible = await createButton.isVisible();
    console.log(`🔍 Botón #btn-crear-rol visible: ${isVisible ? '✅ SÍ' : '❌ NO'}`);

    if (isVisible) {
        const buttonText = await createButton.textContent();
        console.log('📝 Texto del botón:', buttonText);

        const href = await createButton.getAttribute('href');
        console.log('🔗 Href del botón:', href);

        // Verificar que es clickeable
        await expect(createButton).toBeVisible();
        await expect(createButton).toBeEnabled();
        console.log('✅ Botón es clickeable');

        // Test de click
        await createButton.click();
        await page.waitForTimeout(1000);

        const newUrl = page.url();
        console.log('🌐 URL después del click:', newUrl);

        if (newUrl.includes('/admin/roles/create')) {
            console.log('🎉 ¡ÉXITO! El botón funciona correctamente');
        } else {
            console.log('❌ El botón no redirige correctamente');
        }
    } else {
        console.log('❌ BOTÓN NO ENCONTRADO - Verificar HTML');

        // Buscar cualquier enlace que contenga "crear" o "create"
        const anyCreateLink = page.locator('a:has-text("Crear")');
        const count = await anyCreateLink.count();
        console.log(`🔍 Enlaces con "Crear" encontrados: ${count}`);

        if (count > 0) {
            for (let i = 0; i < count; i++) {
                const link = anyCreateLink.nth(i);
                const text = await link.textContent();
                const href = await link.getAttribute('href');
                console.log(`  - Enlace ${i + 1}: "${text}" -> ${href}`);
            }
        }
    }

    // El test debe fallar si no encuentra el botón
    expect(isVisible).toBe(true);
});
