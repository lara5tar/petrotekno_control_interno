import './bootstrap';

// Función global para manejar archivos en los formularios de vehículos
window.handleFileInput = function (input, previewId) {
    const file = input.files[0];
    const previewElement = document.getElementById(previewId);

    if (!previewElement) return;

    if (file) {
        // Mostrar información del archivo seleccionado
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
        const fileType = file.type;

        // Validar tipo de archivo
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(fileType)) {
            alert('Tipo de archivo no válido. Solo se permiten PDF, JPG, JPEG y PNG.');
            input.value = '';
            return;
        }

        // Validar tamaño (5MB máximo)
        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo es demasiado grande. Tamaño máximo: 5MB.');
            input.value = '';
            return;
        }

        // Mostrar preview del archivo
        previewElement.innerHTML = `
            <div class="flex items-center space-x-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-green-900 truncate">${fileName}</p>
                    <p class="text-sm text-green-700">Tamaño: ${fileSize} MB</p>
                </div>
                <button type="button" onclick="clearFileInput('${input.id}', '${previewId}')" 
                        class="flex-shrink-0 text-green-600 hover:text-green-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
    } else {
        previewElement.innerHTML = '';
    }
};

// Función para limpiar el input de archivo
window.clearFileInput = function (inputId, previewId) {
    const input = document.getElementById(inputId);
    const previewElement = document.getElementById(previewId);

    if (input) input.value = '';
    if (previewElement) previewElement.innerHTML = '';
};
