import { test, expect } from '@playwright/test';

test.describe('Obra Creation Debug', () => {
  test('diagnosticar error al crear obra', async ({ page }) => {
    // Configurar manejo de errores
    page.on('console', msg => {
      console.log(`BROWSER LOG [${msg.type()}]:`, msg.text());
    });

    page.on('pageerror', err => {
      console.log('PAGE ERROR:', err.message);
    });

    page.on('requestfailed', request => {
      console.log('REQUEST FAILED:', request.url(), request.failure()?.errorText);
    });

    try {
      // Ir a la página de login primero
      console.log('Navegando a la página de login...');
      await page.goto('http://localhost:8000/login');

      // Verificar si estamos en la página de login
      const loginForm = await page.locator('form').first();
      if (await loginForm.isVisible()) {
        console.log('Formulario de login encontrado, intentando hacer login...');

        // Intentar hacer login (ajustar estos valores según tu aplicación)
        await page.fill('input[name="email"]', 'admin@test.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');

        // Esperar a ser redirigido
        await page.waitForURL('**/dashboard', { timeout: 5000 }).catch(() => {
          console.log('No se redirigió al dashboard, continuando...');
        });
      }

      // Intentar ir a la página de creación de obras
      console.log('Navegando a la página de creación de obras...');
      await page.goto('http://localhost:8000/obras/create');

      // Esperar un momento para que la página cargue
      await page.waitForTimeout(2000);

      // Capturar el contenido de la página
      const pageContent = await page.content();
      console.log('Contenido de la página (primeros 1000 caracteres):', pageContent.substring(0, 1000));

      // Verificar si hay errores visibles
      const errorMessages = await page.locator('.alert-danger, .error, [class*="error"]').allTextContents();
      if (errorMessages.length > 0) {
        console.log('Errores encontrados en la página:', errorMessages);
      }

      // Verificar el título de la página
      const title = await page.title();
      console.log('Título de la página:', title);

      // Verificar la URL actual
      const currentUrl = page.url();
      console.log('URL actual:', currentUrl);

      // Verificar si hay errores de Laravel/PHP
      const phpErrors = await page.locator('text=/Fatal error|Parse error|Notice:|Warning:|Error:/').allTextContents();
      if (phpErrors.length > 0) {
        console.log('Errores de PHP encontrados:', phpErrors);
      }

      // Verificar si existe el formulario de creación
      const createForm = page.locator('form');
      const formExists = await createForm.count() > 0;
      console.log('Formulario de creación existe:', formExists);

      if (formExists) {
        const formAction = await createForm.getAttribute('action');
        console.log('Action del formulario:', formAction);
      }

      // Tomar screenshot para análisis visual
      await page.screenshot({
        path: 'debug-obra-creation.png',
        fullPage: true
      });
      console.log('Screenshot guardado como debug-obra-creation.png');

    } catch (error) {
      console.log('Error durante el test:', error);

      // Tomar screenshot del error
      await page.screenshot({
        path: 'debug-obra-creation-error.png',
        fullPage: true
      });
      console.log('Screenshot de error guardado como debug-obra-creation-error.png');
    }
  });

  test('verificar acceso directo a obras/create', async ({ page }) => {
    console.log('Verificando acceso directo a /obras/create...');

    page.on('response', response => {
      if (response.status() >= 400) {
        console.log(`HTTP ERROR: ${response.status()} - ${response.url()}`);
      }
    });

    try {
      const response = await page.goto('http://localhost:8000/obras/create');
      console.log('Status de respuesta:', response?.status());

      // Verificar si hay redirección
      const finalUrl = page.url();
      console.log('URL final después de redirección:', finalUrl);

      await page.waitForTimeout(1000);

      // Verificar contenido
      const bodyText = await page.locator('body').textContent();
      console.log('Contenido del body (primeros 500 caracteres):', bodyText?.substring(0, 500));

    } catch (error) {
      console.log('Error en acceso directo:', error);
    }
  });
});