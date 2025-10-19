@extends('layouts.app')

@section('title', 'Editar Rol')

@section('header', 'Gestión de Roles')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Gestión de Roles', 'url' => route('admin.roles.index')],
        ['label' => 'Editar Rol']
    ]" />

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
        </div>
    @endif

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Se encontraron {{ $errors->count() }} error(es) en el formulario:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Editar Rol: {{ $role->nombre_rol }}</h2>
        <a href="{{ route('admin.roles.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al Listado
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="roleForm">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <!-- Información Básica del Rol -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                        </svg>
                        Información Básica del Rol
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Define el nombre y descripción del rol</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre_rol" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol *</label>
                            <input type="text" 
                                   id="nombre_rol" 
                                   name="nombre_rol" 
                                   value="{{ old('nombre_rol', $role->nombre_rol) }}"
                                   required
                                   placeholder="Ej: Editor, Moderador, Auditor"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_rol') border-red-500 @enderror">
                            @error('nombre_rol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      rows="3"
                                      placeholder="Describe las responsabilidades y alcance de este rol..."
                                      class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $role->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 p-3 bg-blue-50 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p><strong>Usuarios asignados:</strong> {{ $role->usuarios->count() }}</p>
                                <p class="text-xs mt-1">Los cambios afectarán a todos los usuarios con este rol.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selección de Permisos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Permisos del Rol
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Selecciona los permisos que tendrá este rol</p>

                    <div class="flex justify-between items-center mb-4">
                        <div class="text-sm text-gray-600">
                            Selecciona los permisos necesarios para este rol
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" id="selectAll" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-md hover:bg-blue-200 transition-colors">
                                Seleccionar Todos
                            </button>
                            <button type="button" id="deselectAll" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-md hover:bg-gray-200 transition-colors">
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
                                                   {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
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

                <!-- Botones de Acción -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.roles.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancelar
                    </a>
                    
                    <button type="submit" 
                            class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-6 rounded-md flex items-center transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Actualizar Rol
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de edición de roles cargado');
    
    // Seleccionar/deseleccionar todos los permisos
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Seleccionar todos');
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
            document.querySelectorAll('.module-select').forEach(checkbox => {
                checkbox.checked = true;
                checkbox.indeterminate = false;
            });
        });
    }

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Deseleccionar todos');
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.module-select').forEach(checkbox => {
                checkbox.checked = false;
                checkbox.indeterminate = false;
            });
        });
    }

    // Seleccionar/deseleccionar permisos por módulo
    document.querySelectorAll('.module-select').forEach(moduleCheckbox => {
        moduleCheckbox.addEventListener('change', function(e) {
            const module = this.dataset.module;
            const isChecked = this.checked;
            console.log(`Módulo ${module} cambiado a:`, isChecked);
            
            const modulePermissions = document.querySelectorAll(`input.permission-checkbox[data-module="${module}"]`);
            console.log(`Permisos encontrados para ${module}:`, modulePermissions.length);
            
            modulePermissions.forEach(permission => {
                permission.checked = isChecked;
            });
            
            this.indeterminate = false;
        });
    });

    // Actualizar estado de checkbox de módulo cuando se cambian permisos individuales
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateModuleSelector(this.dataset.module);
        });
    });

    function updateModuleSelector(module) {
        const moduleCheckbox = document.querySelector(`.module-select[data-module="${module}"]`);
        if (!moduleCheckbox) return;
        
        const modulePermissions = document.querySelectorAll(`input.permission-checkbox[data-module="${module}"]`);
        const checkedPermissions = document.querySelectorAll(`input.permission-checkbox[data-module="${module}"]:checked`);
        
        console.log(`Módulo ${module}: ${checkedPermissions.length}/${modulePermissions.length} seleccionados`);
        
        if (checkedPermissions.length === modulePermissions.length && modulePermissions.length > 0) {
            moduleCheckbox.checked = true;
            moduleCheckbox.indeterminate = false;
        } else if (checkedPermissions.length > 0) {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = true;
        } else {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = false;
        }
    }

    function updateAllModuleSelectors() {
        const modules = new Set();
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            modules.add(checkbox.dataset.module);
        });
        
        modules.forEach(module => {
            updateModuleSelector(module);
        });
    }

    // Inicializar estado de módulos
    updateAllModuleSelectors();
    
    console.log('Total checkboxes de permisos:', document.querySelectorAll('.permission-checkbox').length);
    console.log('Total checkboxes de módulos:', document.querySelectorAll('.module-select').length);
});
</script>
@endpush
