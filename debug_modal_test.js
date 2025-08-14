// Script de debug para verificar que el modal funciona correctamente
// Este script se puede ejecutar en la consola del navegador

console.log('🔍 Iniciando debug del modal de kilometraje...');

// Función para verificar que el botón existe
function verificarBoton() {
    const botones = document.querySelectorAll('button[onclick="openKilometrajeModal()"]');
    console.log(`✅ Encontrados ${botones.length} botones "Capturar Nuevo"`);

    if (botones.length > 0) {
        console.log('📋 Información de los botones encontrados:');
        botones.forEach((boton, index) => {
            console.log(`  Botón ${index + 1}:`, {
                text: boton.textContent.trim(),
                onclick: boton.getAttribute('onclick'),
                visible: boton.offsetParent !== null
            });
        });
        return botones[0]; // Retorna el primer botón
    }
    return null;
}

// Función para verificar que el modal existe
function verificarModal() {
    const modal = document.getElementById('kilometraje-modal');
    if (modal) {
        console.log('✅ Modal encontrado:', {
            id: modal.id,
            classes: modal.className,
            hidden: modal.classList.contains('hidden')
        });
        return modal;
    } else {
        console.log('❌ Modal NO encontrado');
        return null;
    }
}

// Función para verificar la función JavaScript
function verificarFuncion() {
    if (typeof openKilometrajeModal === 'function') {
        console.log('✅ Función openKilometrajeModal() está definida');
        return true;
    } else {
        console.log('❌ Función openKilometrajeModal() NO está definida');
        return false;
    }
}

// Función para simular click en el botón
function simularClick() {
    const boton = verificarBoton();
    const modal = verificarModal();
    const funcionExiste = verificarFuncion();

    if (boton && modal && funcionExiste) {
        console.log('🧪 Simulando click en el botón...');

        // Verificar estado antes del click
        console.log('📊 Estado antes del click:', {
            modalHidden: modal.classList.contains('hidden')
        });

        // Simular el click
        boton.click();

        // Verificar estado después del click
        setTimeout(() => {
            console.log('📊 Estado después del click:', {
                modalHidden: modal.classList.contains('hidden')
            });

            if (!modal.classList.contains('hidden')) {
                console.log('🎉 ¡ÉXITO! El modal se abrió correctamente');
                console.log('🔍 Verificando formulario dentro del modal...');

                const form = modal.querySelector('#kilometraje-form');
                const inputKm = modal.querySelector('#kilometraje');
                const inputFecha = modal.querySelector('#fecha_captura');

                console.log('📋 Elementos del formulario:', {
                    formulario: form ? 'Encontrado' : 'No encontrado',
                    inputKilometraje: inputKm ? 'Encontrado' : 'No encontrado',
                    inputFecha: inputFecha ? 'Encontrado' : 'No encontrado',
                    actionForm: form ? form.action : 'N/A'
                });

            } else {
                console.log('❌ FALLO: El modal no se abrió');
            }
        }, 200);

    } else {
        console.log('❌ No se puede simular el click. Falta algún elemento:');
        console.log('  - Botón:', boton ? 'OK' : 'FALTA');
        console.log('  - Modal:', modal ? 'OK' : 'FALTA');
        console.log('  - Función:', funcionExiste ? 'OK' : 'FALTA');
    }
}

// Función principal de debug
function debugModal() {
    console.log('🏁 Ejecutando debug completo...');
    console.log('=================================');

    verificarBoton();
    verificarModal();
    verificarFuncion();

    console.log('=================================');
    console.log('🧪 Para probar el modal, ejecuta: simularClick()');
}

// Función para verificar si estamos en la página correcta
function verificarPagina() {
    const url = window.location.href;
    const esVehiculoShow = url.includes('/vehiculos/') && !url.includes('/create') && !url.includes('/edit');

    console.log('📄 Información de la página:', {
        url: url,
        esVehiculoShow: esVehiculoShow,
        title: document.title
    });

    return esVehiculoShow;
}

// Auto-ejecutar debug
if (verificarPagina()) {
    debugModal();
} else {
    console.log('⚠️  No estás en una página de detalle de vehículo');
    console.log('   Navega a una página como: /vehiculos/{id}');
}

// Exportar funciones para uso manual
window.debugModal = debugModal;
window.simularClick = simularClick;
window.verificarBoton = verificarBoton;
window.verificarModal = verificarModal;
