import { test, expect } from '@playwright/test';

test.describe('Creación de Obras - Versión Corregida', () => {
    test.beforeEach(async ({ page }) => {
        // Ir a la página de login primero
        await page.goto('/login');

        // Hacer login con credenciales válidas
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        // Esperar a que se complete el login y redirija al dashboard
        await page.waitForURL('/home');
        await expect(page).toHaveURL('/home');
    });

    test('Debería mostrar el formulario de creación de obras correctamente', async ({ page }) => {
        console.log('🎯 Test: Verificando formulario de creación de obras');

        // Navegar a la página de creación de obras
        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Verificar que estamos en la página correcta
        await expect(page).toHaveURL('/obras/create');

        // Usar selector más específico para el h2 del contenido principal
        await expect(page.locator('h2.text-2xl.font-bold.text-gray-800')).toContainText('Agregar Nueva Obra');

        // Verificar elementos principales del formulario
        await expect(page.locator('#nombre_obra')).toBeVisible();
        await expect(page.locator('#estatus')).toBeVisible();
        await expect(page.locator('#fecha_inicio')).toBeVisible();
        await expect(page.locator('#encargado_id')).toBeVisible();

        // Verificar secciones del formulario
        await expect(page.locator('text=Información Básica de la Obra')).toBeVisible();
        await expect(page.locator('text=Vehículos Asignados')).toBeVisible();
        await expect(page.locator('text=Documentos de la Obra')).toBeVisible();

        // Verificar botones de acción con selectores más específicos
        await expect(page.locator('form button[type="submit"]')).toContainText('Crear Obra');
        await expect(page.locator('a[href*="/obras"]').first()).toContainText('Volver al listado');

        console.log('✅ Formulario mostrado correctamente');
    });

    test('Debería crear una obra básica sin vehículos', async ({ page }) => {
        console.log('🎯 Test: Creando obra básica sin vehículos');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Llenar datos básicos de la obra
        await page.fill('#nombre_obra', 'Obra de Prueba Playwright Basic');
        await page.selectOption('#estatus', 'planificada');
        await page.fill('#avance', '0');

        // Configurar fechas
        const fechaInicio = new Date().toISOString().split('T')[0];
        const fechaFin = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

        await page.fill('#fecha_inicio', fechaInicio);
        await page.fill('#fecha_fin', fechaFin);

        // Seleccionar encargado (el primer disponible)
        await page.selectOption('#encargado_id', { index: 1 });

        // Agregar observaciones
        await page.fill('#observaciones', 'Obra de prueba creada con Playwright para testing automatizado');

        // Enviar formulario
        await page.click('form button[type="submit"]');

        // Usar timeout más alto para la redirección
        await page.waitForURL('/obras', { timeout: 10000 });
        await expect(page).toHaveURL('/obras');

        // Verificar mensaje de éxito con selector más específico
        await expect(page.locator('div[role="alert"].bg-green-100')).toContainText('exitosamente');

        // Verificar que la obra aparece en la lista
        await expect(page.locator('text=Obra de Prueba Playwright Basic')).toBeVisible();

        console.log('✅ Obra básica creada exitosamente');
    });

    test('Debería validar campos requeridos', async ({ page }) => {
        console.log('🎯 Test: Validando campos requeridos');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Intentar enviar formulario vacío
        await page.click('form button[type="submit"]');

        // Verificar que permanecemos en la página de creación
        await expect(page).toHaveURL('/obras/create');

        // Verificar validaciones del navegador para campos requeridos
        const nombreObra = page.locator('#nombre_obra');
        const estatus = page.locator('#estatus');
        const fechaInicio = page.locator('#fecha_inicio');
        const encargado = page.locator('#encargado_id');

        // Verificar que los campos tienen el atributo required
        await expect(nombreObra).toHaveAttribute('required');
        await expect(estatus).toHaveAttribute('required');
        await expect(fechaInicio).toHaveAttribute('required');
        await expect(encargado).toHaveAttribute('required');

        console.log('✅ Validaciones de campos requeridos funcionando');
    });

    test('Debería agregar y eliminar vehículos dinámicamente', async ({ page }) => {
        console.log('🎯 Test: Agregando y eliminando vehículos');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Verificar estado inicial (sin vehículos)
        await expect(page.locator('text=No hay vehículos asignados')).toBeVisible();

        // Agregar primer vehículo
        await page.click('button:has-text("Agregar Vehículo")');

        // Verificar que se agregó el vehículo
        await expect(page.locator('.vehicle-card')).toHaveCount(1);

        // Usar selector más específico para el h4 del vehículo
        await expect(page.locator('.vehicle-card h4')).toContainText('Vehículo');

        // Agregar segundo vehículo
        await page.click('button:has-text("Agregar Vehículo")');

        // Verificar que se agregó el segundo vehículo
        await expect(page.locator('.vehicle-card')).toHaveCount(2);

        // Eliminar primer vehículo
        await page.locator('.vehicle-card').first().locator('.remove-vehicle').click();

        // Verificar que se eliminó
        await expect(page.locator('.vehicle-card')).toHaveCount(1);

        // Eliminar último vehículo
        await page.locator('.remove-vehicle').click();

        // Verificar que volvemos al estado inicial
        await expect(page.locator('.vehicle-card')).toHaveCount(0);
        await expect(page.locator('text=No hay vehículos asignados')).toBeVisible();

        console.log('✅ Funcionalidad de vehículos dinámicos trabajando correctamente');
    });

    test('Debería crear obra con vehículos asignados', async ({ page }) => {
        console.log('🎯 Test: Creando obra con vehículos');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Llenar datos básicos
        await page.fill('#nombre_obra', 'Obra con Vehículos - Playwright Test');
        await page.selectOption('#estatus', 'en_progreso');
        await page.fill('#fecha_inicio', new Date().toISOString().split('T')[0]);
        await page.selectOption('#encargado_id', { index: 1 });

        // Agregar vehículo
        await page.click('button:has-text("Agregar Vehículo")');

        // Esperar a que se cargue el select de vehículos
        await page.waitForSelector('.vehicle-select', { timeout: 5000 });

        // Seleccionar vehículo (el primer disponible)
        await page.selectOption('.vehicle-select', { index: 1 });

        // Llenar kilometraje inicial
        await page.fill('input[name*="kilometraje_inicial"]', '15000');

        // Llenar observaciones del vehículo
        await page.fill('textarea[name*="observaciones"]', 'Vehículo asignado para pruebas');

        // Enviar formulario
        await page.click('form button[type="submit"]');

        // Verificar redirección y éxito con timeout más alto
        await page.waitForURL('/obras', { timeout: 15000 });

        // Verificar mensaje de éxito con selector más específico
        await expect(page.locator('div[role="alert"].bg-green-100').first()).toContainText('vehículo');

        console.log('✅ Obra con vehículos creada exitosamente');
    });

    test('Debería manejar subida de archivos correctamente', async ({ page }) => {
        console.log('🎯 Test: Verificando sección de documentos');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Verificar que los inputs de archivo están presentes
        await expect(page.locator('#archivo_contrato')).toBeHidden(); // Input hidden, label visible
        await expect(page.locator('label[for="archivo_contrato"]')).toBeVisible();
        await expect(page.locator('label[for="archivo_fianza"]')).toBeVisible();
        await expect(page.locator('label[for="archivo_acta_entrega_recepcion"]')).toBeVisible();

        // Verificar textos iniciales
        await expect(page.locator('text=PDF, DOC, DOCX (máx. 10MB)')).toHaveCount(3);

        console.log('✅ Sección de documentos configurada correctamente');
    });

    test('Debería validar fechas correctamente', async ({ page }) => {
        console.log('🎯 Test: Validando fechas');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Configurar fecha de fin anterior a fecha de inicio
        const fechaInicio = '2024-12-01';
        const fechaFin = '2024-11-01'; // Anterior a inicio

        await page.fill('#nombre_obra', 'Obra con Fechas Inválidas');
        await page.selectOption('#estatus', 'planificada');
        await page.fill('#fecha_inicio', fechaInicio);
        await page.fill('#fecha_fin', fechaFin);
        await page.selectOption('#encargado_id', { index: 1 });

        // Intentar enviar
        await page.click('form button[type="submit"]');

        // Debería mostrar error de validación con timeout
        await expect(page.locator('.fixed.top-4.right-4')).toContainText('fecha de finalización no puede ser anterior', { timeout: 10000 });

        console.log('✅ Validación de fechas funcionando');
    });

    test('Debería navegar correctamente entre páginas', async ({ page }) => {
        console.log('🎯 Test: Navegación entre páginas');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Verificar breadcrumb con selectores más específicos
        await expect(page.locator('nav[aria-label="Breadcrumb"] a:has-text("Inicio")')).toBeVisible();
        await expect(page.locator('nav[aria-label="Breadcrumb"] a:has-text("Obras")')).toBeVisible();
        await expect(page.locator('nav[aria-label="Breadcrumb"] span:has-text("Agregar Obra")')).toBeVisible();

        // Probar botón "Volver al listado"
        await page.click('a:has-text("Volver al listado")');
        await page.waitForURL('/obras');
        await expect(page).toHaveURL('/obras');

        // Regresar a create
        await page.goto('/obras/create');

        // Probar botón "Cancelar"
        await page.click('a:has-text("Cancelar")');
        await page.waitForURL('/obras');
        await expect(page).toHaveURL('/obras');

        console.log('✅ Navegación funcionando correctamente');
    });

    test('Debería inicializar Alpine.js correctamente', async ({ page }) => {
        console.log('🎯 Test: Verificando Alpine.js');

        await page.goto('/obras/create');
        await page.waitForLoadState('networkidle');

        // Verificar que Alpine.js se inicializó
        const alpineInitialized = await page.evaluate(() => {
            return typeof window.Alpine !== 'undefined';
        });

        expect(alpineInitialized).toBe(true);

        console.log('✅ Alpine.js inicializado correctamente');
    });
});