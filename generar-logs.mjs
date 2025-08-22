import { chromium } from 'playwright';

async function verificarLogsRoles() {
    console.log('🔍 VERIFICANDO LOGS DE ROLES...');

    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        // Login
        await page.goto('http://127.0.0.1:8003/login');
        await page.fill('#email', 'admin@petrotekno.com');
        await page.fill('#password', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        // Ir a editar personal para activar los logs
        console.log('📝 Accediendo a editar personal para generar logs...');
        await page.goto('http://127.0.0.1:8003/personal/3/edit', { waitUntil: 'networkidle' });

        console.log('✅ Página cargada, verificar logs en terminal');

    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        await browser.close();
    }
}

verificarLogsRoles();
