@extends('layouts.app')

@section('title', 'Nuevo Mantenimiento')
@section('header', 'Nuevo Mantenimiento')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 flex items-center mb-2">
            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Mantenimiento
        </h1>
        <p class="text-gray-600">Registra un nuevo mantenimiento para los vehículos de la flota</p>
    </div>
    <div class="flex space-x-2 mt-4 sm:mt-0">
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
    <form method="POST" action="{{ route('mantenimientos.store') }}" class="space-y-6">
        @csrf

        <!-- Información del Vehículo -->
        <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 5a2 2 0 00-2 2v6c0 1.1.9 2 2 2h1v2a1 1 0 001 1h2a1 1 0 001-1v-2h6v2a1 1 0 001 1h2a1 1 0 001-1v-2h1c1.1 0 2-.9 2-2V7a2 2 0 00-2-2H4zm0 2h12v6H4V7zm2 2a1 1 0 00-1 1v1a1 1 0 002 0v-1a1 1 0 00-1-1zm8 0a1 1 0 00-1 1v1a1 1 0 002 0v-1a1 1 0 00-1-1z"/>
                </svg>
                Información del Vehículo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vehículo -->
                <div>
                    <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-1">Vehículo *</label>
                    <select id="vehiculo_id" name="vehiculo_id" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('vehiculo_id') border-red-300 @enderror">
                        <option value="">Seleccione un vehículo</option>
                        @foreach($vehiculosOptions as $vehiculo)
                            <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
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
                            <option value="{{ $tipo->id }}" {{ old('tipo_servicio') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre_tipo_servicio }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_servicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sistema del Vehículo -->
                <div>
                    <label for="sistema_vehiculo" class="block text-sm font-medium text-gray-700 mb-1">Sistema del Vehículo *</label>
                    <select id="sistema_vehiculo" name="sistema_vehiculo" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('sistema_vehiculo') border-red-300 @enderror">
                        <option value="">Seleccione un sistema</option>
                        <option value="motor" {{ old('sistema_vehiculo') == 'motor' ? 'selected' : '' }}>Motor</option>
                        <option value="transmision" {{ old('sistema_vehiculo') == 'transmision' ? 'selected' : '' }}>Transmisión</option>
                        <option value="hidraulico" {{ old('sistema_vehiculo') == 'hidraulico' ? 'selected' : '' }}>Hidráulico</option>
                        <option value="general" {{ old('sistema_vehiculo') == 'general' ? 'selected' : '' }}>General</option>
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
                           value="{{ old('proveedor') }}" 
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
                           value="{{ old('kilometraje_servicio') }}" 
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
                          class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('descripcion') border-red-300 @enderror">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Fechas y Costo -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Fechas y Costo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio *</label>
                    <input id="fecha_inicio" type="date" name="fecha_inicio" 
                           value="{{ old('fecha_inicio') }}" 
                           required
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_inicio') border-red-300 @enderror">
                    @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                    <input id="fecha_fin" type="date" name="fecha_fin" 
                           value="{{ old('fecha_fin') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_fin') border-red-300 @enderror">
                    @error('fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Costo -->
                <div>
                    <label for="costo" class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input id="costo" type="number" name="costo" 
                               value="{{ old('costo') }}" 
                               min="0" 
                               step="0.01" 
                               placeholder="0.00"
                               class="w-full pl-8 p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('costo') border-red-300 @enderror">
                    </div>
                    @error('costo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('mantenimientos.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 text-center">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Crear Mantenimiento
            </button>
        </div>
    </form>
</div>
@endsection
