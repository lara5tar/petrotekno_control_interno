@extends('layouts.app')

@section('title', 'Agregar Vehículo')

@section('header', 'Agregar Vehículo')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
        ['label' => 'Agregar Vehículo']
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
        <h2 class="text-2xl font-bold text-gray-800">Agregar Nuevo Vehículo</h2>
        <a href="{{ route('vehiculos.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6" x-data="vehiculoFormController()">
        <form action="{{ route('vehiculos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
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
                        <x-form-input name="marca" label="Marca" required placeholder="Ej: Ford, Chevrolet, Toyota" />
                        <x-form-input name="modelo" label="Modelo" required placeholder="Ej: F-150, Silverado, Hilux" />
                        <x-form-input name="anio" label="Año" type="number" required min="1990" max="2025" placeholder="2023" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <x-form-input name="n_serie" label="Número de Serie (VIN)" required placeholder="1FTFW1ET5DFA12345" />
                        <x-form-input name="placas" label="Placas" required placeholder="ABC-123-A" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <x-form-input name="kilometraje_actual" label="Kilometraje Actual (km)" type="number" required min="0" placeholder="15000" />
                        
                        <div class="form-group">
                            <label for="estatus_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Estatus <span class="text-red-500">*</span>
                            </label>
                            <select name="estatus_id" 
                                    id="estatus_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('estatus_id') border-red-500 @enderror">
                                <option value="">Seleccione el estatus</option>
                                @foreach($estatus as $estado)
                                    <option value="{{ $estado->id }}" {{ old('estatus_id') == $estado->id ? 'selected' : '' }}>
                                        {{ ucfirst($estado->nombre_estatus) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estatus_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos Específicos del Vehículo -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos Específicos del Vehículo
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Primera columna -->
                        <div class="space-y-6">
                            <!-- 1. Tarjeta de Circulación -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg bg-blue-50">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Tarjeta de Circulación
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_tarjeta_circulacion" 
                                           placeholder="Número de tarjeta de circulación" 
                                           value="{{ old('no_tarjeta_circulacion') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="tarjeta_circulacion_file" 
                                               name="tarjeta_circulacion_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'tarjeta_circulacion')" />
                                        <label for="tarjeta_circulacion_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_tarjeta" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_tarjeta') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.tarjeta_circulacion || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 2. Tenencia Vehicular -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg bg-green-50">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Tenencia Vehicular
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_tenencia_vehicular" 
                                           placeholder="Número de tenencia vehicular" 
                                           value="{{ old('no_tenencia_vehicular') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="tenencia_vehicular_file" 
                                               name="tenencia_vehicular_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'tenencia_vehicular')" />
                                        <label for="tenencia_vehicular_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_tenencia" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_tenencia') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.tenencia_vehicular || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 3. Verificación Vehicular -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg bg-yellow-50">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Verificación Vehicular
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_verificacion_vehicular" 
                                           placeholder="Número de verificación" 
                                           value="{{ old('no_verificacion_vehicular') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="verificacion_vehicular_file" 
                                               name="verificacion_vehicular_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'verificacion_vehicular')" />
                                        <label for="verificacion_vehicular_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="date" 
                                           name="fecha_vencimiento_verificacion" 
                                           placeholder="Fecha de vencimiento"
                                           value="{{ old('fecha_vencimiento_verificacion') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <span class="text-sm text-gray-500 flex-shrink-0">Vencimiento</span>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.verificacion_vehicular || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>
                        </div>

                        <!-- Segunda columna -->
                        <div class="space-y-6">
                            <!-- 4. Póliza de Seguro -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg bg-red-50">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Póliza de Seguro
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_poliza_seguro" 
                                           placeholder="Número de póliza" 
                                           value="{{ old('no_poliza_seguro') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="poliza_seguro_file" 
                                               name="poliza_seguro_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'poliza_seguro')" />
                                        <label for="poliza_seguro_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
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
                                <div class="mt-2">
                                    <input type="text" 
                                           name="aseguradora" 
                                           placeholder="Nombre de la aseguradora" 
                                           value="{{ old('aseguradora') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.poliza_seguro || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 5. Factura de Compra -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg bg-purple-50">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                                    </svg>
                                    Factura de Compra
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           name="no_factura_compra" 
                                           placeholder="Número de factura" 
                                           value="{{ old('no_factura_compra') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                                    <div class="flex-shrink-0">
                                        <input type="file" 
                                               id="factura_compra_file" 
                                               name="factura_compra_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'factura_compra')" />
                                        <label for="factura_compra_file" 
                                               class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Adjuntar
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500" x-text="fileStatus.factura_compra || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>

                            <!-- 6. Manual del Vehículo -->
                            <div class="space-y-3 p-4 border border-gray-200 rounded-lg bg-indigo-50">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    Manual del Vehículo
                                </label>
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1">
                                        <input type="file" 
                                               id="manual_vehiculo_file" 
                                               name="manual_vehiculo_file" 
                                               accept=".pdf,.jpg,.jpeg,.png" 
                                               class="hidden" 
                                               @change="handleFileInput($event, 'manual_vehiculo')" />
                                        <label for="manual_vehiculo_file" 
                                               class="cursor-pointer inline-flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            Subir Manual del Vehículo
                                        </label>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 text-center" x-text="fileStatus.manual_vehiculo || 'PDF, JPG, PNG (máx. 5MB)'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Fotografía del Vehículo -->
                    <div class="mt-8 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                            <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Fotografía del Vehículo
                        </label>
                        <div class="flex items-center justify-center">
                            <div class="w-full max-w-md">
                                <input type="file" 
                                       id="fotografia_file" 
                                       name="fotografia_file" 
                                       accept=".jpg,.jpeg,.png" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'fotografia')" />
                                <label for="fotografia_file" 
                                       class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-base">Subir Fotografía del Vehículo</span>
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.fotografia || 'JPG, PNG (máx. 5MB)'"></p>
                    </div>
                </div>

                <!-- Documentos Adicionales -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Documentos Adicionales
                    </h4>

                    <div class="space-y-4">
                        <!-- Selector de tipo de documento -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Documento
                                </label>
                                <select x-model="nuevoDocumento.tipo_documento_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow">
                                    <option value="">Seleccionar tipo de documento</option>
                                    @foreach($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre_tipo_documento }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción
                                </label>
                                <input type="text" 
                                       x-model="nuevoDocumento.descripcion"
                                       placeholder="Descripción del documento" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                            </div>
                        </div>

                        <!-- Archivo y fecha de vencimiento -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Archivo
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="file" 
                                           id="documento_adicional_file" 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                                           class="hidden" 
                                           @change="handleAdditionalDocumentFile($event)" />
                                    <label for="documento_adicional_file" 
                                           class="cursor-pointer flex-1 inline-flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span x-text="nuevoDocumento.archivo ? nuevoDocumento.archivo.name : 'Seleccionar archivo'"></span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG, DOC, DOCX (máx. 10MB)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de Vencimiento (Opcional)
                                </label>
                                <input type="date" 
                                       x-model="nuevoDocumento.fecha_vencimiento"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" />
                            </div>
                        </div>

                        <!-- Botón para agregar documento -->
                        <div class="flex justify-end">
                            <button type="button" 
                                    @click="agregarDocumentoAdicional()"
                                    :disabled="!nuevoDocumento.tipo_documento_id || !nuevoDocumento.archivo"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-petroyellow hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-petroyellow disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Agregar Documento
                            </button>
                        </div>

                        <!-- Lista de documentos adicionales agregados -->
                        <div x-show="documentosAdicionales.length > 0" class="mt-6">
                            <h5 class="text-sm font-medium text-gray-900 mb-3">Documentos Agregados:</h5>
                            <div class="space-y-3">
                                <template x-for="(doc, index) in documentosAdicionales" :key="index">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900" x-text="doc.tipo_nombre"></p>
                                                    <p class="text-xs text-gray-500" x-text="doc.descripcion"></p>
                                                    <p class="text-xs text-gray-400" x-text="doc.archivo.name"></p>
                                                    <p x-show="doc.fecha_vencimiento" class="text-xs text-gray-400">
                                                        Vence: <span x-text="doc.fecha_vencimiento"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                @click="eliminarDocumentoAdicional(index)"
                                                class="ml-3 inline-flex items-center p-1 border border-transparent rounded-full text-red-400 hover:text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Campos ocultos para enviar los documentos adicionales -->
                        <template x-for="(doc, index) in documentosAdicionales" :key="index">
                            <div>
                                <input type="hidden" :name="'documentos_adicionales_tipos[' + index + ']'" :value="doc.tipo_documento_id" />
                                <input type="hidden" :name="'documentos_adicionales_descripciones[' + index + ']'" :value="doc.descripcion" />
                                <input type="hidden" :name="'documentos_adicionales_fechas_vencimiento[' + index + ']'" :value="doc.fecha_vencimiento" />
                            </div>
                        </template>
                        
                        <!-- Input oculto para archivos de documentos adicionales -->
                        <input type="file" 
                               name="documentos_adicionales_archivos[]" 
                               multiple 
                               style="display: none;" 
                               id="documentos_adicionales_archivos_hidden">
                    </div>
                </div>

                <!-- Configuración de Mantenimiento -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Intervalos de Mantenimiento (Opcional)
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
                                   value="{{ old('intervalo_km_motor') }}"
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
                                   value="{{ old('intervalo_km_transmision') }}"
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
                                   value="{{ old('intervalo_km_hidraulico') }}"
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                        @error('observaciones') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('vehiculos.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Guardar Vehículo
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('vehiculoFormController', () => ({
            fileStatus: {
                tarjeta_circulacion: '',
                tenencia_vehicular: '',
                verificacion_vehicular: '',
                poliza_seguro: '',
                factura_compra: '',
                manual_vehiculo: '',
                fotografia: '',
                documentos_adicionales: ''
            },
            documentosAdicionales: [],
            nuevoDocumento: {
                tipo_documento_id: '',
                descripcion: '',
                archivo: null,
                fecha_vencimiento: ''
            },
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (5MB para fotos, 10MB para documentos)
                const maxSize = type === 'fotografia' ? 5 * 1024 * 1024 : 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    const maxSizeMB = type === 'fotografia' ? '5MB' : '10MB';
                    alert(`El archivo es demasiado grande. Máximo ${maxSizeMB}`);
                    event.target.value = '';
                    this.fileStatus[type] = '';
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
                    this.fileStatus[type] = '';
                    return;
                }

                this.fileStatus[type] = `✅ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            },

            handleAdditionalDocumentFile(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.nuevoDocumento.archivo = null;
                    return;
                }

                // Validar tamaño del archivo (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('El archivo es muy grande. El tamaño máximo es 10MB.');
                    event.target.value = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no válido. Solo se permiten: PDF, JPG, PNG, DOC, DOCX');
                    event.target.value = '';
                    return;
                }

                this.nuevoDocumento.archivo = file;
            },

            agregarDocumentoAdicional() {
                if (!this.nuevoDocumento.tipo_documento_id || !this.nuevoDocumento.archivo) {
                    alert('Por favor seleccione un tipo de documento y un archivo.');
                    return;
                }

                // Obtener el nombre del tipo de documento
                const selectElement = document.querySelector('select[x-model="nuevoDocumento.tipo_documento_id"]');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const tipoNombre = selectedOption.text;

                // Agregar el documento a la lista
                this.documentosAdicionales.push({
                    tipo_documento_id: this.nuevoDocumento.tipo_documento_id,
                    tipo_nombre: tipoNombre,
                    descripcion: this.nuevoDocumento.descripcion || '',
                    archivo: this.nuevoDocumento.archivo,
                    fecha_vencimiento: this.nuevoDocumento.fecha_vencimiento || null
                });

                // Actualizar el input oculto de archivos
                this.actualizarArchivosOcultos();

                // Limpiar el formulario
                this.nuevoDocumento = {
                    tipo_documento_id: '',
                    descripcion: '',
                    archivo: null,
                    fecha_vencimiento: ''
                };

                // Limpiar el input de archivo
                document.getElementById('documento_adicional_file').value = '';
            },

            eliminarDocumentoAdicional(index) {
                this.documentosAdicionales.splice(index, 1);
                this.actualizarArchivosOcultos();
            },

            actualizarArchivosOcultos() {
                const hiddenInput = document.getElementById('documentos_adicionales_archivos_hidden');
                const dataTransfer = new DataTransfer();
                
                // Agregar todos los archivos al DataTransfer
                this.documentosAdicionales.forEach(doc => {
                    if (doc.archivo) {
                        dataTransfer.items.add(doc.archivo);
                    }
                });
                
                // Asignar los archivos al input oculto
                hiddenInput.files = dataTransfer.files;
            },

            handleMultipleFileInput(event, type) {
                const files = event.target.files;
                if (!files || files.length === 0) {
                    this.fileStatus[type] = '';
                    return;
                }

                const maxSize = 10 * 1024 * 1024; // 10MB
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
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
                    this.fileStatus[type] = `⚠️ ${validFiles} archivos válidos, ${invalidFiles} archivos inválidos (tamaño o tipo)`;
                } else {
                    this.fileStatus[type] = `✅ ${validFiles} archivos seleccionados`;
                }
            }
        }));
    });
</script>
@endpush
