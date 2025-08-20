@extends('layouts.app')

@section('title', 'Gestionar Permisos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestionar Permisos</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Rol: <span class="font-medium text-blue-600">{{ $role->nombre_rol }}</span>
                        @if($role->descripcion)
                            - {{ $role->descripcion }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Resumen del Rol -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $role->permisos->count() }}</div>
                        <div class="text-sm text-gray-600">Permisos Asignados</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $role->usuarios->count() }}</div>
                        <div class="text-sm text-gray-600">Usuarios con este Rol</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $permissions->count() }}</div>
                        <div class="text-sm text-gray-600">Módulos Disponibles</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.roles.permissions.update', $role) }}" method="POST" id="permissionsForm">
                @csrf
                @method('PUT')

                <!-- Controles de Selección -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex space-x-2">
                            <button type="button" id="selectAll" class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Seleccionar Todos
                            </button>
                            <button type="button" id="deselectAll" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Deseleccionar Todos
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Permisos seleccionados:</span>
                            <span id="selectedCount" class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">0</span>
                        </div>
                    </div>
                </div>

                <!-- Permisos por Módulo -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-gray-900 capitalize flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ ucfirst($module) }}
                                    </h3>
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               class="module-select rounded text-blue-600 focus:ring-blue-500" 
                                               data-module="{{ $module }}"
                                               id="module_{{ $module }}">
                                        <label for="module_{{ $module }}" class="ml-2 text-sm text-gray-600 font-medium">Todos</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4 space-y-3 max-h-64 overflow-y-auto">
                                @foreach($modulePermissions as $permission)
                                    <div class="flex items-start">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->id }}"
                                               id="permission_{{ $permission->id }}"
                                               data-module="{{ $module }}"
                                               class="permission-checkbox mt-1 rounded text-blue-600 focus:ring-blue-500"
                                               {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ ucfirst(str_replace('_', ' ', $permission->nombre_permiso)) }}
                                            </div>
                                            @if($permission->descripcion)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $permission->descripcion }}
                                                </div>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Información Adicional -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-700">
                            <p><strong>Importante:</strong> Los cambios en los permisos afectarán inmediatamente a todos los usuarios asignados a este rol ({{ $role->usuarios->count() }} usuarios).</p>
                            <p class="mt-1">Asegúrate de seleccionar solo los permisos necesarios para este rol.</p>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.roles.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Volver a Roles
                        </a>
                        <a href="{{ route('admin.roles.show', $role) }}" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Ver Detalles
                        </a>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Actualizar Permisos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selectedCount;
    }

    function updateModuleSelectors() {
        document.querySelectorAll('.module-select').forEach(moduleCheckbox => {
            const module = moduleCheckbox.dataset.module;
            const modulePermissions = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
            const checkedPermissions = document.querySelectorAll(`[data-module="${module}"].permission-checkbox:checked`);
            
            if (checkedPermissions.length === 0) {
                moduleCheckbox.checked = false;
                moduleCheckbox.indeterminate = false;
            } else if (checkedPermissions.length === modulePermissions.length) {
                moduleCheckbox.checked = true;
                moduleCheckbox.indeterminate = false;
            } else {
                moduleCheckbox.checked = false;
                moduleCheckbox.indeterminate = true;
            }
        });
        updateSelectedCount();
    }

    // Seleccionar/Deseleccionar todos los permisos
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateModuleSelectors();
    });

    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateModuleSelectors();
    });

    // Manejar selección por módulo
    document.querySelectorAll('.module-select').forEach(moduleCheckbox => {
        moduleCheckbox.addEventListener('change', function() {
            const module = this.dataset.module;
            const modulePermissions = document.querySelectorAll(`[data-module="${module}"].permission-checkbox`);
            
            modulePermissions.forEach(permission => {
                permission.checked = this.checked;
            });
            updateSelectedCount();
        });
    });

    // Actualizar selección de módulo cuando cambian permisos individuales
    document.querySelectorAll('.permission-checkbox').forEach(permissionCheckbox => {
        permissionCheckbox.addEventListener('change', updateModuleSelectors);
    });

    // Inicializar estado
    updateModuleSelectors();
});
</script>
@endsection
