@extends('layouts.app')

@section('title', 'Editar Vehículo')

@section('header', 'Editar Vehículo')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
        ['label' => 'Editar Vehículo']
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
        <h2 class="text-2xl font-bold text-gray-800">Editar Vehículo - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h2>
        <a href="{{ route('vehiculos.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('vehiculos.update', $vehiculo->id) }}" method="POST" enctype="multipart/form-data" id="vehiculoForm">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <!-- Información del Vehículo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                        </svg>
                        Información del Vehículo
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-form-input name="marca" label="Marca" required placeholder="Ej: Ford, Chevrolet, Toyota" value="{{ old('marca', $vehiculo->marca) }}" />
                        <x-form-input name="modelo" label="Modelo" required placeholder="Ej: F-150, Silverado, Hilux" value="{{ old('modelo', $vehiculo->modelo) }}" />
                        <x-form-input name="anio" label="Año" type="number" min="1990" max="2025" placeholder="2023" value="{{ old('anio', $vehiculo->anio) }}" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <x-form-input name="n_serie" label="Número de Serie (VIN)" required placeholder="1FTFW1ET5DFA12345" value="{{ old('n_serie', $vehiculo->n_serie) }}" />
            <x-form-input name="placas" label="Placas" placeholder="ABC-123-A" value="{{ old('placas', $vehiculo->placas) }}" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-6">
                        <x-form-input name="kilometraje_actual" label="Kilometraje Actual (km)" type="number" required min="0" placeholder="15000" value="{{ old('kilometraje_actual', $vehiculo->kilometraje_actual) }}" />
                    </div>

                    <!-- Operador Asignado -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-6">
                        <div class="form-group">
                            <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Operador Asignado
                            </label>
                            <select name="operador_id" 
                                    id="operador_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('operador_id') border-red-500 @enderror">
                                <option value="">Seleccione un operador (opcional)</option>
                                @if(isset($operadores) && $operadores->count() > 0)
                                    @foreach($operadores as $operador)
                                        <option value="{{ $operador->id }}" {{ (old('operador_id', $vehiculo->operador_id ?? '') == $operador->id) ? 'selected' : '' }}>
                                            {{ $operador->nombre_completo }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('operador_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Persona encargada de operar el vehículo</p>
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos Específicos del Vehículo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Vehículo
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Primera columna -->
                        <div class="space-y-6">
                            <!-- 1. Póliza de Seguro -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Póliza de Seguro
                                </label>
                                <div class="flex items-center justify-center">
                                    <div class="w-full">
                                        <input type="file" 
                                               id="poliza_seguro_file" 
                                               name="poliza_seguro_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               onchange="handleFileInput(event, 'poliza_seguro')" />
                                        <label for="poliza_seguro_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <span class="text-base">Subir Póliza de Seguro</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_seguro" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_seguro') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="poliza_seguro_status">PDF, JPG, PNG (máx. 5MB)</p>
                            </div>

                            <!-- 2. Derecho Vehicular -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Derecho Vehicular
                                </label>
                                <div class="flex items-center justify-center">
                                    <div class="w-full">
                                        <input type="file" 
                                               id="derecho_vehicular_file" 
                                               name="derecho_vehicular_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               onchange="handleFileInput(event, 'derecho_vehicular')" />
                                        <label for="derecho_vehicular_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <span class="text-base">Subir Derecho Vehicular</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_derecho" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_derecho') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="derecho_vehicular_status">PDF, JPG, PNG (máx. 5MB)</p>
                            </div>
                        </div>

                        <!-- Segunda columna -->
                        <div class="space-y-6">
                            <!-- 3. Factura y/o Pedimento -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                                    </svg>
                                    Factura y/o Pedimento
                                </label>
                                <div class="flex items-center justify-center">
                                    <div class="w-full">
                                        <input type="file" 
                                               id="factura_pedimento_file" 
                                               name="factura_pedimento_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               onchange="handleFileInput(event, 'factura_pedimento')" />
                                        <label for="factura_pedimento_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <span class="text-base">Subir Factura y/o Pedimento</span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="factura_pedimento_status">PDF, JPG, PNG (máx. 5MB)</p>
                            </div>

                            <!-- 4. Fotografía del Vehículo -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Fotografía del Vehículo
                                </label>
                                <div class="flex items-center justify-center">
                                    <div class="w-full">
                                        <input type="file" 
                                               id="fotografia_file" 
                                               name="fotografia_file" 
                                               accept=".jpg,.jpeg,.png" 
                                               class="hidden" 
                                               onchange="handleFileInput(event, 'fotografia')" />
                                        <label for="fotografia_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-base">Subir Fotografía del Vehículo</span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="fotografia_status">JPG, PNG (máx. 5MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Mantenimiento -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Intervalos de Mantenimiento
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="intervalo_km_motor" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Motor (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_motor" 
                                   name="intervalo_km_motor" 
                                   min="1000" 
                                   step="500"
                                   placeholder="5000"
                                   value="{{ old('intervalo_km_motor', $vehiculo->intervalo_km_motor) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_motor') border-red-500 @enderror">
                            @error('intervalo_km_motor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite de motor</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo_km_transmision" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Transmisión (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_transmision" 
                                   name="intervalo_km_transmision" 
                                   min="5000" 
                                   step="1000"
                                   placeholder="40000"
                                   value="{{ old('intervalo_km_transmision', $vehiculo->intervalo_km_transmision) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_transmision') border-red-500 @enderror">
                            @error('intervalo_km_transmision') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite de transmisión</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo_km_hidraulico" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Hidráulico (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_hidraulico" 
                                   name="intervalo_km_hidraulico" 
                                   min="1000" 
                                   step="500"
                                   placeholder="10000"
                                   value="{{ old('intervalo_km_hidraulico', $vehiculo->intervalo_km_hidraulico) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_hidraulico') border-red-500 @enderror">
                            @error('intervalo_km_hidraulico') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite hidráulico (si aplica)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Observaciones -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Observaciones Adicionales
                    </h3>
                    
                    <div class="form-group">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Notas y Observaciones
                        </label>
                        <textarea name="observaciones" 
                                id="observaciones"
                                rows="4" 
                                placeholder="Agregue cualquier información adicional sobre el vehículo (características especiales, modificaciones, etc.)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $vehiculo->observaciones) }}</textarea>
                        @error('observaciones') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

        {{-- Sección de Documentos del Vehículo --}}
        @if(isset($vehiculo->documentos) && $vehiculo->documentos->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-petrodark mb-4">Documentos del Vehículo</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Tipo</th>
                            <th scope="col" class="px-6 py-3">Descripción</th>
                            <th scope="col" class="px-6 py-3">Fecha Vencimiento</th>
                            <th scope="col" class="px-6 py-3">Archivo</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculo->documentos as $documento)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $documento->tipoDocumento->nombre ?? 'Sin tipo' }}
                            </td>
                            <td class="px-6 py-4">{{ $documento->descripcion }}</td>
                            <td class="px-6 py-4">
                                @if($documento->fecha_vencimiento)
                                    {{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}
                                    @if(\Carbon\Carbon::parse($documento->fecha_vencimiento)->isPast())
                                        <span class="text-red-600 text-xs">(Vencido)</span>
                                    @elseif(\Carbon\Carbon::parse($documento->fecha_vencimiento)->diffInDays() <= 30)
                                        <span class="text-yellow-600 text-xs">(Por vencer)</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Sin vencimiento</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($documento->ruta_archivo)
                                    <a href="{{ Storage::url($documento->ruta_archivo) }}" 
                                       target="_blank" 
                                       class="text-blue-600 hover:underline">
                                        Ver archivo
                                    </a>
                                @else
                                    <span class="text-gray-400">Sin archivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="editarDocumento({{ $documento->id }})" 
                                        class="text-blue-600 hover:underline mr-3">
                                    Editar
                                </button>
                                <form action="{{ route('documentos.destroy', $documento->id) }}" 
                                      method="POST" 
                                      class="inline" 
                                      onsubmit="return confirm('¿Está seguro de eliminar este documento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Formulario para agregar nuevos documentos --}}
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-petrodark mb-4">Agregar Nuevo Documento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="nuevo_tipo_documento" class="block text-sm font-medium text-petrodark mb-1">Tipo de Documento</label>
                    <select id="nuevo_tipo_documento" class="w-full rounded-md border-gray-300 shadow-sm focus:border-petroyellow focus:ring-petroyellow">
                        <option value="">Seleccione un tipo</option>
                        @if(isset($tiposDocumento))
                            @foreach($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" 
                                        data-requiere-vencimiento="{{ $tipo->requiere_vencimiento ? '1' : '0' }}">
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label for="nuevo_descripcion" class="block text-sm font-medium text-petrodark mb-1">Descripción</label>
                    <input type="text" 
                           id="nuevo_descripcion" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-petroyellow focus:ring-petroyellow" 
                           placeholder="Descripción del documento">
                </div>
                <div id="fecha_vencimiento_container" style="display: none;">
                    <label for="nuevo_fecha_vencimiento" class="block text-sm font-medium text-petrodark mb-1">Fecha de Vencimiento</label>
                    <input type="date" 
                           id="nuevo_fecha_vencimiento" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-petroyellow focus:ring-petroyellow">
                </div>
                <div>
                    <label for="nuevo_archivo" class="block text-sm font-medium text-petrodark mb-1">Archivo</label>
                    <input type="file" 
                           id="nuevo_archivo" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-petroyellow focus:ring-petroyellow"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>
            </div>
            <div class="mt-4">
                <button type="button" 
                        onclick="agregarDocumento()" 
                        class="px-4 py-2 bg-petroyellow text-petrodark rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Agregar Documento
                </button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Estado de los archivos
    const fileStatus = {
        tarjeta_circulacion: '',
        tenencia_vehicular: '',
        verificacion_vehicular: '',
        poliza_seguro: '',
        factura_pedimento: '',
        manual_vehiculo: '',
        fotografia: '',
        documentos_adicionales: ''
    };
    
    // Función para manejar la selección de archivos
    function handleFileInput(event, type) {
        const file = event.target.files[0];
        const statusElement = document.getElementById(`${type}_status`);
        
        if (!file) {
            statusElement.textContent = type === 'fotografia' ? 'JPG, PNG (máx. 5MB)' : 'PDF, JPG, PNG (máx. 5MB)';
            fileStatus[type] = '';
            return;
        }

        // Validar tamaño (5MB para fotos, 10MB para documentos)
        const maxSize = type === 'fotografia' ? 5 * 1024 * 1024 : 10 * 1024 * 1024;
        if (file.size > maxSize) {
            const maxSizeMB = type === 'fotografia' ? '5MB' : '10MB';
            alert(`El archivo es demasiado grande. Máximo ${maxSizeMB}`);
            event.target.value = '';
            statusElement.textContent = type === 'fotografia' ? 'JPG, PNG (máx. 5MB)' : 'PDF, JPG, PNG (máx. 5MB)';
            fileStatus[type] = '';
            return;
        }

        // Validar tipo de archivo
        let allowedTypes = [];
        if (type === 'fotografia') {
            allowedTypes = ['image/jpeg', 'image/png'];
        } else {
            allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        }
        
        if (!allowedTypes.includes(file.type)) {
            const allowedExtensions = type === 'fotografia' ? 'JPG, PNG' : 'PDF, JPG, PNG, DOC, DOCX';
            alert(`Formato de archivo no permitido. Solo se permiten: ${allowedExtensions}`);
            event.target.value = '';
            statusElement.textContent = type === 'fotografia' ? 'JPG, PNG (máx. 5MB)' : 'PDF, JPG, PNG (máx. 5MB)';
            fileStatus[type] = '';
            return;
        }

        const fileInfo = `✅ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        statusElement.textContent = fileInfo;
        fileStatus[type] = fileInfo;
    }
    
    // Inicializar documentos adicionales (si se necesita)
    let documentosAdicionales = [];
    
    // Función para manejar documentos adicionales (si se necesita)
    function handleAdditionalDocumentFile(event) {
        const file = event.target.files[0];
        if (!file) return null;
        
        // Validar tamaño del archivo (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('El archivo es muy grande. El tamaño máximo es 10MB.');
            event.target.value = '';
            return null;
        }

        // Validar tipo de archivo
        const allowedTypes = [
            'application/pdf', 
            'image/jpeg', 
            'image/jpg', 
            'image/png', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de archivo no válido. Solo se permiten: PDF, JPG, PNG, DOC, DOCX');
            event.target.value = '';
            return null;
        }

        return file;
    }
    
    // Función para manejar múltiples archivos (si se necesita)
    function handleMultipleFileInput(event, type) {
        const files = event.target.files;
        const statusElement = document.getElementById(`${type}_status`);
        
        if (!files || files.length === 0) {
            statusElement.textContent = 'PDF, JPG, PNG (máx. 5MB)';
            fileStatus[type] = '';
            return;
        }

        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            'application/pdf', 
            'image/jpeg', 
            'image/png', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        let validFiles = 0;
        let invalidFiles = 0;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            if (file.size > maxSize || !allowedTypes.includes(file.type)) {
                invalidFiles++;
            } else {
                validFiles++;
            }
        }

        if (invalidFiles > 0) {
            const fileInfo = `⚠️ ${validFiles} archivos válidos, ${invalidFiles} archivos inválidos (tamaño o tipo)`;
            statusElement.textContent = fileInfo;
            fileStatus[type] = fileInfo;
        } else {
            const fileInfo = `✅ ${validFiles} archivos seleccionados`;
            statusElement.textContent = fileInfo;
            fileStatus[type] = fileInfo;
        }
    }
</script>
@endpush
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Intervalos de Mantenimiento
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="intervalo_km_motor" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Motor (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_motor" 
                                   name="intervalo_km_motor" 
                                   min="1000" 
                                   step="500"
                                   placeholder="5000"
                                   value="{{ old('intervalo_km_motor', $vehiculo->intervalo_km_motor) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_motor') border-red-500 @enderror">
                            @error('intervalo_km_motor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite de motor</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo_km_transmision" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Transmisión (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_transmision" 
                                   name="intervalo_km_transmision" 
                                   min="5000" 
                                   step="1000"
                                   placeholder="40000"
                                   value="{{ old('intervalo_km_transmision', $vehiculo->intervalo_km_transmision) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_transmision') border-red-500 @enderror">
                            @error('intervalo_km_transmision') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite de transmisión</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="intervalo_km_hidraulico" class="block text-sm font-medium text-gray-700 mb-2">
                                Intervalo Aceite Hidráulico (km)
                            </label>
                            <input type="number" 
                                   id="intervalo_km_hidraulico" 
                                   name="intervalo_km_hidraulico" 
                                   min="1000" 
                                   step="500"
                                   placeholder="10000"
                                   value="{{ old('intervalo_km_hidraulico', $vehiculo->intervalo_km_hidraulico) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('intervalo_km_hidraulico') border-red-500 @enderror">
                            @error('intervalo_km_hidraulico') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Cada cuántos kilómetros cambiar aceite hidráulico (si aplica)</p>
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Vehículo
                    </h4>

                    <!-- Documentos Estructurados -->
                    <div class="mb-8">
                        <h5 class="text-md font-medium text-gray-800 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Documentos Oficiales
                        </h5>
                        
                        <!-- Mostrar documentos existentes -->
                        @if(isset($vehiculo->documentos) && $vehiculo->documentos->count() > 0)
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                                <h6 class="text-sm font-medium text-blue-800 mb-3">Documentos registrados:</h6>
                                <div class="space-y-2">
                                    @foreach($vehiculo->documentos as $documento)
                                        <div class="flex items-center justify-between p-3 bg-white rounded border border-blue-200">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                                                        {{ $documento->tipoDocumento->nombre_tipo_documento ?? 'Sin tipo' }}
                                                    </span>
                                                    <span class="text-sm text-gray-900">{{ $documento->descripcion }}</span>
                                                </div>
                                                @if($documento->fecha_vencimiento)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Vence: {{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}
                                                        @if(\Carbon\Carbon::parse($documento->fecha_vencimiento)->isPast())
                                                            <span class="text-red-600 font-medium">(VENCIDO)</span>
                                                        @elseif(\Carbon\Carbon::parse($documento->fecha_vencimiento)->diffInDays() <= 30)
                                                            <span class="text-yellow-600 font-medium">(PRÓXIMO A VENCER)</span>
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if($documento->ruta_archivo)
                                                    <a href="{{ asset('storage/' . $documento->ruta_archivo) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                                        Ver archivo
                                                    </a>
                                                @endif
                                                <button type="button" 
                                                        onclick="editarDocumento('{{ $documento->id }}')"
                                                        class="text-green-600 hover:text-green-800 text-sm">
                                                    Editar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Formulario para agregar nuevo documento -->
                        <div class="border border-gray-300 rounded-lg p-4">
                            <h6 class="text-sm font-medium text-gray-700 mb-3">Agregar nuevo documento oficial:</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nuevo_tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tipo de Documento
                                    </label>
                                    <select id="nuevo_tipo_documento" 
                                            name="nuevo_tipo_documento" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                        <option value="">Seleccionar tipo...</option>
                                        @foreach($tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id }}" data-requiere-vencimiento="{{ $tipo->requiere_vencimiento }}">
                                                {{ $tipo->nombre_tipo_documento }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="nuevo_descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                                        Descripción
                                    </label>
                                    <input type="text" 
                                           id="nuevo_descripcion" 
                                           name="nuevo_descripcion" 
                                           placeholder="Ej: Tarjeta de circulación 2025"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                </div>
                                <div id="fecha_vencimiento_container" style="display: none;">
                                    <label for="nuevo_fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">
                                        Fecha de Vencimiento
                                    </label>
                                    <input type="date" 
                                           id="nuevo_fecha_vencimiento" 
                                           name="nuevo_fecha_vencimiento" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                </div>
                                <div>
                                    <label for="nuevo_archivo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Archivo (Opcional)
                                    </label>
                                    <input type="file" 
                                           id="nuevo_archivo" 
                                           name="nuevo_archivo" 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" 
                                        onclick="agregarDocumento()"
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Agregar Documento
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos Adicionales -->
                    <div>
                        <h5 class="text-md font-medium text-gray-800 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Documentos Adicionales (Archivos Múltiples)
                        </h5>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Subir múltiples archivos (Opcional)
                        </label>
                        @if(isset($vehiculo->documentos_adicionales) && is_array($vehiculo->documentos_adicionales) && count($vehiculo->documentos_adicionales) > 0)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Archivos actuales:</h6>
                                <div class="space-y-2">
                                    @foreach($vehiculo->documentos_adicionales as $index => $documento)
                                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                                            <span class="text-sm text-gray-600">{{ basename($documento) }}</span>
                                            <a href="{{ asset('storage/' . $documento) }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                Ver archivo
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <input type="file" 
                                   id="documentos_adicionales" 
                                   name="documentos_adicionales[]" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                                   multiple
                                   class="hidden" 
                                   @change="handleMultipleFileInput($event, 'documentos_adicionales')" />
                            <label for="documentos_adicionales" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M8 14v20c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252M8 14c0 4.418 7.163 8 16 8s16-3.582 16-8M8 14c0-4.418 7.163-8 16-8s16 3.582 16 8m0 0v14m-16-5c0 4.418 7.163 8 16 8 1.381 0 2.721-.087 4-.252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-gray-900">{{ isset($vehiculo->documentos_adicionales) && count($vehiculo->documentos_adicionales) > 0 ? 'Cambiar documentos adicionales' : 'Subir documentos adicionales' }}</span>
                                <span class="mt-1 block text-sm text-gray-500">PDF, JPG, PNG, DOC hasta 10MB cada uno</span>
                                <span class="mt-1 block text-xs text-gray-400">Puede seleccionar múltiples archivos</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" x-text="fileStatus.documentos_adicionales || ''"></p>
                        @error('documentos_adicionales.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Operador Asignado -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Asignar Operador
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-6">
                        <!-- Operador Asignado -->
                        <div class="form-group">
                            <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Operador Asignado
                            </label>
                            <select name="operador_id" 
                                    id="operador_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('operador_id') border-red-500 @enderror">
                                <option value="">Seleccione un operador (opcional)</option>
                                @if(isset($operadores) && $operadores->count() > 0)
                                    @foreach($operadores as $operador)
                                        <option value="{{ $operador->id }}" {{ (old('operador_id', $vehiculo->operador_id) == $operador->id) ? 'selected' : '' }}>
                                            {{ $operador->nombre_completo }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('operador_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Persona encargada de operar el vehículo</p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Observaciones Adicionales
                    </h3>
                    
                    <div class="form-group">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                            Notas y Observaciones
                        </label>
                        <textarea name="observaciones" 
                                id="observaciones"
                                rows="4" 
                                placeholder="Agregue cualquier información adicional sobre el vehículo (características especiales, modificaciones, etc.)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $vehiculo->observaciones) }}</textarea>
                        @error('observaciones') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Historial de Cambios -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        Información del Registro
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Información de Creación</h4>
                            <p class="text-sm text-gray-600">Creado el: <strong>{{ isset($vehiculo->created_at) && $vehiculo->created_at ? $vehiculo->created_at->format('d/m/Y - H:i') : 'No disponible' }}</strong></p>
                            <p class="text-sm text-gray-600">Por: <strong>{{ $vehiculo->created_by ?? 'Sistema' }}</strong></p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Última Modificación</h4>
                            <p class="text-sm text-gray-600">Modificado el: <strong>{{ isset($vehiculo->updated_at) && $vehiculo->updated_at ? $vehiculo->updated_at->format('d/m/Y - H:i') : 'No disponible' }}</strong></p>
                            <p class="text-sm text-gray-600">Por: <strong>{{ $vehiculo->updated_by ?? 'Sistema' }}</strong></p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Importante</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Los cambios que realices se registrarán en el historial del vehículo. Asegúrate de que toda la información sea correcta antes de guardar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('vehiculos.show', $vehiculo->id) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Actualizar Vehículo
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vehiculoEditFormController', () => ({
            fileStatus: {
                documentos_adicionales: ''
            },

            handleMultipleFileInput(event) {
                const files = Array.from(event.target.files);
                if (files.length === 0) {
                    this.fileStatus.documentos_adicionales = '';
                    return;
                }

                const maxSize = 10 * 1024 * 1024; // 10MB por archivo
                const allowedTypes = [
                    'application/pdf',
                    'image/jpeg',
                    'image/png', 
                    'image/jpg',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];

                for (let file of files) {
                    if (file.size > maxSize) {
                        alert(`El archivo ${file.name} es demasiado grande. Máximo 10MB por archivo`);
                        event.target.value = '';
                        this.fileStatus.documentos_adicionales = '';
                        return;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        alert(`El archivo ${file.name} tiene un formato no permitido`);
                        event.target.value = '';
                        this.fileStatus.documentos_adicionales = '';
                        return;
                    }
                }

                this.fileStatus.documentos_adicionales = `${files.length} archivo(s) seleccionado(s): ${files.map(f => f.name).join(', ')}`;
            }
        }));
    });

    // Función para mostrar/ocultar el campo de fecha de vencimiento según el tipo de documento
    document.addEventListener('DOMContentLoaded', function() {
        const tipoDocumentoSelect = document.getElementById('nuevo_tipo_documento');
        const fechaVencimientoContainer = document.getElementById('fecha_vencimiento_container');
        
        if (tipoDocumentoSelect && fechaVencimientoContainer) {
            tipoDocumentoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const requiereVencimiento = selectedOption.getAttribute('data-requiere-vencimiento');
                
                if (requiereVencimiento === '1') {
                    fechaVencimientoContainer.style.display = 'block';
                } else {
                    fechaVencimientoContainer.style.display = 'none';
                    document.getElementById('nuevo_fecha_vencimiento').value = '';
                }
            });
        }
    });
    
    // Función para agregar un nuevo documento estructurado
    function agregarDocumento() {
        const tipoDocumentoId = document.getElementById('nuevo_tipo_documento').value;
        const descripcion = document.getElementById('nuevo_descripcion').value;
        const fechaVencimiento = document.getElementById('nuevo_fecha_vencimiento').value;
        const archivoInput = document.getElementById('nuevo_archivo');
        
        // Validaciones básicas
        if (!tipoDocumentoId) {
            alert('Debe seleccionar un tipo de documento');
            return;
        }
        
        if (!descripcion) {
            alert('Debe ingresar una descripción');
            return;
        }
        
        // Verificar si el tipo requiere fecha de vencimiento
        const tipoDocumentoSelect = document.getElementById('nuevo_tipo_documento');
        const selectedOption = tipoDocumentoSelect.options[tipoDocumentoSelect.selectedIndex];
        const requiereVencimiento = selectedOption.getAttribute('data-requiere-vencimiento');
        
        if (requiereVencimiento === '1' && !fechaVencimiento) {
            alert('Este tipo de documento requiere fecha de vencimiento');
            return;
        }
        
        // Crear FormData para enviar los datos
        const formData = new FormData();
        formData.append('tipo_documento_id', tipoDocumentoId);
        formData.append('descripcion', descripcion);
        formData.append('vehiculo_id', '{{ $vehiculo->id }}');
        
        if (fechaVencimiento) {
            formData.append('fecha_vencimiento', fechaVencimiento);
        }
        
        if (archivoInput.files.length > 0) {
            formData.append('archivo', archivoInput.files[0]);
        }
        
        // Mostrar indicador de carga
        const btnAgregar = document.querySelector('button[onclick="agregarDocumento()"]');
        const textoOriginal = btnAgregar.textContent;
        btnAgregar.textContent = 'Guardando...';
        btnAgregar.disabled = true;
        
        // Enviar solicitud AJAX
        fetch('{{ route("documentos.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Documento guardado exitosamente');
                // Recargar la página para mostrar el nuevo documento
                window.location.reload();
            } else {
                let errorMessage = 'Error al guardar el documento';
                if (data.errors) {
                    errorMessage += ': ' + Object.values(data.errors).flat().join(', ');
                } else if (data.message) {
                    errorMessage += ': ' + data.message;
                }
                alert(errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar el documento. Por favor, intente nuevamente.');
        })
        .finally(() => {
            // Restaurar el botón
            btnAgregar.textContent = textoOriginal;
            btnAgregar.disabled = false;
        });
    }

    // Función para editar documento (placeholder)
    function editarDocumento(documentoId) {
        // Por ahora solo mostrar un mensaje, se puede implementar un modal más adelante
        alert('Función de edición en desarrollo. ID del documento: ' + documentoId);
    }
</script>
@endpush
