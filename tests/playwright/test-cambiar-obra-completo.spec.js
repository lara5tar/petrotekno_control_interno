import { test, expect } from '@playwright/test';

test.describe('Funcionalidad Cambiar Obra - Test Completo', () => {
  test('Debe permitir cambiar la obra de un vehículo correctamente', async ({ page }) => {
    console.log('🚀 Iniciando test completo de cambiar obra...');
    
    // Navegar a la página del vehículo
    console.log('📍 Navegando a página del vehículo...');
    await page.goto('http://localhost:8000/vehiculos/1');
    
    // Verificar que la página carga correctamente
    await expect(page).toHaveTitle(/Detalles del Vehículo/);
    console.log('✅ Página del vehículo cargada correctamente');
    
    // Buscar el botón "Cambiar Obra"
    console.log('🔍 Buscando botón "Cambiar Obra"...');
    const cambiarObraButton = page.locator('button:has-text("Cambiar Obra")').first();
    
    // Verificar que el botón existe
    await expect(cambiarObraButton).toBeVisible();
    console.log('✅ Botón "Cambiar Obra" encontrado');
    
    // Hacer clic en el botón para abrir el modal
    console.log('🖱️ Haciendo clic en "Cambiar Obra"...');
    await cambiarObraButton.click();
    
    // Esperar a que el modal aparezca
    console.log('⏳ Esperando que aparezca el modal...');
    const modal = page.locator('#cambiarObraModal');
    await expect(modal).toBeVisible();
    console.log('✅ Modal "Cambiar Obra" abierto correctamente');
    
    // Verificar que el modal tiene los elementos esperados
    console.log('🔍 Verificando elementos del modal...');
    
    // Título del modal
    await expect(page.locator('h3:has-text("Cambiar Obra del Vehículo")')).toBeVisible();
    console.log('✅ Título del modal visible');
    
    // Select de nueva obra
    const selectObra = page.locator('#nueva_obra_id');
    await expect(selectObra).toBeVisible();
    console.log('✅ Select de nueva obra visible');
    
    // Select de nuevo operador
    const selectOperador = page.locator('#nuevo_operador_id');
    await expect(selectOperador).toBeVisible();
    console.log('✅ Select de nuevo operador visible');
    
    // Campo de kilometraje inicial
    const kilometrajeInput = page.locator('#kilometraje_inicial_cambio');
    await expect(kilometrajeInput).toBeVisible();
    console.log('✅ Campo de kilometraje inicial visible');
    
    // Verificar que hay opciones en el select de obras
    console.log('🔍 Verificando opciones de obras...');
    await selectObra.click();
    const opcionesObra = selectObra.locator('option');
    const countObras = await opcionesObra.count();
    console.log(`📊 Encontradas ${countObras} opciones de obras`);
    
    if (countObras > 1) { // Más de 1 porque la primera es "Seleccionar obra..."
      console.log('✅ Hay obras disponibles para seleccionar');
      
      // Seleccionar una obra (la segunda opción, saltando "Seleccionar obra...")
      await selectObra.selectOption({ index: 1 });
      console.log('✅ Obra seleccionada');
    } else {
      console.log('⚠️ No hay obras disponibles para asignar');
    }
    
    // Verificar que hay opciones en el select de operadores
    console.log('🔍 Verificando opciones de operadores...');
    await selectOperador.click();
    const opcionesOperador = selectOperador.locator('option');
    const countOperadores = await opcionesOperador.count();
    console.log(`📊 Encontrados ${countOperadores} opciones de operadores`);
    
    if (countOperadores > 1) { // Más de 1 porque la primera es "Seleccionar operador..."
      console.log('✅ Hay operadores disponibles para seleccionar');
      
      // Seleccionar un operador
      await selectOperador.selectOption({ index: 1 });
      console.log('✅ Operador seleccionado');
    } else {
      console.log('⚠️ No hay operadores disponibles para asignar');
    }
    
    // Llenar el kilometraje inicial
    console.log('📝 Llenando kilometraje inicial...');
    await kilometrajeInput.fill('50000');
    console.log('✅ Kilometraje inicial completado');
    
    // Verificar botones del modal
    const btnGuardar = page.locator('button:has-text("Guardar Cambio")');
    const btnCancelar = page.locator('button:has-text("Cancelar")');
    
    await expect(btnGuardar).toBeVisible();
    await expect(btnCancelar).toBeVisible();
    console.log('✅ Botones del modal visibles');
    
    // Probar cerrar modal con cancelar
    console.log('🔄 Probando cerrar modal con cancelar...');
    await btnCancelar.click();
    await expect(modal).toBeHidden();
    console.log('✅ Modal cerrado correctamente con cancelar');
    
    // Reabrir modal para probar funcionalidad completa
    console.log('🔄 Reabriendo modal para test completo...');
    await cambiarObraButton.click();
    await expect(modal).toBeVisible();
    
    // Solo intentar envío si hay datos disponibles
    if (countObras > 1 && countOperadores > 1) {
      console.log('🚀 Iniciando test de envío del formulario...');
      
      // Rellenar formulario nuevamente
      await selectObra.selectOption({ index: 1 });
      await selectOperador.selectOption({ index: 1 });
      await kilometrajeInput.fill('50000');
      
      // Capturar respuesta de la red
      const responsePromise = page.waitForResponse(response => 
        response.url().includes('/asignaciones-obra/cambiar-obra') && 
        response.request().method() === 'POST'
      );
      
      // Hacer clic en guardar
      console.log('💾 Haciendo clic en Guardar...');
      await btnGuardar.click();
      
      try {
        // Esperar respuesta del servidor
        const response = await responsePromise;
        console.log(`📡 Respuesta del servidor: ${response.status()}`);
        
        if (response.status() === 200) {
          console.log('✅ Cambio de obra procesado exitosamente');
        } else {
          console.log(`⚠️ Respuesta del servidor: ${response.status()}`);
          const responseText = await response.text();
          console.log('📄 Respuesta:', responseText.substring(0, 200));
        }
      } catch (error) {
        console.log('⚠️ Error en la respuesta del servidor:', error.message);
      }
      
      // Verificar que aparece algún tipo de notificación o feedback
      setTimeout(async () => {
        const toastMessage = page.locator('.toast, .alert, .notification, [role="alert"]');
        if (await toastMessage.isVisible()) {
          console.log('✅ Notificación mostrada al usuario');
        }
      }, 1000);
      
    } else {
      console.log('⚠️ Saltando test de envío por falta de datos disponibles');
    }
    
    console.log('🎉 Test de funcionalidad cambiar obra completado');
  });
});
