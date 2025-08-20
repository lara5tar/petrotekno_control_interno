import { test, expect } from '@playwright/test';

test.describe('Roles Management - Create Button', () => {
    test.beforeEach(async ({ page }) => {
        // Navegar a la página de login
        await page.goto('http://127.0.0.1:8001/login');

        // Realizar login como administrador
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a que la redirección complete
        await page.waitForURL('**/dashboard');
    });

    test('should display create role button in roles management page', async ({ page }) => {
        // Navegar a la página de gestión de roles
        await page.goto('http://127.0.0.1:8001/admin/roles');

        // Verificar que la página cargó correctamente
        await expect(page).toHaveTitle(/Gestión de Roles/);

        // Verificar que el título de la página esté presente
        await expect(page.locator('h1')).toContainText('Gestión de Roles');

        // Verificar que el botón "Crear Nuevo Rol" esté presente
        const createButton = page.locator('a[href="/admin/roles/create"]');
        await expect(createButton).toBeVisible();

        // Verificar el texto del botón
        await expect(createButton).toContainText('Crear Nuevo Rol');

        // Verificar que el botón tenga las clases CSS correctas
        await expect(createButton).toHaveClass(/bg-gray-600/);
        await expect(createButton).toHaveClass(/hover:bg-gray-700/);

        // Verificar que el icono esté presente
        const icon = createButton.locator('svg');
        await expect(icon).toBeVisible();
    });

    test('should navigate to create role page when button is clicked', async ({ page }) => {
        // Navegar a la página de gestión de roles
        await page.goto('http://127.0.0.1:8001/admin/roles');

        // Hacer clic en el botón "Crear Nuevo Rol"
        const createButton = page.locator('a[href="/admin/roles/create"]');
        await createButton.click();

        // Verificar que navegó a la página de crear rol
        await page.waitForURL('**/admin/roles/create');
        await expect(page).toHaveURL('http://127.0.0.1:8001/admin/roles/create');

        // Verificar que la página de crear rol cargó correctamente
        await expect(page.locator('h1')).toContainText('Crear Nuevo Rol');

        // Verificar que el formulario esté presente
        await expect(page.locator('form')).toBeVisible();

        // Verificar que los campos básicos estén presentes
        await expect(page.locator('input[name="nombre_rol"]')).toBeVisible();
        await expect(page.locator('textarea[name="descripcion"]')).toBeVisible();

        // Verificar que la sección de permisos esté presente
        await expect(page.locator('text=Permisos del Rol')).toBeVisible();

        // Verificar que los botones de selección de permisos estén presentes
        await expect(page.locator('button:has-text("Seleccionar Todos")')).toBeVisible();
        await expect(page.locator('button:has-text("Deseleccionar Todos")')).toBeVisible();
    });

    test('should verify button permissions and visibility', async ({ page }) => {
        // Navegar a la página de gestión de roles
        await page.goto('http://127.0.0.1:8001/admin/roles');

        // El botón debe estar visible para usuarios con permisos de crear_roles
        const createButton = page.locator('a[href="/admin/roles/create"]');

        // Verificar que el botón esté presente y sea clickeable
        await expect(createButton).toBeVisible();
        await expect(createButton).toBeEnabled();

        // Verificar que el botón tenga el hover effect
        await createButton.hover();
        await expect(createButton).toHaveClass(/hover:bg-gray-700/);
    });

    test('should verify button styling and accessibility', async ({ page }) => {
        // Navegar a la página de gestión de roles
        await page.goto('http://127.0.0.1:8001/admin/roles');

        const createButton = page.locator('a[href="/admin/roles/create"]');

        // Verificar estilos CSS
        await expect(createButton).toHaveClass(/bg-gray-600/);
        await expect(createButton).toHaveClass(/text-white/);
        await expect(createButton).toHaveClass(/rounded-lg/);
        await expect(createButton).toHaveClass(/font-medium/);
        await expect(createButton).toHaveClass(/inline-flex/);
        await expect(createButton).toHaveClass(/items-center/);

        // Verificar que el botón tenga el atributo href correcto
        await expect(createButton).toHaveAttribute('href', '/admin/roles/create');

        // Verificar que el icono SVG esté presente y sea accesible
        const icon = createButton.locator('svg');
        await expect(icon).toBeVisible();
        await expect(icon).toHaveClass(/w-5/);
        await expect(icon).toHaveClass(/h-5/);
        await expect(icon).toHaveClass(/mr-2/);
    });

    test('should verify page layout and button position', async ({ page }) => {
        // Navegar a la página de gestión de roles
        await page.goto('http://127.0.0.1:8001/admin/roles');

        // Verificar que el header esté presente
        const header = page.locator('.flex.justify-between.items-center');
        await expect(header).toBeVisible();

        // Verificar que el botón esté en la posición correcta (lado derecho)
        const createButton = page.locator('a[href="/admin/roles/create"]');
        await expect(createButton).toBeVisible();

        // Verificar que el botón esté dentro del header
        const buttonInHeader = header.locator('a[href="/admin/roles/create"]');
        await expect(buttonInHeader).toBeVisible();

        // Verificar que haya una tabla de roles presente
        await expect(page.locator('table')).toBeVisible();

        // Verificar headers de la tabla
        await expect(page.locator('th:has-text("ROL")')).toBeVisible();
        await expect(page.locator('th:has-text("DESCRIPCIÓN")')).toBeVisible();
        await expect(page.locator('th:has-text("PERMISOS")')).toBeVisible();
        await expect(page.locator('th:has-text("USUARIOS")')).toBeVisible();
        await expect(page.locator('th:has-text("ACCIONES")')).toBeVisible();
    });
});
