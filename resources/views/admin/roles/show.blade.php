@extends('layouts.app')

@section('title', 'Detalles del Rol')

@section('header', 'Gestión de Roles')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
    ['label' => 'Roles', 'url' => route('admin.roles.index')],
    ['label' => 'Detalle del Rol']
]" />

<div class="h-[calc(100vh-120px)] flex flex-col gap-4">

    <!-- Contenido Principal en Grid 1/3 - 2/3 -->
    <div class="flex-1 grid grid-cols-3 gap-4">
        <!-- Panel Izquierdo - 1/3 del espacio -->
        <div class="flex flex-col gap-4">
            <!-- Datos Generales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Información General</h3>
                </div>
                <div class="p-4">
                    <!-- Nombre del Rol -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Nombre del Rol</label>
                        <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                            {{ $role->nombre_rol }}
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600">Descripción</label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 min-h-[80px]">
                            @if($role->descripcion)
                                <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $role->descripcion }}</div>
                            @else
                                <div class="text-sm text-gray-400 italic">Sin descripción</div>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Permisos</label>
                            <div class="bg-blue-100 text-blue-800 px-3 py-2 rounded text-center">
                                <div class="text-2xl font-bold">{{ $role->permisos->count() }}</div>
                                <div class="text-xs">asignados</div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Usuarios</label>
                            <div class="bg-green-100 text-green-800 px-3 py-2 rounded text-center">
                                <div class="text-2xl font-bold">{{ $role->usuarios->count() }}</div>
                                <div class="text-xs">activos</div>
                            </div>
                        </div>
                    </div>

                    <!-- Módulos Cubiertos -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Módulos Cubiertos</label>
                        <div class="bg-purple-100 text-purple-800 px-3 py-2 rounded text-center">
                            <div class="text-2xl font-bold">
                                {{ $role->permisos->groupBy(function($permission) {
                                    $parts = explode('_', $permission->nombre_permiso);
                                    return isset($parts[1]) ? $parts[1] : 'general';
                                })->count() }}
                            </div>
                            <div class="text-xs">módulos</div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span class="font-medium">Creado:</span>
                                <span>{{ $role->created_at ? $role->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Última actualización:</span>
                                <span>{{ $role->updated_at ? $role->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Hace:</span>
                                <span>{{ $role->created_at ? $role->created_at->diffForHumans() : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - 2/3 del espacio -->
        <div class="col-span-2 flex flex-col gap-4">
            <!-- Pestañas de Información - Diseño tipo carpetas -->
            <div class="bg-white flex-1 flex flex-col" id="tabs-container">
                <div class="relative">
                    <nav class="flex space-x-1 pr-3 pt-3">
                        <button onclick="changeTab('permisos')" 
                                id="tab-permisos"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 ml-0 bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3c0-.265.105-.52.293-.707l10-10a6 6 0 017.414 7.414z" />
                                </svg>
                                Permisos
                            </span>
                        </button>
                        <button onclick="changeTab('usuarios')" 
                                id="tab-usuarios"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                Usuarios
                            </span>
                        </button>
                    </nav>
                    <!-- Línea base que conecta con las pestañas -->
                    <div class="absolute bottom-0 left-0 right-0 h-px bg-gray-300 z-0"></div>
                </div>

                <!-- Contenido de pestañas con bordes tipo carpeta -->
                <div class="flex-1 overflow-hidden bg-gray-50 border-l border-r border-b border-gray-300 rounded-b-lg">
                    <!-- Contenido de Permisos -->
                    <div id="content-permisos" class="p-6 bg-gray-50 tab-content h-full overflow-y-auto" style="display: block;">
                        <div class="space-y-6">
                            @if($role->permisos->count() > 0)
                                @php
                                    $permissionsByModule = $role->permisos->groupBy(function($permission) {
                                        $parts = explode('_', $permission->nombre_permiso);
                                        return isset($parts[1]) ? $parts[1] : 'general';
                                    });
                                @endphp
                                
                                @foreach($permissionsByModule as $module => $modulePermissions)
                                    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                        <h4 class="font-semibold text-gray-800 capitalize mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                            </svg>
                                            {{ ucfirst($module) }}
                                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                                                {{ $modulePermissions->count() }}
                                            </span>
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($modulePermissions as $permission)
                                                <div class="flex items-start bg-gray-50 rounded-lg p-3 border border-gray-200">
                                                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $permission->nombre_permiso)) }}
                                                        </div>
                                                        @if($permission->descripcion)
                                                            <div class="text-xs text-gray-500 mt-1">{{ $permission->descripcion }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m-16-4c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sin permisos asignados</h3>
                                    <p class="text-sm text-gray-600 mb-4">Este rol no tiene permisos asignados actualmente.</p>
                                    @can('editar_roles')
                                    <a href="{{ route('admin.roles.edit', $role) }}" 
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                                        Asignar Permisos
                                    </a>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contenido de Usuarios -->
                    <div id="content-usuarios" class="p-6 bg-gray-50 tab-content h-full overflow-y-auto" style="display: none;">
                        <div class="space-y-4">
                            @if($role->usuarios->count() > 0)
                                @foreach($role->usuarios as $user)
                                    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <div class="text-base font-semibold text-gray-900">
                                                    {{ $user->personal ? $user->personal->nombres . ' ' . $user->personal->apellidos : 'Usuario sin personal asignado' }}
                                                </div>
                                                <div class="text-sm text-gray-600 mt-1">
                                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $user->email }}
                                                </div>
                                                @if($user->personal)
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $user->personal->cargo ?? 'Sin cargo definido' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-shrink-0">
                                                @if($user->deleted_at)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        </svg>
                                                        Inactivo
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        Activo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sin usuarios asignados</h3>
                                    <p class="text-sm text-gray-600">Ningún usuario tiene asignado este rol actualmente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    <!-- Botón Editar -->
    @can('editar_roles')
    <a href="{{ route('admin.roles.edit', $role) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Editar Rol">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>
    @endcan

    <!-- Botón Eliminar -->
    @can('eliminar_roles')
    @if($role->usuarios->count() == 0 && !in_array($role->nombre_rol, ['Admin', 'Administrador']))
        <button type="button" 
                onclick="openDeleteModal('{{ route('admin.roles.destroy', $role) }}', '{{ $role->nombre_rol }}')"
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
                title="Eliminar Rol">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>Eliminar</span>
        </button>
    @endif
    @endcan
</div>

@endsection

<!-- Modal de confirmación para eliminar rol -->
<x-delete-confirmation-modal 
    id="delete-confirmation-modal"
    entity="el rol"
    entityIdField="rol-id"
    entityDisplayField="rol-info"
    routeName="admin.roles"
    additionalText="Esta acción no se puede deshacer y eliminará toda la información asociada al rol."
/>

@push('scripts')
<script>
    // Función para cambiar entre pestañas
    function changeTab(tabName) {
        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });
        
        // Remover estilos activos de todas las pestañas
        document.querySelectorAll('[id^="tab-"]').forEach(tab => {
            tab.classList.remove('bg-gray-50', 'border-gray-300', 'border-t', 'border-l', 'border-r', 'text-gray-800', 'shadow-sm', 'z-10');
            tab.classList.add('bg-gray-100', 'border-gray-300', 'border-t', 'border-l', 'border-r', 'text-gray-600', 'hover:bg-gray-200');
        });
        
        // Mostrar el contenido seleccionado
        document.getElementById('content-' + tabName).style.display = 'block';
        
        // Activar la pestaña seleccionada
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
        activeTab.classList.add('bg-gray-50', 'border-gray-300', 'border-t', 'border-l', 'border-r', 'text-gray-800', 'shadow-sm', 'z-10');
    }

    // Función para abrir modal de eliminación
    function openDeleteModal(deleteUrl, itemName) {
        const modal = document.getElementById('delete-confirmation-modal');
        const entityIdSpan = document.getElementById('entity-id');
        const entityDisplaySpan = document.getElementById('entity-display');
        const form = document.getElementById('delete-confirmation-modal-form');
        
        if (modal && entityIdSpan && entityDisplaySpan && form) {
            const rolId = deleteUrl.split('/').pop();
            
            entityIdSpan.textContent = `#${rolId}`;
            entityDisplaySpan.textContent = itemName ? ` - ${itemName}` : '';
            form.setAttribute('action', deleteUrl);
            modal.classList.remove('hidden');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-confirmation-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endpush
