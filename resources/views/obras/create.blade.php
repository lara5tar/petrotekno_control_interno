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

                {{-- Ubicación de la Obra --}}
                <div class="md:col-span-2">
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">
                        Ubicación de la Obra
                    </label>
                    <input type="text" 
                           id="ubicacion" 
                           name="ubicacion" 
                           value="{{ old('ubicacion') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('ubicacion') border-red-500 @enderror"
                           placeholder="Ingrese la ubicación donde se realizará la obra">
                    @error('ubicacion')
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

                {{-- Responsable de la obra --}}
                <div class="md:col-span-2">
                    <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Responsable de la obra <span class="text-red-500">*</span>
                    </label>
                    <select id="encargado_id" 
                            name="encargado_id" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('encargado_id') border-red-500 @enderror"
                            required
                            data-testid="encargado-selector">
                        <option value="">Seleccione un responsable</option>
                        @foreach($encargados as $encargado)
                            <option value="{{ $encargado['id'] }}" {{ old('encargado_id') == $encargado['id'] ? 'selected' : '' }}
                                    data-testid="encargado-option-{{ $encargado['id'] }}"
                                    data-categoria="{{ $encargado['categoria'] ?? 'Sin puesto' }}">
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
                    <p class="mt-1 text-xs text-gray-500">Personal activo disponible para supervisión del proyecto</p>
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
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1 1 0 11-3 0 1.5 1.5 0 013 0z" />
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                </svg>
                Asignación de Vehículos
            </h3>
            <p class="text-sm text-gray-500 mb-6">Seleccione los vehículos que desea asignar a esta obra</p>

            @if($vehiculos->isNotEmpty())
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-600">Use el modal para seleccionar vehículos disponibles</p>
                    </div>
                    <button type="button" 
                            onclick="openAsignarVehiculosModal()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Seleccionar Vehículos
                    </button>
                </div>
                
                {{-- Lista de vehículos seleccionados para mostrar al usuario --}}
                <div id="selected-vehicles-display" class="space-y-3">
                    <div class="text-gray-600 text-sm" id="no-vehicles-message">
                        Ningún vehículo seleccionado. Use el botón "Seleccionar Vehículos" para asignar vehículos a esta obra.
                    </div>
                </div>
                
                {{-- Inputs ocultos para enviar los vehículos seleccionados --}}
                <div id="vehicle-inputs" class="hidden"></div>
                
                @error('vehiculos')
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
                            <h3 class="text-sm font-medium text-yellow-800">No hay vehículos registrados</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Debe registrar vehículos antes de poder asignarlos a una obra.</p>
                                <p class="mt-1">
                                    <a href="{{ route('vehiculos.create') }}" class="font-medium underline hover:text-yellow-600">
                                        Registrar nuevo vehículo →
                                    </a>
                                </p>
                            </div>
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
                               class="hidden" id="archivo_contrato" onchange="updateFileLabel('archivo_contrato')">
                        <label for="archivo_contrato" id="label_archivo_contrato"
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon_archivo_contrato">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base" id="text_archivo_contrato">Subir Contrato</span>
                        </label>
                        <div id="file_info_archivo_contrato" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded-md">
                            <div class="flex items-center text-sm text-green-800">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="filename_archivo_contrato"></span>
                                <button type="button" onclick="clearFile('archivo_contrato')" class="ml-auto text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
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
                               class="hidden" id="archivo_fianza" onchange="updateFileLabel('archivo_fianza')">
                        <label for="archivo_fianza" id="label_archivo_fianza"
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon_archivo_fianza">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base" id="text_archivo_fianza">Subir Fianza</span>
                        </label>
                        <div id="file_info_archivo_fianza" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded-md">
                            <div class="flex items-center text-sm text-green-800">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="filename_archivo_fianza"></span>
                                <button type="button" onclick="clearFile('archivo_fianza')" class="ml-auto text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
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
                               class="hidden" id="archivo_acta_entrega_recepcion" onchange="updateFileLabel('archivo_acta_entrega_recepcion')">
                        <label for="archivo_acta_entrega_recepcion" id="label_archivo_acta_entrega_recepcion"
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon_archivo_acta_entrega_recepcion">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base" id="text_archivo_acta_entrega_recepcion">Subir Acta</span>
                        </label>
                        <div id="file_info_archivo_acta_entrega_recepcion" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded-md">
                            <div class="flex items-center text-sm text-green-800">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="filename_archivo_acta_entrega_recepcion"></span>
                                <button type="button" onclick="clearFile('archivo_acta_entrega_recepcion')" class="ml-auto text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
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
    </form>

