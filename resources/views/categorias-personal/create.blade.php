@extends('layouts.app')

@section('title', 'Nueva Categoría de Personal')

@section('header', 'Crear Nueva Categoría')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Categorías de Personal', 'url' => route('categorias-personal.index')],
        ['label' => 'Nueva Categoría']
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Nueva Categoría de Personal</h2>
        <a href="{{ route('categorias-personal.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Cancelar
        </a>
    </div>

    <!-- Alertas de errores -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="font-bold">Error al crear la categoría:</div>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('categorias-personal.store') }}" method="POST" id="categoriaForm">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Nombre de la Categoría -->
                <div>
                    <label for="nombre_categoria" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Categoría <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nombre_categoria" 
                           id="nombre_categoria" 
                           value="{{ old('nombre_categoria') }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_categoria') border-red-500 @enderror"
                           placeholder="Ej: Ingeniero, Operador, Supervisor, etc.">
                    @error('nombre_categoria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Máximo 255 caracteres</p>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('categorias-personal.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded transition duration-200">
                        Crear Categoría
                    </button>
                </div>
            </form>
        </div>
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
            Creando...
        `;
        
        // Restaurar botón después de 5 segundos por si hay errores de red
        setTimeout(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Crear Categoría
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
});
</script>
@endpush