@extends('layouts.app')

@section('title', 'Agregar Obra')

@section('header', 'Agregar Obra')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Obras', 'url' => route('obras.index')],
        ['label' => 'Agregar Obra']
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
        <h2 class="text-2xl font-bold text-gray-800">Agregar Nueva Obra</h2>
        <a href="{{ route('obras.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow p-6" x-data="obraFormController()">
        <form action="{{ route('obras.store') }}" method="POST" enctype="multipart/form-data" id="createObraForm">
            @csrf
            
            <div class="space-y-8">
                <!-- Información Básica de la Obra -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.414 0L8.586 11l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0l9-9A1 1 0 0018 3z" clip-rule="evenodd" />
                        </svg>
                        Información Básica de la Obra
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-input name="nombre_obra" label="Nombre de la Obra" required placeholder="Ej: Construcción de puente vehicular" />
                        
                        <div class="form-group">
                            <label for="avance" class="block text-sm font-medium text-gray-700 mb-2">
                                Avance (%)
                            </label>
                            <input type="number" 
                                   id="avance" 
                                   name="avance" 
                                   min="0" 
                                   max="100"
                                   placeholder="0"
                                   value="{{ old('avance', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('avance') border-red-500 @enderror">
                            @error('avance') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <x-form-input name="fecha_inicio" label="Fecha de Inicio" type="date" required />
                        <x-form-input name="fecha_fin" label="Fecha de Finalización (Estimada)" type="date" />
                    </div>
                    
                    <div class="mt-6">
                        <div class="form-group">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones
                            </label>
                            <textarea name="observaciones" 
                                    id="observaciones"
                                    rows="4" 
                                    maxlength="1000"
                                    placeholder="Agregue cualquier información adicional sobre la obra (características especiales, ubicación, etc.)" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                            @error('observaciones') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección de Asignaciones -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        Asignaciones de Personal y Vehículos
                    </h3>
                    
                    <!-- Encargado de la obra -->
                    <div class="mb-6">
                        <div class="form-group">
                            <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Encargado de la Obra <span class="text-red-500">*</span>
                            </label>
                            <select name="encargado_id" 
                                    id="encargado_id" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('encargado_id') border-red-500 @enderror">
                                <option value="">Seleccionar encargado...</option>
                                @foreach($encargados as $encargado)
                                    <option value="{{ $encargado['id'] }}" {{ old('encargado_id') == $encargado['id'] ? 'selected' : '' }}>
                                        {{ $encargado['nombre_usuario'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('encargado_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Vehículos asignados -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-medium text-gray-700 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1 1 0 11-3 0 1.5 1.5 0 013 0z"/>
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                                </svg>
                                Vehículos Asignados
                            </label>
                            <button type="button" 
                                    @click="addVehicle()"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Agregar Vehículo
                            </button>
                        </div>

                        <div id="vehiculosContainer" class="space-y-4">
                            <!-- Los vehículos se agregarán dinámicamente aquí -->
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                        </svg>
                        Documentos de la Obra
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Contrato -->
                        <div class="space-y-3">
                            <label class="flex text-sm font-medium text-gray-700 mb-3 items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Contrato
                            </label>
                            <div class="flex items-center justify-center">
                                <div class="w-full">
                                    <input type="file" 
                                           id="archivo_contrato" 
                                           name="archivo_contrato" 
                                           accept=".pdf,.doc,.docx" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'contrato')" />
                                    <label for="archivo_contrato" 
                                           class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                        <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="text-base">Subir Contrato</span>
                                    </label>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 text-center" x-text="fileStatus.contrato || 'PDF, DOC, DOCX (máx. 10MB)'"></p>
                        </div>

                        <!-- Fianza -->
                        <div class="space-y-3">
                            <label class="flex text-sm font-medium text-gray-700 mb-3 items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Fianza
                            </label>
                            <div class="flex items-center justify-center">
                                <div class="w-full">
                                    <input type="file" 
                                           id="archivo_fianza" 
                                           name="archivo_fianza" 
                                           accept=".pdf,.doc,.docx" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'fianza')" />
                                    <label for="archivo_fianza" 
                                           class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                        <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="text-base">Subir Fianza</span>
                                    </label>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 text-center" x-text="fileStatus.fianza || 'PDF, DOC, DOCX (máx. 10MB)'"></p>
                        </div>

                        <!-- Acta de Entrega-Recepción -->
                        <div class="space-y-3">
                            <label class="flex text-sm font-medium text-gray-700 mb-3 items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Acta de Entrega-Recepción
                            </label>
                            <div class="flex items-center justify-center">
                                <div class="w-full">
                                    <input type="file" 
                                           id="archivo_acta_entrega_recepcion" 
                                           name="archivo_acta_entrega_recepcion" 
                                           accept=".pdf,.doc,.docx" 
                                           class="hidden" 
                                           @change="handleFileInput($event, 'acta')" />
                                    <label for="archivo_acta_entrega_recepcion" 
                                           class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                                        <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="text-base">Subir Acta</span>
                                    </label>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 text-center" x-text="fileStatus.acta || 'PDF, DOC, DOCX (máx. 10MB)'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('obras.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-petrodark bg-petroyellow hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-petroyellow">
                    Crear Obra
                </button>
            </div>
        </form>
    </div>

    <!-- Template para vehículos -->
    <template id="vehicleTemplate">
        <div class="bg-white border border-gray-200 rounded-lg p-4 vehicle-card">
            <div class="flex justify-between items-start mb-4">
                <h4 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                    </svg>
                    Vehículo <span class="vehicle-number text-blue-500"></span>
                </h4>
                <button type="button" 
                        class="text-red-500 hover:text-red-700 remove-vehicle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Vehículo <span class="text-red-500">*</span>
                    </label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow vehicle-select" 
                            name="vehiculos[INDEX][vehiculo_id]" required onchange="updateKilometrajeInicial(this)">
                        <option value="">Seleccionar vehículo...</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" data-kilometraje="{{ $vehiculo->kilometraje_actual ?? 0 }}">
                                {{ $vehiculo->placas }} - {{ $vehiculo->modelo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kilometraje Inicial
                    </label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" 
                           name="vehiculos[INDEX][kilometraje_inicial]" 
                           min="0" 
                           placeholder="0">
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Combustible Inicial (L)
                    </label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" 
                           name="vehiculos[INDEX][combustible_inicial]" 
                           min="0" 
                           step="0.1" 
                           placeholder="0.0">
                </div>

                <div class="form-group lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow" 
                              name="vehiculos[INDEX][observaciones]" 
                              rows="2" 
                              maxlength="500" 
                              placeholder="Observaciones adicionales..."></textarea>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('obraFormController', () => ({
            fileStatus: {
                contrato: '',
                fianza: '',
                acta: ''
            },
            vehicleIndex: 0,
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (10MB para documentos)
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. Máximo 10MB');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no válido. Solo se permiten PDF, DOC y DOCX');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                this.fileStatus[type] = `✓ ${file.name}`;
            },

            addVehicle() {
                const template = document.getElementById('vehicleTemplate');
                const container = document.getElementById('vehiculosContainer');
                
                if (!template || !container) {
                    console.error('Template o container no encontrado');
                    return;
                }

                // Clonar el template
                const clone = template.content.cloneNode(true);
                
                // Actualizar los índices
                const currentIndex = this.vehicleIndex++;
                
                // Actualizar el número del vehículo
                const vehicleNumber = clone.querySelector('.vehicle-number');
                if (vehicleNumber) {
                    vehicleNumber.textContent = `#${currentIndex + 1}`;
                }
                
                // Actualizar los nombres de los inputs
                const inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace('INDEX', currentIndex);
                    }
                });
                
                // Agregar event listener para el botón de eliminar
                const removeButton = clone.querySelector('.remove-vehicle');
                if (removeButton) {
                    removeButton.addEventListener('click', (e) => {
                        const vehicleCard = e.target.closest('.vehicle-card');
                        if (vehicleCard) {
                            vehicleCard.remove();
                            this.updateVehicleNumbers();
                        }
                    });
                }
                
                // Agregar al container
                container.appendChild(clone);
            },

            updateVehicleNumbers() {
                const vehicleCards = document.querySelectorAll('.vehicle-card');
                vehicleCards.forEach((card, index) => {
                    const numberSpan = card.querySelector('.vehicle-number');
                    if (numberSpan) {
                        numberSpan.textContent = `#${index + 1}`;
                    }
                });
            }
        }));
    });

    // Agregar un vehículo por defecto al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar un poco para que Alpine.js se inicialice
        setTimeout(() => {
            const alpineComponent = document.querySelector('[x-data="obraFormController()"]');
            if (alpineComponent && alpineComponent._x_dataStack) {
                const data = alpineComponent._x_dataStack[0];
                if (data && typeof data.addVehicle === 'function') {
                    data.addVehicle();
                }
            }
        }, 100);
    });

    function updateKilometrajeInicial(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const kilometrajeInput = selectElement.closest('.vehicle-card').querySelector('input[name*="kilometraje_inicial"]');
        
        if (kilometrajeInput) {
            kilometrajeInput.value = selectedOption.getAttribute('data-kilometraje') || 0;
        }
    }
</script>
@endpush
