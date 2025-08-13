import { test, expect } from '@playwright/test';

test.describe('Sistema de Login - Petrotekno Control Interno', () => {

    test('Login automático al sistema', async ({ page }) => {
        // Navegar a la página de login
        await page.goto('/login');

        // Esperar a que la página cargue completamente
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en la página de login
        await expect(page).toHaveTitle(/Login|Iniciar Sesión|Petrotekno/);

        // Buscar los campos de usuario y contraseña
        const emailField = page.locator('input[name="email"], input[type="email"], input[id*="email"]').first();
        const passwordField = page.locator('input[name="password"], input[type="password"], input[id*="password"]').first();

        // Verificar que los campos existen
        await expect(emailField).toBeVisible();
        await expect(passwordField).toBeVisible();

        // Credenciales por defecto (puedes cambiarlas según tu sistema)
        const email = 'admin@petrotekno.com';
        const password = 'password';

        // Llenar el formulario de login
        await emailField.fill(email);
        await passwordField.fill(password);

        // Buscar y hacer clic en el botón de login
        const loginButton = page.locator('button[type="submit"], input[type="submit"], button:has-text("Iniciar"), button:has-text("Login"), button:has-text("Entrar")').first();
        await expect(loginButton).toBeVisible();
        await loginButton.click();

        // Esperar a que se complete el login y redirija
        await page.waitForLoadState('networkidle');

        // Verificar que se ha hecho login exitosamente
        // Esto puede ser el dashboard, o cualquier página principal del sistema
        await expect(page.url()).not.toContain('/login');

        // Verificar elementos del dashboard (ajusta según tu interfaz)
        await expect(page.locator('text=Dashboard, text=Panel, text=Bienvenido, text=Inicio')).toBeVisible({ timeout: 10000 });

        console.log('✅ Login exitoso! URL actual:', page.url());

        // Tomar una captura de pantalla del dashboard
        await page.screenshot({ path: 'login-exitoso.png', fullPage: true });
    });

    test('Explorar navegación principal después del login', async ({ page }) => {
        // Hacer login primero
        await page.goto('/login');

        // Usar credenciales por defecto
        await page.fill('input[name="email"], input[type="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"], input[type="password"]', 'password');
        await page.click('button[type="submit"], input[type="submit"]');

        await page.waitForLoadState('networkidle');

        // Explorar el menú principal
        const menuItems = [
            'Vehículos',
            'Obras',
            'Personal',
            'Mantenimientos',
            'Kilometrajes',
            'Asignaciones',
            'Documentos'
        ];

        for (const item of menuItems) {
            const menuLink = page.locator(`a:has-text("${item}"), nav a:has-text("${item}"), .menu a:has-text("${item}")`).first();

            if (await menuLink.isVisible()) {
                console.log(`✅ Encontrado menú: ${item}`);

                // Hacer clic y verificar que navega
                await menuLink.click();
                await page.waitForLoadState('networkidle');

                console.log(`📍 Navegó a: ${page.url()}`);

                // Tomar captura de pantalla de cada sección
                await page.screenshot({ path: `seccion-${item.toLowerCase()}.png` });

                // Volver al dashboard/inicio
                await page.goto('/dashboard');
                await page.waitForLoadState('networkidle');
            }
        }
    });

    test('Verificar acceso a módulo de vehículos', async ({ page }) => {
        // Login
        await page.goto('/login');
        await page.fill('input[name="email"], input[type="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"], input[type="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Navegar a vehículos
        await page.goto('/vehiculos');
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en la página de vehículos
        await expect(page.url()).toContain('/vehiculos');

        // Verificar elementos típicos de la página de vehículos
        await expect(page.locator('text=Vehículos, h1:has-text("Vehículos"), .title:has-text("Vehículos")')).toBeVisible();

        // Buscar botón de agregar vehículo
        const addButton = page.locator('button:has-text("Agregar"), button:has-text("Nuevo"), a:has-text("Crear")').first();
        if (await addButton.isVisible()) {
            console.log('✅ Botón de agregar vehículo encontrado');
        }

        // Buscar tabla o lista de vehículos
        const vehiclesList = page.locator('table, .card, .vehicle-item').first();
        if (await vehiclesList.isVisible()) {
            console.log('✅ Lista de vehículos encontrada');
        }

        await page.screenshot({ path: 'vehiculos-modulo.png', fullPage: true });
    });

    test('Información del sistema y credenciales', async ({ page }) => {
        console.log('🔑 CREDENCIALES POR DEFECTO:');
        console.log('Email: admin@petrotekno.com');
        console.log('Password: password');
        console.log('');
        console.log('🌐 URL BASE: http://localhost:8000');
        console.log('');
        console.log('📝 NOTAS:');
        console.log('- Asegúrate de que el servidor esté corriendo: php artisan serve');
        console.log('- Si las credenciales no funcionan, verifica en la base de datos');
        console.log('- Las capturas de pantalla se guardan en la raíz del proyecto');

        // Intentar obtener información del sistema
        await page.goto('/');
        await page.waitForLoadState('networkidle');

        console.log(`🏠 Página de inicio: ${page.url()}`);

        // Verificar si hay redirección automática al login
        if (page.url().includes('/login')) {
            console.log('🔄 Redirección automática al login detectada');
        }

        await page.screenshot({ path: 'pagina-inicial.png' });
    });
});