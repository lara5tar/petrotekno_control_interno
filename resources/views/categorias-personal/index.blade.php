@extends('layouts.app')

@section('title', 'Categorías de Personal')

@section('header', 'Gestión de Categorías de Personal')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Personal', 'url' => route('personal.index')],
        ['label' => 'Categorías de Personal']
    ]" />

    <!-- Encabezado con botones de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Categorías de Personal</h2>
        <div class="flex space-x-3">
            <a href="{{ route('personal.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al Personal
            </a>
            @hasPermission('crear_catalogos')
            <button type="button" 
                    onclick="openCreateCategoryModal()"
                    class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nueva Categoría
            </button>
            @endhasPermission
        </div>
    </div>

    <!-- Alertas de sesión -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('categorias-personal.index') }}" id="filtrosForm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar categoría</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Buscar por nombre de categoría..." 
                               class="pl-10 p-2 border border-gray-300 rounded-md w-full">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded transition duration-200">
                        Filtrar
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('categorias-personal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de categorías -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de Categoría</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personal Asignado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($categorias->count() > 0)
                        @foreach($categorias as $categoria)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $categoria->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $categoria->nombre_categoria }}
                                    @if($categoria->descripcion)
                                        <p class="text-xs text-gray-500 mt-1">{{ $categoria->descripcion }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $categoria->personal_count }} {{ $categoria->personal_count == 1 ? 'empleado' : 'empleados' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $categoria->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('categorias-personal.show', $categoria) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('categorias-personal.edit', $categoria) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        @if($categoria->personal_count == 0)
                                        <form action="{{ route('categorias-personal.destroy', $categoria) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar la categoría {{ $categoria->nombre_categoria }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400" title="No se puede eliminar: tiene personal asignado">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Estado vacío dentro de la tabla -->
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay categorías</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if(request()->filled('search'))
                                            No se encontraron categorías con los criterios especificados.
                                        @else
                                            Aún no hay categorías de personal registradas en el sistema.
                                        @endif
                                    </p>
                                    @if(request()->filled('search'))
                                        <div class="mt-4">
                                            <a href="{{ route('categorias-personal.index') }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200">
                                                Limpiar filtros
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mt-4">
                                        <a href="{{ route('categorias-personal.create') }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-white bg-petroyellow hover:bg-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Crear primera categoría
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(method_exists($categorias, 'hasPages') && $categorias->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($categorias->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                                Anterior
                            </span>
                        @else
                            <a href="{{ $categorias->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </a>
                        @endif
                        
                        @if($categorias->hasMorePages())
                            <a href="{{ $categorias->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Siguiente
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                                Siguiente
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ $categorias->firstItem() }}</span> a <span class="font-medium">{{ $categorias->lastItem() }}</span> de <span class="font-medium">{{ $categorias->total() }}</span> categorías
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{ $categorias->appends(request()->query())->links() }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal para crear nueva categoría -->
    <div id="createCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Encabezado del modal -->
                <div class="flex items-center justify-between pb-3">
                    <h3 class="text-lg font-medium text-gray-900">Nueva Categoría de Personal</h3>
                    <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Formulario -->
                <form id="createCategoryForm" onsubmit="submitCreateCategory(event)">
                    @csrf
                    <div class="mb-4">
                        <label for="modal_nombre_categoria" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Categoría <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="modal_nombre_categoria" 
                               name="nombre_categoria" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-petrodark focus:border-petrodark" 
                               placeholder="Ej: Supervisor, Operador, Técnico..."
                               required
                               maxlength="255">
                        <div id="modal_error_message" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" 
                                onclick="closeCategoryModal()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit" 
                                id="modal_submit_btn"
                                class="px-4 py-2 bg-petroyellow text-petrodark rounded-md hover:bg-yellow-500 transition duration-200">
                            Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de búsqueda
    const searchInput = document.getElementById('search');
    const form = document.getElementById('filtrosForm');
    
    // Event listener para búsqueda con delay
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            form.submit();
        }, 500); // Esperar 500ms después de que el usuario deje de escribir
    });
    
    // Prevenir envío múltiple del formulario
    form.addEventListener('submit', function() {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtrando...';
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Filtrar';
            }, 2000);
        }
    });
});

// Funciones para el modal de crear categoría
function openCreateCategoryModal() {
    document.getElementById('createCategoryModal').classList.remove('hidden');
    document.getElementById('modal_nombre_categoria').focus();
    // Limpiar formulario
    document.getElementById('createCategoryForm').reset();
    document.getElementById('modal_error_message').classList.add('hidden');
}

function closeCategoryModal() {
    document.getElementById('createCategoryModal').classList.add('hidden');
}

function submitCreateCategory(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = document.getElementById('modal_submit_btn');
    const errorDiv = document.getElementById('modal_error_message');
    const nombreInput = document.getElementById('modal_nombre_categoria');
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creando...';
    errorDiv.classList.add('hidden');
    
    // Preparar datos del formulario
    const formData = new FormData(form);
    
    // Asegurar URL HTTPS
    const storeUrl = '{{ route("categorias-personal.store") }}';
    const httpsUrl = storeUrl.replace(/^http:/, 'https:');
    
    // Realizar petición AJAX
    fetch(httpsUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Éxito: cerrar modal y recargar página
            closeCategoryModal();
            
            // Mostrar mensaje de éxito
            showSuccessMessage('Categoría creada exitosamente');
            
            // Recargar la página para mostrar la nueva categoría
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Error: mostrar mensaje de error
            if (data.errors && data.errors.nombre_categoria) {
                errorDiv.textContent = data.errors.nombre_categoria[0];
            } else {
                errorDiv.textContent = data.message || 'Error al crear la categoría';
            }
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Error de conexión. Inténtalo de nuevo.';
        errorDiv.classList.remove('hidden');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.textContent = 'Crear Categoría';
    });
}

function showSuccessMessage(message) {
    // Crear elemento de mensaje de éxito
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
    successDiv.innerHTML = message;
    
    // Agregar al DOM
    document.body.appendChild(successDiv);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.parentNode.removeChild(successDiv);
        }
    }, 3000);
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('createCategoryModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeCategoryModal();
    }
});
</script>
@endpush