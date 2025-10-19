@extends('layouts.app')

@section('title', 'Editar Tipo de Activo')

@section('header', 'Editar Tipo de Activo')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Tipos de Activos', 'url' => route('tipos-activos.index')],
        ['label' => 'Editar: ' . $tipoActivo->nombre]
    ]" />

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Editar Tipo de Activo</h2>
        <div class="flex space-x-3">
            <a href="{{ route('tipos-activos.show', $tipoActivo) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                Ver Detalles
            </a>
            <a href="{{ route('tipos-activos.index') }}" 
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
            <div class="font-bold">Error al actualizar el tipo de activo:</div>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Alertas de sesión -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('tipos-activos.update', $tipoActivo) }}" method="POST" id="tipoActivoForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Información básica -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Información del Tipo de Activo</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">ID:</span> {{ $tipoActivo->id }}
                        </div>
                        <div>
                            <span class="font-medium">Creado:</span> {{ $tipoActivo->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Última actualización:</span> {{ $tipoActivo->updated_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Activos asignados:</span> {{ $tipoActivo->vehiculos_count ?? 0 }}
                        </div>
                    </div>
                </div>

                <!-- Nombre del Tipo de Activo -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Tipo de Activo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="nombre" 
                           id="nombre" 
                           value="{{ old('nombre', $tipoActivo->nombre) }}"
                           required
                           maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre') border-red-500 @enderror"
                           placeholder="Ej: Vehículo, Maquinaria, Equipo de Oficina, etc.">
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Máximo 255 caracteres</p>
                </div>

                <!-- Tiene Kilometraje -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ¿Este tipo de activo maneja kilometraje? <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" 
                                   id="tiene_kilometraje_si" 
                                   name="tiene_kilometraje" 
                                   value="1" 
                                   {{ old('tiene_kilometraje', $tipoActivo->tiene_kilometraje) == '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                            <label for="tiene_kilometraje_si" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">Sí</span> - Este tipo de activo registra kilometraje
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" 
                                   id="tiene_kilometraje_no" 
                                   name="tiene_kilometraje" 
                                   value="0" 
                                   {{ old('tiene_kilometraje', $tipoActivo->tiene_kilometraje) == '0' ? 'checked' : '' }}
                                   class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                            <label for="tiene_kilometraje_no" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">No</span> - Este tipo de activo no registra kilometraje
                            </label>
                        </div>
                    </div>
                    @error('tiene_kilometraje')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    @if($tipoActivo->vehiculos_count > 0)
                        <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Atención:</strong> Este tipo de activo tiene {{ $tipoActivo->vehiculos_count }} activos asignados. 
                                        Cambiar la configuración de kilometraje puede afectar el registro de datos en estos activos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <p class="text-sm text-gray-500 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Si no maneja kilometraje, los activos de este tipo no tendrán alertas de mantenimiento preventivo basadas en kilometraje.
                            </p>
                </div>

                <!-- Tiene Placa -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ¿Este tipo de activo maneja placas? <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" 
                                   id="tiene_placa_si" 
                                   name="tiene_placa" 
                                   value="1" 
                                   {{ old('tiene_placa', $tipoActivo->tiene_placa ?? true) == '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                            <label for="tiene_placa_si" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">Sí</span> - Este tipo de activo requiere placas
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" 
                                   id="tiene_placa_no" 
                                   name="tiene_placa" 
                                   value="0" 
                                   {{ old('tiene_placa', $tipoActivo->tiene_placa ?? true) == '0' ? 'checked' : '' }}
                                   class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                            <label for="tiene_placa_no" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">No</span> - Este tipo de activo no requiere placas
                            </label>
                        </div>
                    </div>
                    @error('tiene_placa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Los vehículos y activos móviles generalmente requieren placas.
                    </p>
                </div>

                <!-- Tiene Número de Serie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ¿Este tipo de activo maneja número de serie? <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" 
                                   id="tiene_numero_serie_si" 
                                   name="tiene_numero_serie" 
                                   value="1" 
                                   {{ old('tiene_numero_serie', $tipoActivo->tiene_numero_serie ?? true) == '1' ? 'checked' : '' }}
                                   class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                            <label for="tiene_numero_serie_si" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">Sí</span> - Este tipo de activo requiere número de serie (VIN)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" 
                                   id="tiene_numero_serie_no" 
                                   name="tiene_numero_serie" 
                                   value="0" 
                                   {{ old('tiene_numero_serie', $tipoActivo->tiene_numero_serie ?? true) == '0' ? 'checked' : '' }}
                                   class="h-4 w-4 text-petroyellow focus:ring-petroyellow border-gray-300">
                            <label for="tiene_numero_serie_no" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">No</span> - Este tipo de activo no requiere número de serie
                            </label>
                        </div>
                    </div>
                    @error('tiene_numero_serie')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        El número de serie permite identificar únicamente cada activo.
                    </p>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('tipos-activos.show', $tipoActivo) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                        Ver Detalles
                    </a>
                    <a href="{{ route('tipos-activos.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Actualizar Tipo de Activo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tipoActivoForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Prevenir envío múltiple del formulario
    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Actualizando...
        `;
        
        // Restaurar botón después de 5 segundos por si hay errores de red
        setTimeout(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Actualizar Tipo de Activo
            `;
        }, 5000);
    });
    
    // Validación de caracteres en tiempo real
    const nombreInput = document.getElementById('nombre');
    
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