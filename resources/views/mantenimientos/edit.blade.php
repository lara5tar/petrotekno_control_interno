@extends('layouts.app')

@section('title', 'Editar Mantenimiento')

@section('header', 'Editar Mantenimiento')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Mantenimientos', 'url' => route('mantenimientos.index')],
        ['label' => 'Editar Mantenimiento']
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

    {{-- Encabezado --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Editar Mantenimiento</h2>
        <a href="{{ route('mantenimientos.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('mantenimientos.update', $mantenimiento->id) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                {{-- Información del Activo --}}
        <!-- Información del Activo -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Información del Activo
            </h3>            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Información de Auditoría -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" clip-rule="evenodd" />
                </svg>
                Información de Auditoría
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Creación</label>
                    <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                        {{ $mantenimiento->created_at ? $mantenimiento->created_at->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Última Actualización</label>
                    <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                        {{ $mantenimiento->updated_at ? $mantenimiento->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4 pt-4">
            <a href="{{ route('mantenimientos.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                Cancelar
            </a>
            <button type="submit" 
                    class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200">
                Actualizar Mantenimiento
            </button>
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
