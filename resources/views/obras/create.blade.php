@extends('layouts.app')

@section('title', 'Crear Nueva Obra')

@section('header', 'Gestión de Obras')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Obras', 'url' => route('obras.index')],
        ['label' => 'Crear Nueva']
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

    {{-- Header principal --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Crear Nueva Obra</h2>
        <a href="{{ route('obras.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al Listado
        </a>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('obras.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Información Básica --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                </svg>
                Información Básica
            </h3>
            <p class="text-sm text-gray-500 mb-6">Datos generales de la obra</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nombre de la Obra --}}
                <div class="md:col-span-2">
                    <label for="nombre_obra" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre de la Obra <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nombre_obra" 
                           name="nombre_obra" 
                           value="{{ old('nombre_obra') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_obra') border-red-500 @enderror"
                           placeholder="Ingrese el nombre de la obra"
                           required>
                    @error('nombre_obra')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estatus --}}
                <div>
                    <label for="estatus" class="block text-sm font-medium text-gray-700 mb-1">
                        Estatus <span class="text-red-500">*</span>
                    </label>
                    <select id="estatus" 
                            name="estatus" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('estatus') border-red-500 @enderror"
                            required>
                        <option value="">Seleccione un estatus</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado }}" {{ old('estatus') == $estado ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $estado)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Avance --}}
                <div>
                    <label for="avance" class="block text-sm font-medium text-gray-700 mb-1">
                        Avance (%)
                    </label>
                    <input type="number" 
                           id="avance" 
                           name="avance" 
                           value="{{ old('avance', 0) }}"
                           min="0" 
                           max="100"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('avance') border-red-500 @enderror"
                           placeholder="0">
                    @error('avance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha de Inicio --}}
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="fecha_inicio" 
                           name="fecha_inicio" 
                           value="{{ old('fecha_inicio') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_inicio') border-red-500 @enderror"
                           required>
                    @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha de Fin --}}
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Fin
                    </label>
                    <input type="date" 
                           id="fecha_fin" 
                           name="fecha_fin" 
                           value="{{ old('fecha_fin') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_fin') border-red-500 @enderror">
                    @error('fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Encargado --}}
                <div class="md:col-span-2">
                    <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Encargado <span class="text-red-500">*</span>
                    </label>
                    <select id="encargado_id" 
                            name="encargado_id" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('encargado_id') border-red-500 @enderror"
                            required>
                        <option value="">Seleccione un encargado</option>
                        @foreach($encargados as $encargado)
                            <option value="{{ $encargado['id'] }}" {{ old('encargado_id') == $encargado['id'] ? 'selected' : '' }}>
                                {{ $encargado['nombre_completo'] }}
                                @if(isset($encargado['categoria']))
                                    - {{ $encargado['categoria'] }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('encargado_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Observaciones --}}
                <div class="md:col-span-2">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones
                    </label>
                    <textarea id="observaciones" 
                              name="observaciones" 
                              rows="3"
                              class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror"
                              placeholder="Observaciones adicionales sobre la obra">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Asignación de Vehículos --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                </svg>
                Asignación de Vehículos
            </h3>
            <p class="text-sm text-gray-500 mb-6">Seleccione un vehículo para asignar a esta obra</p>

            @if($vehiculos->isNotEmpty())
                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-600">Puede asignar un vehículo a esta obra para su seguimiento.</p>
                    <button type="button" 
                            onclick="openVehicleModal()" 
                            class="bg-petroyellow hover:bg-yellow-500 text-petrodark px-4 py-2 rounded-md transition-colors duration-200 flex items-center font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Asignar Vehículo
                    </button>
                </div>
                
                {{-- Lista de vehículos asignados --}}
                <div id="assigned-vehicles-list" class="space-y-3">
                    <div class="text-gray-600 text-sm" id="no-vehicles-message">
                        Ningún vehículo asignado. Use el botón "Asignar Vehículo" para agregar vehículos a esta obra.
                    </div>
                </div>
                
                {{-- Área de checkboxes para el envío del formulario (ocultos) --}}
                <div id="vehicle-checkboxes" class="hidden"></div>
                
                @error('vehiculos_seleccionados')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">No hay vehículos disponibles para asignar en este momento.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Documentos --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                </svg>
                Documentos de la Obra
            </h3>
            <p class="text-sm text-gray-500 mb-6">Archivos relacionados con la obra</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Contrato --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contrato</label>
                    <div class="relative">
                        <input type="file" name="archivo_contrato" accept=".pdf,.doc,.docx" 
                               class="hidden" id="archivo_contrato">
                        <label for="archivo_contrato" 
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base">Subir Contrato</span>
                        </label>
                        <p class="text-xs text-gray-500 text-center mt-2">PDF, DOC, DOCX (máx. 10MB)</p>
                    </div>
                    @error('archivo_contrato')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fianza --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fianza</label>
                    <div class="relative">
                        <input type="file" name="archivo_fianza" accept=".pdf,.doc,.docx" 
                               class="hidden" id="archivo_fianza">
                        <label for="archivo_fianza" 
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base">Subir Fianza</span>
                        </label>
                        <p class="text-xs text-gray-500 text-center mt-2">PDF, DOC, DOCX (máx. 10MB)</p>
                    </div>
                    @error('archivo_fianza')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Acta de Entrega-Recepción --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Acta de Entrega-Recepción</label>
                    <div class="relative">
                        <input type="file" name="archivo_acta_entrega_recepcion" accept=".pdf,.doc,.docx" 
                               class="hidden" id="archivo_acta_entrega_recepcion">
                        <label for="archivo_acta_entrega_recepcion" 
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base">Subir Acta</span>
                        </label>
                        <p class="text-xs text-gray-500 text-center mt-2">PDF, DOC, DOCX (máx. 10MB)</p>
                    </div>
                    @error('archivo_acta_entrega_recepcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('obras.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                Cancelar
            </a>
            <button type="submit" 
                    class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200">
                Crear Obra
            </button>
        </div>
    </form>

    <!-- Modal para asignar vehículos -->
    <div id="vehicle-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <!-- Overlay con efecto blur -->
        <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity duration-300"></div>
        
        <!-- Contenedor del modal -->
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col mx-4 border border-gray-200 overflow-hidden">
            <!-- Header del modal -->
            <div class="relative px-6 py-5 bg-petrodark text-white">
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-10 h-10 bg-petroyellow rounded-lg shadow-lg">
                            <svg class="w-5 h-5 text-petrodark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Asignación de Vehículo</h3>
                            <p class="text-gray-300 text-sm">Seleccione un vehículo para asignar a esta obra</p>
                        </div>
                    </div>
                    
                    <button type="button" onclick="closeVehicleModal()" 
                            class="flex items-center justify-center w-10 h-10 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenido del modal -->
            <div class="flex-1 overflow-y-auto p-6">
                <!-- Panel de instrucciones simplificado -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-gray-700 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Busque y seleccione un vehículo por sus características (marca, modelo, placas). 
                                Solo los vehículos disponibles pueden ser seleccionados.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Selector de vehículo con búsqueda -->
                <div class="mb-6">
                    <label for="vehiculo-search" class="block text-sm font-medium text-gray-700 mb-2">
                        Buscar vehículo
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="vehiculo-search" 
                               class="w-full p-2 pl-10 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow"
                               placeholder="Buscar por marca, modelo, placas..."
                               oninput="filterVehicles(this.value)">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de vehículos disponibles -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccione un vehículo
                    </label>
                    <div id="vehicle-options" class="max-h-60 overflow-y-auto border border-gray-300 rounded-md divide-y divide-gray-200">
                        @foreach($vehiculos->where('esta_asignado', false) as $vehiculo)
                            <div class="vehicle-option p-3 hover:bg-gray-50 cursor-pointer" 
                                 data-id="{{ $vehiculo->id }}"
                                 data-marca="{{ $vehiculo->marca }}"
                                 data-modelo="{{ $vehiculo->modelo }}"
                                 data-anio="{{ $vehiculo->anio }}"
                                 data-placas="{{ $vehiculo->placas }}"
                                 data-km="{{ $vehiculo->kilometraje_actual }}"
                                 onclick="selectVehicle(this)">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h4>
                                        <p class="text-xs text-gray-500">
                                            Año: {{ $vehiculo->anio }} | Placas: <span class="font-mono">{{ $vehiculo->placas }}</span> | 
                                            {{ number_format($vehiculo->kilometraje_actual) }} km
                                        </p>
                                    </div>
                                    <div class="px-2 py-1 bg-gray-900 text-white rounded text-xs font-medium">
                                        Disponible
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Separador para vehículos no disponibles -->
                        @if($vehiculos->where('esta_asignado', true)->count() > 0)
                            <div class="p-3 bg-gray-100 border-b border-gray-300">
                                <p class="text-sm font-medium text-gray-500 text-center">Vehículos no disponibles (solo referencia)</p>
                            </div>
                            
                            @foreach($vehiculos->where('esta_asignado', true) as $vehiculo)
                                <div class="vehicle-option p-3 bg-gray-50 opacity-70 cursor-not-allowed" 
                                    data-id="{{ $vehiculo->id }}"
                                    data-marca="{{ $vehiculo->marca }}"
                                    data-modelo="{{ $vehiculo->modelo }}"
                                    data-anio="{{ $vehiculo->anio }}"
                                    data-placas="{{ $vehiculo->placas }}"
                                    data-km="{{ $vehiculo->kilometraje_actual }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-600">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h4>
                                            <p class="text-xs text-gray-500">
                                                Año: {{ $vehiculo->anio }} | Placas: <span class="font-mono">{{ $vehiculo->placas }}</span>
                                            </p>
                                            @if($vehiculo->obra_asignada)
                                                <p class="text-xs text-gray-500 mt-1 italic">
                                                    Asignado a: {{ $vehiculo->obra_asignada }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-medium border border-gray-300">
                                            En uso
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <!-- Mensaje de no resultados (oculto inicialmente) -->
                        <div id="no-vehicle-results" class="p-4 text-center hidden">
                            <p class="text-gray-500 text-sm">No se encontraron vehículos que coincidan con su búsqueda</p>
                        </div>
                    </div>
                </div>
                
                <!-- Vehículo seleccionado -->
                <div id="selected-vehicle-container" class="hidden mb-6 bg-gray-50 rounded-lg border border-gray-300 p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-900">Vehículo seleccionado:</h4>
                            <p id="selected-vehicle-details" class="text-sm text-gray-700 mt-1"></p>
                        </div>
                        <button type="button" onclick="clearVehicleSelection()" 
                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-full p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Input oculto para el formulario -->
                <input type="hidden" id="selected-vehicle-id" name="vehiculo_id" value="">
            </div>

            <!-- Footer del modal -->
            <div class="px-6 py-4 bg-gray-100 border-t border-gray-200">
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="closeVehicleModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 text-sm font-medium">
                        Cancelar
                    </button>
                    <button type="button" 
                            id="confirm-vehicle-btn"
                            onclick="assignSelectedVehicle()" 
                            class="px-4 py-2 bg-petroyellow text-petrodark rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow focus:ring-offset-2 transition-all duration-200 text-sm font-medium disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
                            disabled>
                        Asignar Vehículo
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
    // Variable para almacenar el vehículo seleccionado
    let selectedVehicleId = null;
    
    function openVehicleModal() {
        document.getElementById('vehicle-modal').classList.remove('hidden');
        // Resetear selección previa al abrir el modal
        clearVehicleSelection();
    }

    function closeVehicleModal() {
        document.getElementById('vehicle-modal').classList.add('hidden');
    }

    function filterVehicles(searchText) {
        searchText = searchText.toLowerCase().trim();
        const vehicleOptions = document.querySelectorAll('.vehicle-option');
        let hasResults = false;
        let availableResults = false;
        
        vehicleOptions.forEach(option => {
            const marca = option.dataset.marca?.toLowerCase() || '';
            const modelo = option.dataset.modelo?.toLowerCase() || '';
            const placas = option.dataset.placas?.toLowerCase() || '';
            const anio = option.dataset.anio?.toLowerCase() || '';
            
            // Buscar en múltiples campos
            const matchesSearch = marca.includes(searchText) || 
                                  modelo.includes(searchText) || 
                                  placas.includes(searchText) ||
                                  anio.includes(searchText);
            
            // No mostrar vehículos no disponibles en los resultados de búsqueda a menos que la búsqueda sea específica
            const isDisabled = option.classList.contains('cursor-not-allowed');
            
            if (matchesSearch) {
                option.style.display = 'block';
                hasResults = true;
                if (!isDisabled) {
                    availableResults = true;
                }
            } else {
                option.style.display = 'none';
            }
        });
        
        // Mostrar/ocultar mensaje de "no hay resultados"
        document.getElementById('no-vehicle-results').style.display = hasResults ? 'none' : 'block';
    }
    
    function selectVehicle(element) {
        // No permitir seleccionar vehículos no disponibles
        if (element.classList.contains('cursor-not-allowed')) {
            return;
        }
        
        // Desmarcar selección anterior
        const previousSelected = document.querySelector('.vehicle-option.bg-gray-100');
        if (previousSelected) {
            previousSelected.classList.remove('bg-gray-100', 'border-l-4', 'border-petroyellow');
        }
        
        // Marcar nueva selección
        element.classList.add('bg-gray-100', 'border-l-4', 'border-petroyellow');
        
        // Guardar ID del vehículo seleccionado
        selectedVehicleId = element.dataset.id;
        document.getElementById('selected-vehicle-id').value = selectedVehicleId;
        
        // Mostrar detalles del vehículo seleccionado
        const marca = element.dataset.marca;
        const modelo = element.dataset.modelo;
        const anio = element.dataset.anio;
        const placas = element.dataset.placas;
        const km = element.dataset.km;
        
        const detailsText = `${marca} ${modelo} (${anio}) - Placas: ${placas} - Kilometraje: ${numberWithCommas(km)} km`;
        document.getElementById('selected-vehicle-details').textContent = detailsText;
        document.getElementById('selected-vehicle-container').classList.remove('hidden');
        
        // Habilitar botón de confirmar
        document.getElementById('confirm-vehicle-btn').disabled = false;
    }
    
    function clearVehicleSelection() {
        // Desmarcar selección visual
        const selected = document.querySelector('.vehicle-option.bg-gray-100');
        if (selected) {
            selected.classList.remove('bg-gray-100', 'border-l-4', 'border-petroyellow');
        }
        
        // Limpiar variable e input
        selectedVehicleId = null;
        document.getElementById('selected-vehicle-id').value = '';
        
        // Ocultar detalles
        document.getElementById('selected-vehicle-container').classList.add('hidden');
        
        // Deshabilitar botón de confirmar
        document.getElementById('confirm-vehicle-btn').disabled = true;
        
        // Restablecer búsqueda
        document.getElementById('vehiculo-search').value = '';
        filterVehicles('');
    }
    
    function assignSelectedVehicle() {
        if (!selectedVehicleId) {
            return;
        }
        
        const vehicleList = document.getElementById('assigned-vehicles-list');
        const noVehiclesMessage = document.getElementById('no-vehicles-message');
        const vehicleCheckboxes = document.getElementById('vehicle-checkboxes');
        
        // Limpiar lista previa
        vehicleList.innerHTML = '';
        vehicleCheckboxes.innerHTML = '';
        
        // Buscar datos del vehículo seleccionado
        const selectedElement = document.querySelector(`.vehicle-option[data-id="${selectedVehicleId}"]`);
        const vehicleMarca = selectedElement.dataset.marca;
        const vehicleModelo = selectedElement.dataset.modelo;
        const vehiclePlacas = selectedElement.dataset.placas;
        
        // Agregar vehículo a la lista
        const vehicleItem = document.createElement('div');
        vehicleItem.className = 'flex items-center justify-between p-4 bg-gray-50 border border-gray-300 rounded-lg shadow-sm';
        vehicleItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-8 h-8 bg-petroyellow rounded-lg">
                    <svg class="w-4 h-4 text-petrodark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-gray-900">${vehicleMarca} ${vehicleModelo}</span>
                    <p class="text-xs text-gray-700">Placas: ${vehiclePlacas}</p>
                </div>
            </div>
            <button type="button" onclick="removeVehicle('${selectedVehicleId}')" 
                    class="flex items-center justify-center w-8 h-8 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        vehicleList.appendChild(vehicleItem);
        noVehiclesMessage.classList.add('hidden');
        vehicleCheckboxes.classList.remove('hidden');
        
        // Agregar checkbox oculto para el formulario
        const hiddenCheckbox = document.createElement('input');
        hiddenCheckbox.type = 'checkbox';
        hiddenCheckbox.name = 'vehiculos_seleccionados[]';
        hiddenCheckbox.value = selectedVehicleId;
        hiddenCheckbox.checked = true;
        hiddenCheckbox.classList.add('hidden');
        vehicleCheckboxes.appendChild(hiddenCheckbox);
        
        // Cerrar modal
        closeVehicleModal();
    }
    
    function removeVehicle(vehicleId) {
        const vehicleList = document.getElementById('assigned-vehicles-list');
        const noVehiclesMessage = document.getElementById('no-vehicles-message');
        const vehicleCheckboxes = document.getElementById('vehicle-checkboxes');
        
        // Eliminar todos los elementos de la lista y los checkboxes
        vehicleList.innerHTML = '';
        vehicleCheckboxes.innerHTML = '';
        
        // Mostrar mensaje de ningún vehículo asignado
        noVehiclesMessage.classList.remove('hidden');
        vehicleCheckboxes.classList.add('hidden');
    }
    
    function numberWithCommas(x) {
        return new Intl.NumberFormat().format(x);
    }
</script>
@endsection
