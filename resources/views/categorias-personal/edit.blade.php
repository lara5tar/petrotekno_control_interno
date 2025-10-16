@extends('layouts.app')

@section('title', 'Editar Puesto de Personal')

@section('header', 'Editar Puesto')

@section('content')
    {{-- Breadcrumb --}}
        {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Puestos de Personal', 'url' => route('categorias-personal.index')],
        ['label' => 'Editar Puesto']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Editar Puesto: {{ $categoriaPersonal->nombre_categoria }}</h2>
        <div class="flex space-x-3">
            <a href="{{ route('categorias-personal.show', $categoriaPersonal) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Cancelar
            </a>
        </div>
    </div>

    <!-- Alertas de errores -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="font-bold">Error al actualizar el puesto:</div>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Información adicional -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium">Información del puesto:</p>
                <ul class="mt-1 list-disc list-inside space-y-1">
                    <li>ID: {{ $categoriaPersonal->id }}</li>
                    <li>Personal asignado: {{ $categoriaPersonal->personal_count ?? 0 }} {{ ($categoriaPersonal->personal_count ?? 0) == 1 ? 'empleado' : 'empleados' }}</li>
                    <li>Fecha de creación: {{ $categoriaPersonal->created_at->format('d/m/Y H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('categorias-personal.update', $categoriaPersonal) }}" method="POST" id="categoriaForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Nombre del Puesto -->
                <div>
                    <label for="nombre_categoria" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Puesto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nombre_categoria" 
                           id="nombre_categoria" 
                           value="{{ old('nombre_categoria', $categoriaPersonal->nombre_categoria) }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_categoria') border-red-500 @enderror"
                           placeholder="Ej: Ingeniero, Operador, Supervisor, etc.">
                    @error('nombre_categoria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Máximo 255 caracteres</p>
                </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex items-center justify-between">
                <div class="flex space-x-4">
                    <a href="{{ route('categorias-personal.show', $categoriaPersonal) }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-6 rounded transition duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Actualizar Puesto
                    </button>
                </div>

                <!-- Botón de eliminar (solo si no tiene personal asignado) -->
                @if(($categoriaPersonal->personal_count ?? 0) == 0)
                    <form action="{{ route('categorias-personal.destroy', $categoriaPersonal) }}" method="POST" class="inline"
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar el puesto {{ $categoriaPersonal->nombre_categoria }}? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded transition duration-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Eliminar Puesto
                        </button>
                    </form>
                @else
                    <div class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded text-gray-500 bg-gray-100 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        No se puede eliminar (tiene personal asignado)
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoriaForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Prevenir envío múltiple del formulario
    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Actualizando...
        `;
        
        // Restaurar botón después de 5 segundos por si hay errores de red
        setTimeout(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Actualizar Puesto
            `;
        }, 5000);
    });
    
    // Validación de caracteres en tiempo real
    const nombreInput = document.getElementById('nombre_categoria');
    
    // Contador para nombre
    if (nombreInput) {
        const nombreCounter = document.createElement('p');
        nombreCounter.className = 'mt-1 text-xs text-gray-500';
        nombreInput.parentNode.appendChild(nombreCounter);
        
        function updateNombreCounter() {
            const remaining = 255 - nombreInput.value.length;
            nombreCounter.textContent = `${nombreInput.value.length}/255 caracteres`;
            nombreCounter.className = remaining < 50 ? 'mt-1 text-xs text-orange-500' : 'mt-1 text-xs text-gray-500';
        }
        
        nombreInput.addEventListener('input', updateNombreCounter);
        updateNombreCounter();
    }
    
    // Detectar cambios en el formulario
    let originalData = new FormData(form);
    let hasChanges = false;
    
    function checkForChanges() {
        const currentData = new FormData(form);
        hasChanges = false;
        
        for (let [key, value] of currentData.entries()) {
            if (originalData.get(key) !== value) {
                hasChanges = true;
                break;
            }
        }
        
        // Cambiar estilo del botón submit según si hay cambios
        if (hasChanges) {
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitBtn.classList.add('bg-petroyellow', 'hover:bg-yellow-500');
            submitBtn.disabled = false;
        }
    }
    
    // Escuchar cambios en los inputs
    form.addEventListener('input', checkForChanges);
    form.addEventListener('change', checkForChanges);
    
    // Advertir sobre cambios no guardados
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // No advertir si se envía el formulario
    form.addEventListener('submit', function() {
        hasChanges = false;
    });
});
</script>
@endpush