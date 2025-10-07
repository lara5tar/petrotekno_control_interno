@extends('layouts.app')

@section('title', 'Gestión de Roles')

@section('header', 'Gestión de Roles')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Gestión de Roles']
    ]" />

    <!-- Encabezado con botón de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Listado de Roles</h2>
        <div class="flex space-x-3">
            @hasPermission('crear_roles')
            <a href="{{ route('admin.roles.create') }}" 
               class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md flex items-center transition duration-200"
               id="btn-crear-rol">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Agregar Rol
            </a>
            @endhasPermission
        </div>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Mensaje de error --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
    @endif

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('admin.roles.index') }}" id="filtrosForm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
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
                               placeholder="Buscar por nombre de rol o descripción..." 
                               class="pl-10 p-2 border border-gray-300 rounded-md w-full">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                        Buscar
                    </button>
                    <a href="{{ route('admin.roles.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de roles -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rol
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permisos
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuarios
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($roles as $role)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $role->nombre_rol }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $role->descripcion ?? 'Sin descripción' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $role->permisos->count() }} permisos
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $role->usuarios->count() }} usuarios
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @can('ver_roles')
                                        <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        @endcan

                                        @can('editar_roles')
                                        <a href="{{ route('admin.roles.permissions', $role) }}" class="text-yellow-600 hover:text-yellow-900" title="Gestionar permisos">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar rol">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        @endcan

                                        @can('eliminar_roles')
                                        @if($role->usuarios->count() == 0 && !in_array($role->nombre_rol, ['Admin', 'Administrador']))
                                            <button type="button" 
                                                    onclick="openDeleteModal('{{ route('admin.roles.destroy', $role) }}', '{{ $role->nombre_rol }}')"
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Eliminar rol">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay roles registrados</h3>
                                        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer rol del sistema.</p>
                                        @can('crear_roles')
                                        <div class="mt-6">
                                            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                Crear Rol
                                            </a>
                                        </div>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Función para mostrar mensajes de éxito
function showSuccessMessage(message) {
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

// Modal de eliminación
function openDeleteModal(deleteUrl, itemName) {
    const modal = document.getElementById('modal-eliminar');
    const entityIdSpan = document.getElementById('entity-id');
    const entityDisplaySpan = document.getElementById('entity-display');
    const deleteForm = document.getElementById('modal-eliminar-form');
    
    if (!modal || !entityIdSpan || !entityDisplaySpan || !deleteForm) {
        console.error('No se encontraron los elementos del modal');
        return;
    }
    
    // Extraer ID de la URL
    const urlParts = deleteUrl.split('/');
    const itemId = urlParts[urlParts.length - 1];
    
    entityIdSpan.textContent = `#${itemId}`;
    entityDisplaySpan.textContent = ` - ${itemName}`;
    deleteForm.action = deleteUrl;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('modal-eliminar');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Manejar el clic en cancelar
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('modal-eliminar-btn-cancelar');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeDeleteModal);
    }
    
    // Manejar el envío del formulario
    const deleteForm = document.getElementById('modal-eliminar-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.textContent = 'Eliminando...';
            
            // Realizar la petición AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    showSuccessMessage(data.message || 'Rol eliminado exitosamente');
                    // Recargar la página después de un breve retraso
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Error al eliminar');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar: ' + error.message);
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            })
            .finally(() => {
                closeDeleteModal();
            });
        });
    }
    
    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('modal-eliminar');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeDeleteModal();
            }
        });
    }
});
</script>
@endpush

<!-- Modal de confirmación de eliminación -->
<x-delete-confirmation-modal />
