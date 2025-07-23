@extends('layouts.app')

@section('title', 'Control de Personal')

@section('header', 'Control de Personal de Petrotekno')

@section('content')
{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Personal', 'url' => route('personal.index')],
    ['label' => $personal->nombre_completo ?? 'Personal']
]" />

<!-- Contenido Principal -->
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo - Datos Generales -->
        <div class="space-y-6">
            <!-- Datos Generales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nombre</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->nombre_completo ?? 'Marco Delgado Reyes' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Categoría</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $personal->categoria->nombre_categoria ?? 'Técnico Especializado' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID Empleado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ str_pad($personal->id ?? 1, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estatus</label>
                            <div class="bg-green-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-green-300 rounded-full mr-2"></span>
                                {{ ucfirst($personal->estatus ?? 'Activo') }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">CURP</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    DERJ850315HNLGYR07
                                </div>
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">RFC</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    DERJ850315XY4
                                </div>
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Identificación (INE)</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    1234567890123
                                </div>
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">NSS</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    12345678901
                                </div>
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Licencia de Manejo</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    LIC123456789
                                </div>
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">CV Profesional</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-green-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    Disponible
                                </div>
                                <button class="bg-gray-600 hover:bg-gray-700 text-white p-2 rounded text-sm transition duration-200 flex items-center" 
                                        title="Ver archivo adjunto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Información Adicional -->
        <div class="space-y-6">
            <!-- Tabs de Información -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-0 border-b border-gray-300">
                    <div class="flex space-x-0" role="tablist">
                        {{-- <button id="asignacion-tab" 
                                class="px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600 bg-white transition-colors duration-200"
                                onclick="switchTab('asignacion')" 
                                role="tab" 
                                aria-selected="true"
                                aria-controls="asignacion-content">
                            Asignación
                        </button> --}}
                        <button id="documentos-tab" 
                                class="px-4 py-3 text-sm font-medium border-b-2 border-gray-600 text-gray-600 bg-white transition-colors duration-200"
                                onclick="switchTab('documentos')" 
                                role="tab" 
                                aria-selected="true"
                                aria-controls="documentos-content">
                            Documentos
                        </button>
                    </div>
                </div>
                
                {{-- <!-- Contenido de Asignación -->
                <div id="asignacion-content" class="tab-content p-4" role="tabpanel" aria-labelledby="asignacion-tab">
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-3">Obra Actual</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600">Nombre de Obra</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Mantenimiento Vial Monterrey
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Ubicación</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Monterrey, N.L.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600">Puesto Asignado</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Técnico Especializado
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Supervisor</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        Ing. Carlos López
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600">Fecha Inicio</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        15/07/2025
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Fecha Término</label>
                                    <div class="bg-gray-600 text-white px-2 py-1 rounded text-sm">
                                        15/12/2025
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botón Ir a Obra -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center" 
                                        title="Ver detalles de la obra">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Ir a Obra
                                </button>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Contenido de Documentos -->
                <div id="documentos-content" class="tab-content p-4" role="tabpanel" aria-labelledby="documentos-tab">
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-3">Documentos del Personal</h4>
                        <div class="space-y-4">
                            <!-- Lista de documentos -->
                            <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Curriculum Vitae</p>
                                            <p class="text-sm text-gray-500">CV_Marco_Delgado_2025.pdf</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver documento">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Identificación Oficial</p>
                                            <p class="text-sm text-gray-500">INE_Marco_Delgado.pdf</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver documento">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Licencia de Manejo</p>
                                            <p class="text-sm text-gray-500">Licencia_Marco_Delgado.pdf</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Ver documento">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>                            <div class="mt-4 pt-4 border-t border-gray-200">
                                @hasPermission('editar_personal')
                                <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Agregar Documento
                                </button>
                                @endhasPermission
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    @hasPermission('editar_personal')
    <!-- Botón Editar -->
    <a href="{{ route('personal.edit', $personal->id ?? 1) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Editar Personal">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>
    @endhasPermission

    @hasPermission('eliminar_personal')
    <!-- Botón Eliminar -->
    <form action="{{ route('personal.destroy', $personal->id ?? 1) }}" 
          method="POST" 
          class="inline" 
          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este personal? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
                title="Eliminar Personal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
            </svg>
            <span>Eliminar</span>
        </button>
    </form>
    @endhasPermission
</div>

@push('scripts')
<script>
// Hacer la función global para que sea accesible desde los elementos HTML
window.switchTab = function(tabName) {
    console.log('Switching to tab:', tabName); // Debug
    
    // Lista de todas las pestañas disponibles (solo documentos por ahora)
    const tabs = ['documentos']; // 'asignacion' comentado temporalmente
    
    tabs.forEach(tab => {
        // Referencias a los elementos
        const tabButton = document.getElementById(`${tab}-tab`);
        const tabContent = document.getElementById(`${tab}-content`);
        
        if (!tabButton || !tabContent) {
            console.error(`Element not found for tab: ${tab}`);
            return;
        }
        
        if (tab === tabName) {
            // Activar pestaña seleccionada
            tabButton.classList.add('border-gray-600', 'text-gray-600', 'bg-white');
            tabButton.classList.remove('text-gray-400', 'border-transparent');
            tabButton.setAttribute('aria-selected', 'true');
            
            // Mostrar contenido de la pestaña activa
            tabContent.classList.remove('hidden');
            console.log(`Activated tab: ${tab}`); // Debug
        } else {
            // Desactivar pestañas no seleccionadas
            tabButton.classList.remove('border-gray-600', 'text-gray-600', 'bg-white');
            tabButton.classList.add('text-gray-400', 'border-transparent');
            tabButton.setAttribute('aria-selected', 'false');
            
            // Ocultar contenido de pestañas inactivas
            tabContent.classList.add('hidden');
        }
    });
};

// Navegación con teclado para accesibilidad
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing tabs'); // Debug
    
    const tabButtons = document.querySelectorAll('[role="tab"]');
    console.log('Found tab buttons:', tabButtons.length); // Debug
    
    tabButtons.forEach((button, index) => {
        button.addEventListener('keydown', function(e) {
            let targetIndex = index;
            
            // Navegación con flechas
            if (e.key === 'ArrowRight') {
                targetIndex = (index + 1) % tabButtons.length;
                e.preventDefault();
            } else if (e.key === 'ArrowLeft') {
                targetIndex = (index - 1 + tabButtons.length) % tabButtons.length;
                e.preventDefault();
            } else if (e.key === 'Home') {
                targetIndex = 0;
                e.preventDefault();
            } else if (e.key === 'End') {
                targetIndex = tabButtons.length - 1;
                e.preventDefault();
            }
            
            if (targetIndex !== index) {
                tabButtons[targetIndex].focus();
                
                // Extraer el nombre de la pestaña del ID del botón
                const tabName = tabButtons[targetIndex].id.replace('-tab', '');
                window.switchTab(tabName);
            }
        });
    });
    
    // Añadir click listeners como alternativa
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.id.replace('-tab', '');
            window.switchTab(tabName);
        });
    });
});

// Función auxiliar para indicadores de carga
window.showTabLoading = function(tabName) {
    const tabContent = document.getElementById(`${tabName}-content`);
    if (tabContent) {
        tabContent.style.opacity = '0.6';
        setTimeout(() => {
            tabContent.style.opacity = '1';
        }, 200);
    }
};
</script>
@endpush
@endsection