<!-- Modal para Asignar Vehículos a la Obra -->
<div id="asignar-vehiculos-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-vehiculos-title" class="text-lg font-semibold text-gray-900">
                Asignar Vehículos a la Nueva Obra
            </h3>
            <button onclick="closeAsignarVehiculosModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            <p><strong>Crear nueva obra:</strong> Seleccione vehículos para asignar</p>
            <p><strong>Vehículos seleccionados:</strong> <span id="vehicles-selected-count">0</span></p>
        </div>

        <!-- Lista de Vehículos Disponibles -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Seleccionar Vehículos Disponibles
            </label>
            <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md p-3 bg-gray-50">
                <div class="space-y-2" id="vehiculos-disponibles-create">
                    @php
                        // Para create, organizamos los vehículos por disponibilidad
                        $vehiculosOrdenados = collect($vehiculos ?? [])->sortBy(function($vehiculo) {
                            // Los disponibles (false) van primero, los no disponibles (true) van al final
                            return $vehiculo->esta_asignado ? 1 : 0;
                        });
                        
                        $vehiculosDisponiblesReales = $vehiculosOrdenados->filter(function($vehiculo) {
                            return !$vehiculo->esta_asignado;
                        });
                        
                        $vehiculosNoDisponibles = $vehiculosOrdenados->filter(function($vehiculo) {
                            return $vehiculo->esta_asignado;
                        });
                    @endphp
                    
                    @if($vehiculosDisponiblesReales->count() > 0)
                        <!-- Encabezado para vehículos disponibles -->
                        <div class="bg-green-50 border border-green-200 rounded p-2 mb-2">
                            <h6 class="text-sm font-semibold text-green-800 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Vehículos Disponibles ({{ $vehiculosDisponiblesReales->count() }})
                            </h6>
                        </div>
                        
                        @foreach($vehiculosDisponiblesReales as $vehiculo)
                            <label class="flex items-center space-x-3 p-2 rounded hover:bg-white cursor-pointer">
                                <input type="checkbox" 
                                       name="vehiculos_create[]" 
                                       value="{{ $vehiculo->id }}"
                                       class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       onchange="updateVehicleCount()">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Placas: {{ $vehiculo->placas }} | 
                                        Año: {{ $vehiculo->anio }} | 
                                        Kilometraje: {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    @endif
                    
                    @if($vehiculosNoDisponibles->count() > 0)
                        <!-- Separador -->
                        @if($vehiculosDisponiblesReales->count() > 0)
                            <div class="border-t border-gray-300 my-3"></div>
                        @endif
                        
                        <!-- Encabezado para vehículos no disponibles -->
                        <div class="bg-red-50 border border-red-200 rounded p-2 mb-2">
                            <h6 class="text-sm font-semibold text-red-800 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Vehículos No Disponibles ({{ $vehiculosNoDisponibles->count() }})
                            </h6>
                            <p class="text-xs text-red-600 mt-1">Estos vehículos están asignados a otras obras</p>
                        </div>
                        
                        @foreach($vehiculosNoDisponibles as $vehiculo)
                            <label class="flex items-center space-x-3 p-2 rounded bg-gray-100 cursor-not-allowed opacity-60">
                                <input type="checkbox" 
                                       name="vehiculos_create[]" 
                                       value="{{ $vehiculo->id }}"
                                       class="form-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       disabled>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-500">
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                        <span class="text-xs text-red-600 ml-2">(No disponible)</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Placas: {{ $vehiculo->placas }} | 
                                        Año: {{ $vehiculo->anio }} | 
                                        Kilometraje: {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                                    </div>
                                    @if($vehiculo->obra_asignada)
                                        <div class="text-xs text-red-600 font-medium">
                                            Asignado a: {{ $vehiculo->obra_asignada }}
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    @endif
                    
                    @if($vehiculosDisponiblesReales->count() === 0 && $vehiculosNoDisponibles->count() === 0)
                        <p class="text-sm text-gray-500 text-center py-4">No hay vehículos disponibles</p>
                    @endif
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">Selecciona los vehículos que deseas asignar a esta nueva obra. Los vehículos seleccionados se asignarán al crear la obra.</p>
        </div>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-3">
            <button type="button" 
                    onclick="closeAsignarVehiculosModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancelar
            </button>
            <button type="button" 
                    onclick="aplicarSeleccionVehiculos()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Aplicar Selección
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Variables para el modal de vehículos
    let vehiculosSeleccionados = [];

    // Funciones para el modal de asignar vehículos
    function openAsignarVehiculosModal() {
        const modal = document.getElementById('asignar-vehiculos-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAsignarVehiculosModal() {
        const modal = document.getElementById('asignar-vehiculos-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function updateVehicleCount() {
        const checkboxes = document.querySelectorAll('input[name="vehiculos_create[]"]:checked');
        const count = checkboxes.length;
        document.getElementById('vehicles-selected-count').textContent = count;
    }

    function aplicarSeleccionVehiculos() {
        const checkboxes = document.querySelectorAll('input[name="vehiculos_create[]"]:checked');
        const vehicleInputs = document.getElementById('vehicle-inputs');
        const selectedDisplay = document.getElementById('selected-vehicles-display');
        const noVehiclesMessage = document.getElementById('no-vehicles-message');
        
        // Limpiar inputs anteriores
        vehicleInputs.innerHTML = '';
        selectedDisplay.innerHTML = '';
        vehiculosSeleccionados = [];
        
        if (checkboxes.length > 0) {
            noVehiclesMessage.style.display = 'none';
            
            checkboxes.forEach(checkbox => {
                const vehicleId = checkbox.value;
                vehiculosSeleccionados.push(vehicleId);
                
                // Crear input oculto para el formulario principal
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'vehiculos_seleccionados[]';
                hiddenInput.value = vehicleId;
                vehicleInputs.appendChild(hiddenInput);
                
                // Obtener información del vehículo para mostrar
                const vehicleLabel = checkbox.closest('label');
                const vehicleInfo = vehicleLabel.querySelector('.font-medium').textContent;
                const vehiclePlacas = vehicleLabel.querySelector('.text-sm').textContent;
                
                // Crear elemento visual
                const vehicleDisplay = document.createElement('div');
                vehicleDisplay.className = 'flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-md';
                vehicleDisplay.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">${vehicleInfo}</div>
                            <div class="text-sm text-gray-600">${vehiclePlacas}</div>
                        </div>
                    </div>
                    <button type="button" onclick="removerVehiculo('${vehicleId}')" class="text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                selectedDisplay.appendChild(vehicleDisplay);
            });
        } else {
            noVehiclesMessage.style.display = 'block';
        }
        
        // Cerrar modal
        closeAsignarVehiculosModal();
    }

    function removerVehiculo(vehicleId) {
        // Remover del array
        vehiculosSeleccionados = vehiculosSeleccionados.filter(id => id !== vehicleId);
        
        // Remover input oculto
        const hiddenInput = document.querySelector(`input[name="vehiculos_seleccionados[]"][value="${vehicleId}"]`);
        if (hiddenInput) {
            hiddenInput.remove();
        }
        
        // Remover elemento visual
        const vehicleDisplay = event.target.closest('.flex.items-center.justify-between');
        if (vehicleDisplay) {
            vehicleDisplay.remove();
        }
        
        // Mostrar mensaje si no hay vehículos
        const selectedDisplay = document.getElementById('selected-vehicles-display');
        const noVehiclesMessage = document.getElementById('no-vehicles-message');
        
        if (vehiculosSeleccionados.length === 0) {
            noVehiclesMessage.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Event listeners para modal de vehículos
        const modalVehiculos = document.getElementById('asignar-vehiculos-modal');
        if (modalVehiculos) {
            modalVehiculos.addEventListener('click', function(e) {
                if (e.target === modalVehiculos) {
                    closeAsignarVehiculosModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAsignarVehiculosModal();
            }
        });
    });

    // Funciones para manejar la subida de archivos con feedback visual
    function updateFileLabel(fieldName) {
        const fileInput = document.getElementById(fieldName);
        const label = document.getElementById('label_' + fieldName);
        const textSpan = document.getElementById('text_' + fieldName);
        const icon = document.getElementById('icon_' + fieldName);
        const fileInfo = document.getElementById('file_info_' + fieldName);
        const filenameSpan = document.getElementById('filename_' + fieldName);
        
        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Tamaño en MB
            
            // Actualizar el estilo del label para mostrar que hay un archivo
            label.classList.remove('border-gray-300', 'text-gray-700');
            label.classList.add('border-green-400', 'text-green-700', 'bg-green-50');
            
            // Cambiar el icono a uno de check
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-green-600');
            
            // Actualizar el texto
            textSpan.textContent = 'Archivo seleccionado';
            
            // Mostrar información del archivo
            filenameSpan.textContent = `${fileName} (${fileSize} MB)`;
            fileInfo.classList.remove('hidden');
            
        } else {
            // Resetear al estado original si no hay archivo
            clearFile(fieldName);
        }
    }
    
    function clearFile(fieldName) {
        const fileInput = document.getElementById(fieldName);
        const label = document.getElementById('label_' + fieldName);
        const textSpan = document.getElementById('text_' + fieldName);
        const icon = document.getElementById('icon_' + fieldName);
        const fileInfo = document.getElementById('file_info_' + fieldName);
        
        // Limpiar el input
        fileInput.value = '';
        
        // Restaurar estilo original
        label.classList.remove('border-green-400', 'text-green-700', 'bg-green-50');
        label.classList.add('border-gray-300', 'text-gray-700');
        
        // Restaurar icono original
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />';
        icon.classList.remove('text-green-600');
        icon.classList.add('text-gray-400');
        
        // Restaurar texto original
        const originalTexts = {
            'archivo_contrato': 'Subir Contrato',
            'archivo_fianza': 'Subir Fianza',
            'archivo_acta_entrega_recepcion': 'Subir Acta'
        };
        textSpan.textContent = originalTexts[fieldName] || 'Subir Archivo';
        
        // Ocultar información del archivo
        fileInfo.classList.add('hidden');
    }
    
    // Validación de archivos antes del envío
    function validateFiles() {
        const fileInputs = ['archivo_contrato', 'archivo_fianza', 'archivo_acta_entrega_recepcion'];
        let isValid = true;
        
        fileInputs.forEach(fieldName => {
            const fileInput = document.getElementById(fieldName);
            if (fileInput.files && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB en bytes
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
                // Validar tamaño
                if (file.size > maxSize) {
                    alert(`El archivo ${file.name} es demasiado grande. Máximo permitido: 10MB`);
                    isValid = false;
                    return;
                }
                
                // Validar tipo de archivo
                if (!allowedTypes.includes(file.type)) {
                    alert(`El archivo ${file.name} no es un tipo válido. Solo se permiten archivos PDF, DOC y DOCX`);
                    isValid = false;
                    return;
                }
            }
        });
        
        return isValid;
    }
    
    // Agregar validación al envío del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateFiles()) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endpush
@endsection
