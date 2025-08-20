@extends('layouts.app')

@section('title', 'Crear Rol')

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
                    <h1 class="text-2xl font-bold text-gray-900">Crear Nuevo Rol</h1>
                    <p class="text-sm text-gray-600 mt-1">Define un nuevo rol con sus permisos correspondientes</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium">Por favor corrige los siguientes errores:</h4>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.roles.store') }}" method="POST" id="roleForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información Básica -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="nombre_rol" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre del Rol *
                                    </label>
                                    <input type="text" 
                                           id="nombre_rol" 
                                           name="nombre_rol" 
                                           value="{{ old('nombre_rol') }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre_rol') border-red-500 @enderror"
                                           placeholder="Ej: Editor, Moderador, Auditor"
                                           required>
                                    @error('nombre_rol')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Nombre único e identificativo del rol</p>
                                </div>

                                <div>
                                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                                        Descripción
                                    </label>
                                    <textarea id="descripcion" 
                                              name="descripcion" 
                                              rows="4"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                                              placeholder="Describe las responsabilidades y alcance de este rol...">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Permisos del Rol</h3>
                                <div class="flex space-x-2">
                                    <button type="button" id="selectAll" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 transition-colors">
                                        Seleccionar Todos
                                    </button>
                                    <button type="button" id="deselectAll" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors">
                                        Deseleccionar Todos
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium text-gray-900 capitalize">
                                                {{ ucfirst($module) }}
                                            </h4>
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       class="module-select rounded text-blue-600 focus:ring-blue-500" 
                                                       data-module="{{ $module }}"
                                                       id="module_{{ $module }}">
                                                <label for="module_{{ $module }}" class="ml-2 text-sm text-gray-600">Todos</label>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            @foreach($modulePermissions as $permission)
                                                <div class="flex items-center">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           id="permission_{{ $permission->id }}"
                                                           data-module="{{ $module }}"
                                                           class="permission-checkbox rounded text-blue-600 focus:ring-blue-500"
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-700">
                                                        {{ ucfirst(str_replace('_', ' ', $permission->nombre_permiso)) }}
                                                        @if($permission->descripcion)
                                                            <span class="block text-xs text-gray-500">{{ $permission->descripcion }}</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="text-sm text-blue-700">
                                        <p><strong>Tip:</strong> Selecciona solo los permisos necesarios para este rol. Los usuarios podrán realizar únicamente las acciones autorizadas por sus permisos.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.roles.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        Cancelar
                    </a>
                    
                    <div class="flex space-x-3">
                        <button type="button" id="previewBtn" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Vista Previa
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Crear Rol
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Vista Previa -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Vista Previa del Rol
                        </h3>
                        <div class="mt-2">
                            <div id="previewContent" class="text-sm text-gray-500">
                                <!-- Contenido dinámico -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="closePreview" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
        });
    });

    // Actualizar selección de módulo cuando cambian permisos individuales
    document.querySelectorAll('.permission-checkbox').forEach(permissionCheckbox => {
        permissionCheckbox.addEventListener('change', updateModuleSelectors);
    });

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
    }

    // Vista previa
    document.getElementById('previewBtn').addEventListener('click', function() {
        const roleName = document.getElementById('nombre_rol').value;
        const description = document.getElementById('descripcion').value;
        const selectedPermissions = document.querySelectorAll('.permission-checkbox:checked');
        
        let previewContent = `
            <div class="space-y-3">
                <div>
                    <strong>Nombre del Rol:</strong> ${roleName || 'Sin especificar'}
                </div>
                <div>
                    <strong>Descripción:</strong> ${description || 'Sin descripción'}
                </div>
                <div>
                    <strong>Permisos Seleccionados:</strong> ${selectedPermissions.length}
                </div>
        `;

        if (selectedPermissions.length > 0) {
            previewContent += '<div><strong>Lista de Permisos:</strong><ul class="list-disc list-inside mt-1 text-xs">';
            selectedPermissions.forEach(permission => {
                const label = permission.parentElement.querySelector('label').textContent.trim();
                previewContent += `<li>${label}</li>`;
            });
            previewContent += '</ul></div>';
        }

        previewContent += '</div>';
        
        document.getElementById('previewContent').innerHTML = previewContent;
        document.getElementById('previewModal').classList.remove('hidden');
    });

    // Cerrar modal
    document.getElementById('closePreview').addEventListener('click', function() {
        document.getElementById('previewModal').classList.add('hidden');
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById('previewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Inicializar estado de módulos
    updateModuleSelectors();
});
</script>
@endsection
