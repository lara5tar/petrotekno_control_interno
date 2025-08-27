@extends('layouts.app')

@section('title', 'Editar Mantenimiento')
@section('header', 'Editar Mantenimiento')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 flex items-center mb-2">
            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar Mantenimiento #{{ $mantenimiento->id }}
        </h1>
        <p class="text-gray-600">Modifica la información del mantenimiento seleccionado</p>
    </div>
    <div class="flex space-x-2 mt-4 sm:mt-0">
        <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver Detalles
        </a>
        <a href="{{ route('mantenimientos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Listado
        </a>
    </div>
</div>

<!-- Errores de validación -->
@if ($errors->any())
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
    <div class="flex items-center mb-2">
        <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <h3 class="text-sm font-medium text-red-800">Se encontraron los siguientes errores:</h3>
    </div>
    <ul class="list-disc list-inside text-sm text-red-700">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Formulario principal -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <form method="POST" action="{{ route('mantenimientos.update', $mantenimiento->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Información del Activo -->
        <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                Información del Activo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Activo -->
                <div>
                    <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-1">Activo *</label>
                    <select id="vehiculo_id" name="vehiculo_id" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('vehiculo_id') border-red-300 @enderror">
                        <option value="">Seleccione un activo</option>
                        @foreach($vehiculosOptions as $vehiculo)
                            <option value="{{ $vehiculo->id }}" {{ (old('vehiculo_id', $mantenimiento->vehiculo_id) == $vehiculo->id) ? 'selected' : '' }}>
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                            </option>
                        @endforeach
                    </select>
                    @error('vehiculo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Servicio -->
                <div>
                    <label for="tipo_servicio" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Servicio *</label>
                    <select id="tipo_servicio" name="tipo_servicio" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('tipo_servicio') border-red-300 @enderror">
                        <option value="">Seleccione un tipo de servicio</option>
                        @foreach($tiposServicioOptions as $tipo)
                            <option value="{{ $tipo->id }}" {{ (old('tipo_servicio', $mantenimiento->tipo_servicio) == $tipo->id) ? 'selected' : '' }}>
                                {{ $tipo->nombre_tipo_servicio }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_servicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sistema del Activo -->
                <div>
                    <label for="sistema_vehiculo" class="block text-sm font-medium text-gray-700 mb-1">Sistema del Activo *</label>
                    <select id="sistema_vehiculo" name="sistema_vehiculo" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('sistema_vehiculo') border-red-300 @enderror">
                        <option value="">Seleccione un sistema</option>
                        <option value="motor" {{ (old('sistema_vehiculo', $mantenimiento->sistema_vehiculo) == 'motor') ? 'selected' : '' }}>Motor</option>
                        <option value="transmision" {{ (old('sistema_vehiculo', $mantenimiento->sistema_vehiculo) == 'transmision') ? 'selected' : '' }}>Transmisión</option>
                        <option value="hidraulico" {{ (old('sistema_vehiculo', $mantenimiento->sistema_vehiculo) == 'hidraulico') ? 'selected' : '' }}>Hidráulico</option>
                        <option value="general" {{ (old('sistema_vehiculo', $mantenimiento->sistema_vehiculo) == 'general') ? 'selected' : '' }}>General</option>
                    </select>
                    @error('sistema_vehiculo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Información del Servicio -->
        <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Información del Servicio
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Proveedor -->
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                    <input id="proveedor" type="text" name="proveedor" 
                           value="{{ old('proveedor', $mantenimiento->proveedor) }}" 
                           placeholder="Nombre del proveedor o taller"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('proveedor') border-red-300 @enderror">
                    @error('proveedor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kilometraje -->
                <div>
                    <label for="kilometraje_servicio" class="block text-sm font-medium text-gray-700 mb-1">Kilometraje *</label>
                    <input id="kilometraje_servicio" type="number" name="kilometraje_servicio" required
                           value="{{ old('kilometraje_servicio', $mantenimiento->kilometraje_servicio) }}" 
                           min="0" 
                           placeholder="Kilometraje al momento del servicio"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('kilometraje_servicio') border-red-300 @enderror">
                    @error('kilometraje_servicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="mt-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" 
                          placeholder="Descripción del servicio realizado"
                          class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('descripcion') border-red-300 @enderror">{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Fechas y Costo -->
        <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Fechas y Costo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio *</label>
                    <input id="fecha_inicio" type="date" name="fecha_inicio" required
                           value="{{ old('fecha_inicio', $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('Y-m-d') : '') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_inicio') border-red-300 @enderror">
                    @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                    <input id="fecha_fin" type="date" name="fecha_fin"
                           value="{{ old('fecha_fin', $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('Y-m-d') : '') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_fin') border-red-300 @enderror">
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="dejar_vacio" class="mr-2">
                        <label for="dejar_vacio" class="text-sm text-gray-600">Dejar vacío si el mantenimiento está en proceso</label>
                    </div>
                    @error('fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Costo -->
            <div class="mt-6">
                <label for="costo" class="block text-sm font-medium text-gray-700 mb-1">Costo (MXN)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input id="costo" type="number" name="costo" step="0.01" min="0"
                           value="{{ old('costo', $mantenimiento->costo) }}"
                           placeholder="0.00"
                           class="w-full pl-8 p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('costo') border-red-300 @enderror">
                </div>
                @error('costo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Información del Mantenimiento -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2 flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Información del Mantenimiento
            </h4>
            <div class="text-sm text-blue-700">
                <p><strong>Creado:</strong> {{ $mantenimiento->created_at ? $mantenimiento->created_at->format('d/m/Y H:i:s') : 'No disponible' }}</p>
                <p><strong>Última modificación:</strong> {{ $mantenimiento->updated_at ? $mantenimiento->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}</p>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Actualizar Mantenimiento
            </button>
            
            <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver Detalles
            </a>
            
            <a href="{{ route('mantenimientos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dejarVacioCheckbox = document.getElementById('dejar_vacio');
    const fechaFinInput = document.getElementById('fecha_fin');
    
    if (dejarVacioCheckbox) {
        dejarVacioCheckbox.addEventListener('change', function() {
            if (this.checked) {
                fechaFinInput.value = '';
                fechaFinInput.disabled = true;
            } else {
                fechaFinInput.disabled = false;
            }
        });
    }
});
</script>
@endsection
