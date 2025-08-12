import { test, expect } from '@playwright/test';

test.describe('Diagnóstico del Flash del Modal', () => {
    test('detectar el flash del modal al cargar la página', async ({ page }) => {
        // Ir directamente a la página de crear obra
        await page.goto('http://localhost:8000/login');
        await page.fill('input[name="email"]', 'admin@petrotekno.com');
        await page.fill('input[name="password"]', 'password123');
        await page.click('button[type="submit"]');

        console.log('=== INICIANDO DIAGNÓSTICO DEL FLASH DEL MODAL ===');

        // Configurar interceptor para capturar el estado del modal durante la carga
        let modalStates = [];

        // Interceptar todas las evaluaciones del modal
        page.on('console', msg => {
            if (msg.text().includes('Modal')) {
                modalStates.push({
                    timestamp: Date.now(),
                    message: msg.text()
                });
            }
        });

        // Tomar screenshot antes de navegar
        await page.screenshot({ path: 'debug-antes-navegar-crear.png' });

        // Navegar a la página de crear obra y monitorear el modal inmediatamente
        console.log('🔍 Navegando a crear obra...');
        const navigationPromise = page.goto('http://localhost:8000/obras/create');

        // Monitorear el modal cada 10ms durante los primeros 2 segundos
        const modalMonitoring = [];
        const startTime = Date.now();

        for (let i = 0; i < 200; i++) { // 200 * 10ms = 2 segundos
            setTimeout(async () => {
                try {
                    const modalState = await page.evaluate(() => {
                        const modal = document.querySelector('[role="dialog"]');
                        if (!modal) return { exists: false, timestamp: Date.now() };

                        const computedStyle = window.getComputedStyle(modal);
                        return {
                            exists: true,
                            visible: modal.offsetParent !== null,
                            display: computedStyle.display,
                            opacity: computedStyle.opacity,
                            zIndex: computedStyle.zIndex,
                            xShow: modal.getAttribute('x-show'),
                            timestamp: Date.now()
                        };
                    });
                    modalMonitoring.push(modalState);
                } catch (error) {
                    // Ignorar errores durante la navegación
                }
            }, i * 10);
        }

        await navigationPromise;
        await page.waitForLoadState('domcontentloaded');

        // Esperar un poco más para capturar todos los estados
        await page.waitForTimeout(3000);

        // Tomar screenshot después de cargar
        await page.screenshot({ path: 'debug-despues-cargar-crear.png' });

        console.log('=== ESTADOS DEL MODAL DURANTE LA CARGA ===');
        modalMonitoring.forEach((state, index) => {
            if (state.exists) {
                console.log(`${index * 10}ms: display=${state.display}, visible=${state.visible}, opacity=${state.opacity}`);
            }
        });

        // Verificar el estado final del controlador Alpine
        const finalControllerState = await page.evaluate(() => {
            const element = document.querySelector('[x-data="obraFormController()"]');
            if (!element || !element._x_dataStack) return { error: 'Controlador no encontrado' };
            return {
                showVehicleModal: element._x_dataStack[0].showVehicleModal,
                alpineInitialized: typeof Alpine !== 'undefined',
                elementExists: !!element
            };
        });

        console.log('Estado final del controlador:', finalControllerState);

        // Verificar si hay algún estado donde el modal esté visible cuando no debería
        const problematicStates = modalMonitoring.filter(state =>
            state.exists && (state.display === 'block' || state.visible === true)
        );

        console.log('=== ESTADOS PROBLEMÁTICOS DETECTADOS ===');
        problematicStates.forEach((state, index) => {
            console.log(`Estado ${index}:`, state);
        });

        if (problematicStates.length > 0) {
            console.log('🚨 FLASH DETECTADO: El modal se muestra antes de que Alpine.js lo controle');
        } else {
            console.log('✅ NO HAY FLASH: El modal se comporta correctamente');
        }
    });
});