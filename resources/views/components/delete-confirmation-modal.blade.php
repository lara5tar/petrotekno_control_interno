@props([
    'id' => 'modal-eliminar',
    'entity' => 'elemento',
    'entityIdField' => 'id',
    'entityDisplayField' => 'nombre',
    'routeName' => '',
    'additionalText' => 'Esta acción no se puede deshacer.'
])

<!-- Modal de confirmación para eliminar -->
<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96 max-w-sm mx-4">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar eliminación</h3>
            <div class="mb-4">
                <p class="text-sm text-gray-500">
                    ¿Está seguro de que desea eliminar {{ $entity }} <span id="entity-id" class="font-semibold"></span><span id="entity-display"></span>?
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $additionalText }}
                </p>
            </div>
            <div class="flex gap-3 justify-center">
                <form id="{{ $id }}-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="{{ $id }}-btn-cancelar" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initDeleteModal = function(config) {
        const {
            modalId = '{{ $id }}',
            entityIdField = '{{ $entityIdField }}',
            entityDisplayField = '{{ $entityDisplayField }}',
            routeName = '{{ $routeName }}',
            deleteButtonSelector = '.btn-eliminar',
            baseUrl = ''
        } = config;

        const modal = document.getElementById(modalId);
        const formEliminar = document.getElementById(modalId + '-form');
        const entityIdSpan = document.getElementById('entity-id');
        const entityDisplaySpan = document.getElementById('entity-display');
        const btnCancelar = document.getElementById(modalId + '-btn-cancelar');
        const botonesEliminar = document.querySelectorAll(deleteButtonSelector);

        if (!modal || !formEliminar || !entityIdSpan || !entityDisplaySpan || !btnCancelar) {
            console.error('Error: No se encontraron todos los elementos del modal de eliminación');
            return;
        }

        // Event listeners para botones de eliminar
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function() {
                const entityId = this.getAttribute('data-' + entityIdField);
                const entityDisplay = this.getAttribute('data-' + entityDisplayField);
                
                console.log('Datos obtenidos:', { entityId, entityDisplay });
                
                // Actualizar el modal con la información de la entidad
                entityIdSpan.textContent = `#${entityId}`;
                entityDisplaySpan.textContent = entityDisplay ? ` - ${entityDisplay}` : '';
                
                // Configurar el formulario con la URL correcta
                const deleteUrl = `${baseUrl}/${entityId}`;
                
                formEliminar.setAttribute('action', deleteUrl);
                
                console.log('Modal configurado con URL:', deleteUrl);
                
                // Mostrar el modal
                modal.classList.remove('hidden');
            });
        });

        // Cerrar modal al cancelar
        btnCancelar.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        // Cerrar modal al hacer clic fuera de él
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Manejar el envío del formulario de eliminación
        formEliminar.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Eliminando...
            `;

            // Realizar la petición de eliminación
            const formData = new FormData(this);
            const url = this.getAttribute('action');

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    // Verificar si es una respuesta JSON o redirección
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                throw new Error(data.message || 'Error en la eliminación');
                            }
                        });
                    } else {
                        // Si no es JSON, asumir que es exitoso (redirect)
                        window.location.reload();
                    }
                } else {
                    throw new Error('Error en la eliminación');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la entidad. Por favor, inténtelo de nuevo.');
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                // Cerrar modal
                modal.classList.add('hidden');
            });
        });

        console.log(`✅ Modal de eliminación inicializado para: ${modalId}`);
    };
});
</script>
@endpush