@extends('layouts.app')

@section('title', 'Editar Activo')

@section('header', 'Editar Activo')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Activos', 'url' => route('vehiculos.index')],
        ['label' => 'Editar Activo']
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
        <h2 class="text-2xl font-bold text-gray-800">Editar Activo</h2>
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
        <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST" enctype="multipart/form-data" id="activoForm">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <!-- Información del Activo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1 1 0 11-3 0 1.5 1.5 0 013 0z"/>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                        </svg>
                        Información del Activo
                    </h3>
                    
                    <!-- Tipo de Activo -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-6">
                        <div class="form-group">
                            <label for="tipo_activo_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Activo <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo_activo_id" 
                                    id="tipo_activo_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('tipo_activo_id') border-red-500 @enderror">
                                <option value="">Seleccione un tipo de activo</option>
                                @if(isset($tiposActivo) && $tiposActivo->count() > 0)
                                    @foreach($tiposActivo as $tipoActivo)
                                        <option value="{{ $tipoActivo->id }}" {{ (old('tipo_activo_id', $vehiculo->tipo_activo_id) == $tipoActivo->id) ? 'selected' : '' }}>
                                            {{ $tipoActivo->nombre }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('tipo_activo_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Seleccione el tipo de activo que está registrando</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-form-input name="marca" label="Marca" required placeholder="Ej: Ford, Chevrolet, Toyota" :value="$vehiculo->marca" />
                        <x-form-input name="modelo" label="Modelo" required placeholder="Ej: F-150, Silverado, Hilux" :value="$vehiculo->modelo" />
                        <div>
                            <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">
                                Año
                            </label>
                            <input type="number" 
                                   name="anio" 
                                   id="anio" 
                                   min="1950" 
                                   max="2025" 
                                   placeholder="2023" 
                                   value="{{ old('anio', $vehiculo->anio) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('anio') border-red-500 @enderror">
                            @error('anio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Año de fabricación del activo (opcional)</p>
                        </div>
                    </div>
                    
                    <!-- Valor Comercial y Propietario -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="valor_comercial" class="block text-sm font-medium text-gray-700 mb-2">
                                Valor Comercial (MXN)
                            </label>
                            <input type="number" 
                                   name="valor_comercial" 
                                   id="valor_comercial" 
                                   min="0" 
                                   step="0.01"
                                   placeholder="250000.00" 
                                   value="{{ old('valor_comercial', $vehiculo->valor_comercial) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('valor_comercial') border-red-500 @enderror">
                            @error('valor_comercial') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Valor comercial del activo en pesos mexicanos (opcional)</p>
                        </div>
                        <div>
                            <label for="propietario" class="block text-sm font-medium text-gray-700 mb-2">
                                Propietario
                            </label>
                            <input type="text" 
                                   name="propietario" 
                                   id="propietario" 
                                   placeholder="Ej: Petrotekno S.A. de C.V." 
                                   value="{{ old('propietario', $vehiculo->propietario) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('propietario') border-red-500 @enderror">
                            @error('propietario') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Nombre del propietario del activo (opcional)</p>
                        </div>
                    </div>
                    
                    <!-- Campos Dinámicos según Tipo de Activo -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <!-- Número de Serie -->
                        <div id="numero-serie-field">
                            <div class="form-group">
                                <label for="n_serie" id="numero-serie-label" class="block text-sm font-medium text-gray-700 mb-2">
                                    Número de Serie (VIN) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="n_serie" 
                                       id="n_serie" 
                                       placeholder="1FTFW1ET5DFA12345"
                                       value="{{ old('n_serie', $vehiculo->n_serie) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('n_serie') border-red-500 @enderror">
                                @error('n_serie') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-500">Número de serie único del activo</p>
                            </div>
                        </div>
                        
                        <!-- Placas -->
                        <div id="placas-field">
                            <div class="form-group">
                                <label for="placas" id="placas-label" class="block text-sm font-medium text-gray-700 mb-2">
                                    Placas
                                </label>
                                <input type="text" 
                                       name="placas" 
                                       id="placas" 
                                       placeholder="ABC-123-A"
                                       value="{{ old('placas', $vehiculo->placas) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('placas') border-red-500 @enderror">
                                @error('placas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-500">Placas de circulación del activo</p>
                            </div>
                        </div>
                        
                        <!-- Kilometraje -->
                        <div id="kilometraje-field">
                            <div class="form-group">
                                <label for="kilometraje_actual" id="kilometraje-label" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kilometraje Actual (km)
                                </label>
                                <input type="number" 
                                       name="kilometraje_actual" 
                                       id="kilometraje_actual" 
                                       min="0" 
                                       placeholder="15000"
                                       value="{{ old('kilometraje_actual', $vehiculo->kilometraje_actual) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('kilometraje_actual') border-red-500 @enderror">
                                @error('kilometraje_actual') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-500">Kilometraje actual del activo</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado y Municipio -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="form-group">
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado
                            </label>
                            <select name="estado" 
                                    id="estado" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('estado') border-red-500 @enderror">
                                <option value="">Seleccione un estado</option>
                                <option value="{{ old('estado', $vehiculo->estado) }}" {{ old('estado') ? 'selected' : '' }}>{{ old('estado') }}</option>
                            </select>
                            @error('estado') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Estado donde se encuentra el activo</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="municipio" class="block text-sm font-medium text-gray-700 mb-2">
                                Municipio
                            </label>
                            <select name="municipio" 
                                    id="municipio" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('municipio') border-red-500 @enderror">
                                <option value="">Seleccione un municipio</option>
                                <option value="{{ old('municipio', $vehiculo->municipio) }}" {{ old('municipio') ? 'selected' : '' }}>{{ old('municipio') }}</option>
                            </select>
                            @error('municipio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Municipio donde se encuentra el activo</p>
                        </div>
                    </div>

                    <!-- Responsable Asignado -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-6">
                        <div class="form-group">
                            <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Responsable Asignado
                            </label>
                            <select name="operador_id" 
                                    id="operador_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('operador_id') border-red-500 @enderror">
                                <option value="">Seleccione un responsable (opcional)</option>
                                @if(isset($operadores) && $operadores->count() > 0)
                                    @foreach($operadores as $operador)
                                        <option value="{{ $operador->id }}" {{ (old('operador_id', $vehiculo->operador_id) == $operador->id) ? 'selected' : '' }}>
                                            {{ $operador->nombre_completo }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('operador_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Persona responsable del activo (puede ser cualquier personal activo)</p>
                        </div>
                    </div>
                    
                    <!-- Fin de la sección de información básica -->
                </div>

                <!-- Sección de Documentos Específicos del Activo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos del Activo
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
                                
                                <!-- Campo Número de Póliza -->
                                <div class="form-group">
                                    <label for="numero_poliza" class="block text-sm font-medium text-gray-700 mb-2">
                                        Número de Póliza
                                    </label>
                                    <input type="text" 
                                           name="numero_poliza" 
                                           id="numero_poliza" 
                                           value="{{ old('numero_poliza', $vehiculo->numero_poliza) }}"
                                           placeholder="Ej: 190324"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('numero_poliza') border-red-500 @enderror">
                                    @error('numero_poliza') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500">Número de la póliza de seguro del activo</p>
                                </div>
                                
                                @if($vehiculo->poliza_url)
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-800">Archivo subido</span>
                                        </div>
                                        <a href="{{ asset('storage/' . $vehiculo->poliza_url) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline">Ver archivo</a>
                                    </div>
                                @endif
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
                                            <span class="text-base">{{ $vehiculo->poliza_url ? 'Cambiar' : 'Subir' }} Póliza de Seguro</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_seguro" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_seguro', $vehiculo->fecha_vencimiento_seguro) }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="poliza_seguro_status">PDF, JPG, PNG (máx. 5MB)</p>
                            </div>

                            <!-- 2. Derecho del Activo -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Derecho del Activo
                                </label>
                                @if($vehiculo->derecho_url)
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-800">Archivo subido</span>
                                        </div>
                                        <a href="{{ asset('storage/' . $vehiculo->derecho_url) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline">Ver archivo</a>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center">
                                    <div class="w-full">
                                        <input type="file" 
                                               id="derecho_vehicular_file" 
                                               name="derecho_vehicular_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               onchange="handleFileInput(event, 'derecho_activo')" />
                                        <label for="derecho_vehicular_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <span class="text-base">{{ $vehiculo->derecho_url ? 'Cambiar' : 'Subir' }} Derecho del Activo</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_derecho" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_derecho', $vehiculo->fecha_vencimiento_derecho) }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="derecho_activo_status">PDF, JPG, PNG (máx. 5MB)</p>
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
                                @if($vehiculo->factura_url)
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-800">Archivo subido</span>
                                        </div>
                                        <a href="{{ asset('storage/' . $vehiculo->factura_url) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline">Ver archivo</a>
                                    </div>
                                @endif
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
                                            <span class="text-base">{{ $vehiculo->factura_url ? 'Cambiar' : 'Subir' }} Factura y/o Pedimento</span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="factura_pedimento_status">PDF, JPG, PNG (máx. 5MB)</p>
                            </div>

                            <!-- 4. Fotografía del Activo -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Fotografía del Activo
                                </label>
                                @if($vehiculo->url_imagen)
                                    <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-800">Fotografía subida</span>
                                        </div>
                                        <a href="{{ asset('storage/' . $vehiculo->url_imagen) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline">Ver imagen</a>
                                    </div>
                                @endif
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
                                            <span class="text-base">{{ $vehiculo->url_imagen ? 'Cambiar' : 'Subir' }} Fotografía del Activo</span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 text-center file-status" id="fotografia_status">JPG, PNG (máx. 5MB)</p>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Configuración de Mantenimiento -->
                <div id="mantenimiento-section" class="bg-white border border-gray-200 rounded-lg p-6">
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
                                placeholder="Agregue cualquier información adicional sobre el activo (características especiales, modificaciones, etc.)" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                        @error('observaciones') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8"></div>

            {{-- Información de Auditoría --}}
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
                            {{ $vehiculo->created_at ? $vehiculo->created_at->format('d/m/Y H:i') : 'No disponible' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Última Actualización</label>
                        <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                            {{ $vehiculo->updated_at ? $vehiculo->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-between items-center">
                <!-- Botón Dar de Baja (izquierda) -->
                @if(!in_array($vehiculo->estatus?->value, ['baja', 'baja_por_venta', 'baja_por_perdida']))
                <button type="button" 
                        onclick="openBajaModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Dar de Baja Activo
                </button>
                @else
                <div></div> <!-- Espacio vacío cuando el activo ya está dado de baja -->
                @endif

                <!-- Botones de Guardar y Cancelar (derecha) -->
                <div class="flex space-x-4">
                    <a href="{{ route('vehiculos.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                        Guardar Activo
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de Baja Simple -->
    <div id="bajaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Encabezado -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Dar de Baja el Activo</h3>
                    <button onclick="closeBajaModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Formulario -->
                <form id="bajaForm" class="mt-4">
                    @csrf
                    <div class="space-y-4">
                        <!-- Tipo de Baja -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Baja <span class="text-red-500">*</span>
                            </label>
                            <select id="tipo_baja" name="tipo_baja" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Seleccione un tipo</option>
                                <option value="baja">Baja General</option>
                                <option value="baja_por_venta">Baja por Venta</option>
                                <option value="baja_por_perdida">Baja por Pérdida</option>
                            </select>
                        </div>

                        <!-- Observación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones/Motivo (opcional)
                            </label>
                            <textarea id="observacion_baja" 
                                      name="observacion_baja" 
                                      rows="4" 
                                      maxlength="1000"
                                      placeholder="Describa el motivo de la baja..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                            <p class="mt-1 text-xs text-gray-500">La fecha y hora se agregarán automáticamente</p>
                        </div>
                    </div>

                    <!-- Mensaje de error -->
                    <div id="bajaError" class="hidden mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded"></div>

                    <!-- Botones -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeBajaModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" 
                                id="btnConfirmarBaja"
                                class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Confirmar Baja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/estados-municipios.js') }}"></script>
<script>
    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        const tipoActivoSelect = document.getElementById('tipo_activo_id');
        const kilometrajeField = document.getElementById('kilometraje-field');
        const kilometrajeInput = document.getElementById('kilometraje_actual');
        const mantenimientoSection = document.getElementById('mantenimiento-section');
        
        // Obtener referencias a los nuevos campos
        const placasField = document.getElementById('placas-field');
        const placasInput = document.getElementById('placas');
        const numeroSerieField = document.getElementById('numero-serie-field');
        const numeroSerieInput = document.getElementById('n_serie');
        
        // Obtener todos los campos de intervalos de mantenimiento
        const intervalosFields = [
            document.getElementById('intervalo_km_motor'),
            document.getElementById('intervalo_km_transmision'),
            document.getElementById('intervalo_km_hidraulico')
        ];
        
        function toggleKilometrajeFields() {
            const tipoActivoId = tipoActivoSelect.value;
            
            if (!tipoActivoId) {
                // Si no hay tipo de activo seleccionado, ocultar todo
                hideKilometrajeFields();
                hidePlacasFields();
                hideNumeroSerieFields();
                return;
            }
            
            // Hacer petición AJAX para obtener información del tipo de activo
            fetch(`/tipos-activos/${tipoActivoId}/info`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const tieneKilometraje = data.data.tiene_kilometraje;
                        const tienePlaca = data.data.tiene_placa;
                        const tieneNumeroSerie = data.data.tiene_numero_serie;
                        
                        // Manejar campo de kilometraje
                        if (tieneKilometraje) {
                            showKilometrajeFields();
                        } else {
                            hideKilometrajeFields();
                        }
                        
                        // Manejar campo de placas
                        if (tienePlaca) {
                            showPlacasFields();
                        } else {
                            hidePlacasFields();
                        }
                        
                        // Manejar campo de número de serie
                        if (tieneNumeroSerie) {
                            showNumeroSerieFields();
                        } else {
                            hideNumeroSerieFields();
                        }
                    } else {
                        console.error('Error al obtener información del tipo de activo');
                        hideKilometrajeFields();
                        hidePlacasFields();
                        hideNumeroSerieFields();
                    }
                })
                .catch(error => {
                    console.error('Error en la petición AJAX:', error);
                    hideKilometrajeFields();
                    hidePlacasFields();
                    hideNumeroSerieFields();
                });
        }
        
        function showKilometrajeFields() {
            // Mostrar y habilitar campo de kilometraje
            if (kilometrajeField) {
                kilometrajeField.style.display = 'block';
            }
            if (kilometrajeInput) {
                kilometrajeInput.disabled = false;
                kilometrajeInput.required = true;
                kilometrajeInput.style.backgroundColor = '';
                kilometrajeInput.style.color = '';
            }
            
            // Mostrar sección de mantenimiento e intervalos
            if (mantenimientoSection) {
                mantenimientoSection.style.display = 'block';
            }
            
            // Habilitar campos de intervalos
            intervalosFields.forEach(field => {
                if (field) {
                    field.disabled = false;
                    field.style.backgroundColor = '';
                    field.style.color = '';
                }
            });
        }
        
        function hideKilometrajeFields() {
            // Deshabilitar y poner en gris el campo de kilometraje
            if (kilometrajeField) {
                kilometrajeField.style.display = 'block'; // Mantener visible pero deshabilitado
            }
            if (kilometrajeInput) {
                kilometrajeInput.disabled = true;
                kilometrajeInput.required = false;
                kilometrajeInput.value = '';
                kilometrajeInput.style.backgroundColor = '#f3f4f6';
                kilometrajeInput.style.color = '#9ca3af';
            }
            
            // Ocultar sección de intervalos de mantenimiento
            if (mantenimientoSection) {
                mantenimientoSection.style.display = 'none';
            }
            
            // Deshabilitar y limpiar campos de intervalos
            intervalosFields.forEach(field => {
                if (field) {
                    field.disabled = true;
                    field.value = '';
                    field.style.backgroundColor = '#f3f4f6';
                    field.style.color = '#9ca3af';
                }
            });
        }
        
        // Funciones para manejar campo de placas
        function showPlacasFields() {
            const placasField = document.getElementById('placas-field');
            const placasInput = document.getElementById('placas');
            const placasLabel = document.getElementById('placas-label');
            
            if (placasField) {
                placasField.style.display = 'block';
            }
            if (placasInput) {
                placasInput.disabled = false;
                placasInput.required = true;
                placasInput.style.backgroundColor = '';
                placasInput.style.color = '';
            }
            if (placasLabel) {
                placasLabel.innerHTML = 'Placas <span class="text-red-500">*</span>';
            }
        }
        
        function hidePlacasFields() {
            const placasField = document.getElementById('placas-field');
            const placasInput = document.getElementById('placas');
            const placasLabel = document.getElementById('placas-label');
            
            if (placasField) {
                placasField.style.display = 'block'; // Mantener visible pero deshabilitado
            }
            if (placasInput) {
                placasInput.disabled = true;
                placasInput.required = false;
                placasInput.value = '';
                placasInput.style.backgroundColor = '#f3f4f6';
                placasInput.style.color = '#9ca3af';
            }
            if (placasLabel) {
                placasLabel.innerHTML = 'Placas';
            }
        }
        
        // Funciones para manejar campo de número de serie
        function showNumeroSerieFields() {
            const numeroSerieField = document.getElementById('numero-serie-field');
            const numeroSerieInput = document.getElementById('n_serie');
            const numeroSerieLabel = document.getElementById('numero-serie-label');
            
            if (numeroSerieField) {
                numeroSerieField.style.display = 'block';
            }
            if (numeroSerieInput) {
                numeroSerieInput.disabled = false;
                numeroSerieInput.required = true;
                numeroSerieInput.style.backgroundColor = '';
                numeroSerieInput.style.color = '';
            }
        }
        
        function hideNumeroSerieFields() {
            const numeroSerieField = document.getElementById('numero-serie-field');
            const numeroSerieInput = document.getElementById('n_serie');
            const numeroSerieLabel = document.getElementById('numero-serie-label');
            
            if (numeroSerieField) {
                numeroSerieField.style.display = 'block'; // Mantener visible pero deshabilitado
            }
            if (numeroSerieInput) {
                numeroSerieInput.disabled = true;
                numeroSerieInput.required = false;
                numeroSerieInput.value = '';
                numeroSerieInput.style.backgroundColor = '#f3f4f6';
                numeroSerieInput.style.color = '#9ca3af';
            }
        }
        
        // Ejecutar al cargar la página
        toggleKilometrajeFields();
        
        // Ejecutar cuando cambie la selección
        if (tipoActivoSelect) {
            tipoActivoSelect.addEventListener('change', toggleKilometrajeFields);
        }
        
        // Validación adicional antes de enviar el formulario
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Validar campos básicos requeridos
                const camposBasicos = [
                    { id: 'tipo_activo_id', nombre: 'Tipo de Activo' },
                    { id: 'marca', nombre: 'Marca' },
                    { id: 'modelo', nombre: 'Modelo' }
                ];
                
                let errores = [];
                
                // Validar campos básicos
                camposBasicos.forEach(campo => {
                    const input = document.getElementById(campo.id);
                    if (input && (!input.value || input.value.trim() === '')) {
                        errores.push(campo.nombre);
                        input.classList.add('border-red-500');
                    } else if (input) {
                        input.classList.remove('border-red-500');
                    }
                });
                
                // Validar campos dinámicos visibles y habilitados
                if (numeroSerieInput && !numeroSerieInput.disabled && numeroSerieInput.required && (!numeroSerieInput.value || numeroSerieInput.value.trim() === '')) {
                    errores.push('Número de Serie');
                    numeroSerieInput.classList.add('border-red-500');
                } else if (numeroSerieInput) {
                    numeroSerieInput.classList.remove('border-red-500');
                }
                
                if (placasInput && !placasInput.disabled && placasInput.required && (!placasInput.value || placasInput.value.trim() === '')) {
                    errores.push('Placas');
                    placasInput.classList.add('border-red-500');
                } else if (placasInput) {
                    placasInput.classList.remove('border-red-500');
                }
                
                if (kilometrajeInput && !kilometrajeInput.disabled && kilometrajeInput.required && (!kilometrajeInput.value || kilometrajeInput.value.trim() === '')) {
                    errores.push('Kilometraje Actual');
                    kilometrajeInput.classList.add('border-red-500');
                } else if (kilometrajeInput) {
                    kilometrajeInput.classList.remove('border-red-500');
                }
                
                // Si hay errores, prevenir el envío
                if (errores.length > 0) {
                    e.preventDefault();
                    
                    // Mostrar mensaje de error
                    alert('Por favor, complete los siguientes campos obligatorios:\n\n• ' + errores.join('\n• '));
                    
                    // Enfocar el primer campo con error
                    const primerCampoError = camposBasicos.find(campo => {
                        const input = document.getElementById(campo.id);
                        return input && (!input.value || input.value.trim() === '');
                    });
                    
                    if (primerCampoError) {
                        const input = document.getElementById(primerCampoError.id);
                        if (input) input.focus();
                    }
                    
                    return false;
                }
            });
        }
    });
    
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
        const labelElement = event.target.nextElementSibling;
        
        if (!file) {
            statusElement.textContent = type === 'fotografia' ? 'JPG, PNG (máx. 5MB)' : 'PDF, JPG, PNG (máx. 5MB)';
            labelElement.classList.remove('border-green-300', 'bg-green-50');
            labelElement.classList.add('border-gray-300', 'bg-white');
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
            labelElement.classList.remove('border-green-300', 'bg-green-50');
            labelElement.classList.add('border-gray-300', 'bg-white');
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
            labelElement.classList.remove('border-green-300', 'bg-green-50');
            labelElement.classList.add('border-gray-300', 'bg-white');
            fileStatus[type] = '';
            return;
        }

        // Archivo válido - actualizar la UI
        const fileInfo = `✅ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
        statusElement.textContent = fileInfo;
        labelElement.classList.remove('border-gray-300', 'bg-white');
        labelElement.classList.add('border-green-300', 'bg-green-50');
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

    // ===== FUNCIONES PARA MODAL DE BAJA =====
    function openBajaModal() {
        document.getElementById('bajaModal').classList.remove('hidden');
    }

    function closeBajaModal() {
        document.getElementById('bajaModal').classList.add('hidden');
        document.getElementById('bajaForm').reset();
        document.getElementById('bajaError').classList.add('hidden');
    }

    // Manejo del formulario de baja
    document.getElementById('bajaForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnConfirmarBaja');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Procesando...';
        
        const formData = new FormData(this);
        
        fetch('{{ route("vehiculos.dar-baja", $vehiculo) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error('HTTP error! status: ' + response.status);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                document.getElementById('bajaError').textContent = data.message || 'Error desconocido';
                document.getElementById('bajaError').classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('bajaError').textContent = 'Error al procesar la solicitud: ' + error.message;
            document.getElementById('bajaError').classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById('bajaModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeBajaModal();
    });
</script>

<script src="{{ asset('js/estados-municipios.js') }}"></script>
@endpush
