@extends('layouts.app')

@section('title', 'Agregar Obra')

@section('header', 'Gestión de Obras')

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



    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <strong class="font-bold">Errores de validación</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Header principal --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Agregar Nueva Obra</h2>
        <a href="{{ route('obras.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al Listado
        </a>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('obras.store') }}" method="POST" enctype="multipart/form-data" x-data="obraFormController()" class="space-y-6">
        @csrf

        {{-- Información Básica de la Obra --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                Información Básica de la Obra
            </h3>
            <p class="text-sm text-gray-500 mb-6">Datos principales y estado del proyecto</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="nombre_obra" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Obra *</label>
                    <input type="text" id="nombre_obra" name="nombre_obra" required 
                           value="{{ old('nombre_obra') }}"
                           placeholder="Ej: Construcción de Puente Vehicular Principal"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('nombre_obra') border-red-500 @enderror">
                    @error('nombre_obra')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="avance" class="block text-sm font-medium text-gray-700 mb-1">Avance del Proyecto (%)</label>
                    <input type="number" id="avance" name="avance" min="0" max="100"
                           value="{{ old('avance', 0) }}"
                           placeholder="0"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('avance') border-red-500 @enderror">
                    @error('avance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones Generales</label>
                    <textarea id="observaciones" name="observaciones" rows="3"
                              placeholder="Observaciones adicionales sobre el proyecto..."
                              class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Cronograma del Proyecto --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                Cronograma del Proyecto
            </h3>
            <p class="text-sm text-gray-500 mb-6">Fechas de planificación y ejecución de la obra</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio *</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required 
                           value="{{ old('fecha_inicio') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_inicio') border-red-500 @enderror">
                    @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Finalización Prevista</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           value="{{ old('fecha_fin') }}"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('fecha_fin') border-red-500 @enderror">
                    @error('fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Asignación de Recursos --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Asignación de Recursos
            </h3>
            <p class="text-sm text-gray-500 mb-6">Personal y equipo asignado para la ejecución del proyecto</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="vehiculo_search" class="block text-sm font-medium text-gray-700 mb-1">Vehículo Asignado</label>
                    <div class="relative" x-data="vehiculoSearch()">
                        <input type="text" 
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false"
                               placeholder="Buscar por placas, marca o modelo..."
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('vehiculo_id') border-red-500 @enderror">
                        <input type="hidden" id="vehiculo_id" name="vehiculo_id" value="{{ old('vehiculo_id') }}">
                        
                        <div x-show="open && filteredVehiculos.length > 0" 
                             x-transition
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                            <template x-for="vehiculo in filteredVehiculos" :key="vehiculo.id">
                                <div @click="selectVehiculo(vehiculo)"
                                     class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100">
                                    <div class="font-medium text-gray-900" x-text="vehiculo.placas"></div>
                                    <div class="text-sm text-gray-500" x-text="vehiculo.marca + ' ' + vehiculo.modelo + ' (' + vehiculo.anio + ')'"></div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="open && search && filteredVehiculos.length === 0"
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-3">
                            <div class="text-gray-500 text-sm">No se encontraron vehículos</div>
                        </div>
                    </div>
                    @error('vehiculo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="operador_search" class="block text-sm font-medium text-gray-700 mb-1">Operador Asignado</label>
                    <div class="relative" x-data="operadorSearch()">
                        <input type="text" 
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false"
                               placeholder="Buscar operador por nombre..."
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('operador_id') border-red-500 @enderror">
                        <input type="hidden" id="operador_id" name="operador_id" value="{{ old('operador_id') }}">
                        
                        <div x-show="open && filteredOperadores.length > 0" 
                             x-transition
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                            <template x-for="operador in filteredOperadores" :key="operador.id">
                                <div @click="selectOperador(operador)"
                                     class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100">
                                    <div class="font-medium text-gray-900" x-text="operador.nombre_completo"></div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="open && search && filteredOperadores.length === 0"
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-3">
                            <div class="text-gray-500 text-sm">No se encontraron operadores</div>
                        </div>
                    </div>
                    @error('operador_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="encargado_search" class="block text-sm font-medium text-gray-700 mb-1">Encargado de Obra</label>
                    <div class="relative" x-data="encargadoSearch()">
                        <input type="text" 
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false"
                               placeholder="Buscar encargado por nombre..."
                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('encargado_id') border-red-500 @enderror">
                        <input type="hidden" id="encargado_id" name="encargado_id" value="{{ old('encargado_id') }}">
                        
                        <div x-show="open && filteredEncargados.length > 0" 
                             x-transition
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                            <template x-for="encargado in filteredEncargados" :key="encargado.id">
                                <div @click="selectEncargado(encargado)"
                                     class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100">
                                    <div class="font-medium text-gray-900" x-text="encargado.nombre_completo"></div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="open && search && filteredEncargados.length === 0"
                             class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-3">
                            <div class="text-gray-500 text-sm">No se encontraron encargados</div>
                        </div>
                    </div>
                    @error('encargado_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Control de Kilometraje --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
                Control de Kilometraje
            </h3>
            <p class="text-sm text-gray-500 mb-6">Registro de kilometraje inicial y final del vehículo asignado</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700 mb-1">Kilometraje Inicial *</label>
                    <input type="number" id="kilometraje_inicial" name="kilometraje_inicial" 
                           value="{{ old('kilometraje_inicial') }}"
                           min="0" step="1" required
                           placeholder="Ejemplo: 45000"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('kilometraje_inicial') border-red-500 @enderror">
                    @error('kilometraje_inicial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="kilometraje_final" class="block text-sm font-medium text-gray-700 mb-1">Kilometraje Final (Se registra al finalizar)</label>
                    <input type="number" id="kilometraje_final" name="kilometraje_final" 
                           value="{{ old('kilometraje_final') }}"
                           min="0" step="1"
                           placeholder="Se llenará al finalizar la obra"
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-petroyellow focus:border-petroyellow @error('kilometraje_final') border-red-500 @enderror">
                    @error('kilometraje_final')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- Documentos de la Obra --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
                Documentos de la Obra
            </h3>
            <p class="text-sm text-gray-500 mb-6">Gestión de archivos y documentación legal del proyecto</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contrato</label>
                    <div class="relative">
                        <input type="file" name="archivo_contrato" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                               class="hidden" id="archivo_contrato" x-on:change="handleFileInput($event, 'contrato')">
                        <label for="archivo_contrato" 
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base">Subir Contrato</span>
                        </label>
                        <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.contrato || 'PDF, JPG, PNG, DOC (máx. 10MB)'"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fianza</label>
                    <div class="relative">
                        <input type="file" name="archivo_fianza" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                               class="hidden" id="archivo_fianza" x-on:change="handleFileInput($event, 'fianza')">
                        <label for="archivo_fianza" 
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base">Subir Fianza</span>
                        </label>
                        <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.fianza || 'PDF, JPG, PNG, DOC (máx. 10MB)'"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Acta Entrega-Recepción</label>
                    <div class="relative">
                        <input type="file" name="archivo_acta_entrega_recepcion" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
                               class="hidden" id="archivo_acta_entrega_recepcion" x-on:change="handleFileInput($event, 'acta')">
                        <label for="archivo_acta_entrega_recepcion" 
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base">Subir Acta</span>
                        </label>
                        <p class="text-xs text-gray-500 text-center mt-2" x-text="fileStatus.acta || 'PDF, JPG, PNG, DOC (máx. 10MB)'"></p>
                    </div>
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
                Crear Obra Completa
            </button>
        </div>
    </form>
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
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (10MB)
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. Máximo 10MB');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato de archivo no permitido. Solo se permiten: PDF, JPG, PNG, DOC, DOCX');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                this.fileStatus[type] = `✅ ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            }
        }));

        // Función para búsqueda de vehículos
        Alpine.data('vehiculoSearch', () => ({
            open: false,
            search: '',
            selectedVehiculo: null,
            vehiculos: @json($vehiculosDisponibles ?? []),
            
            get filteredVehiculos() {
                if (!this.search) return this.vehiculos;
                return this.vehiculos.filter(vehiculo => 
                    vehiculo.placas.toLowerCase().includes(this.search.toLowerCase()) ||
                    vehiculo.marca.toLowerCase().includes(this.search.toLowerCase()) ||
                    vehiculo.modelo.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            
            selectVehiculo(vehiculo) {
                this.selectedVehiculo = vehiculo;
                this.search = vehiculo.placas + ' - ' + vehiculo.marca + ' ' + vehiculo.modelo;
                this.open = false;
                document.getElementById('vehiculo_id').value = vehiculo.id;
                
                // Llenar automáticamente el kilometraje inicial con el kilometraje actual del vehículo
                if (vehiculo.kilometraje_actual) {
                    document.getElementById('kilometraje_inicial').value = vehiculo.kilometraje_actual;
                }
            }
        }));

        // Función para búsqueda de operadores
        Alpine.data('operadorSearch', () => ({
            open: false,
            search: '',
            selectedOperador: null,
            operadores: @json($operadoresDisponibles ?? []),
            
            get filteredOperadores() {
                if (!this.search) return this.operadores;
                return this.operadores.filter(operador => 
                    operador.nombre_completo.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            
            selectOperador(operador) {
                this.selectedOperador = operador;
                this.search = operador.nombre_completo;
                this.open = false;
                document.getElementById('operador_id').value = operador.id;
            }
        }));

        // Función para búsqueda de encargados
        Alpine.data('encargadoSearch', () => ({
            open: false,
            search: '',
            selectedEncargado: null,
            encargados: @json($encargadosDisponibles ?? []),
            
            get filteredEncargados() {
                if (!this.search) return this.encargados;
                return this.encargados.filter(encargado => 
                    encargado.nombre_completo.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            
            selectEncargado(encargado) {
                this.selectedEncargado = encargado;
                this.search = encargado.nombre_completo;
                this.open = false;
                document.getElementById('encargado_id').value = encargado.id;
            }
        }));
    });

    // Validación del formulario antes del envío
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const vehiculoId = document.getElementById('vehiculo_id').value;
                const operadorId = document.getElementById('operador_id').value;
                const kilometrajeInicial = document.getElementById('kilometraje_inicial').value;

                // Validar que si hay vehículo, también hay operador
                if (vehiculoId && !operadorId) {
                    e.preventDefault();
                    alert('⚠️ Si asigna un vehículo, debe también asignar un operador.');
                    document.getElementById('operador_id').focus();
                    return false;
                }

                // Validar que si hay operador, también hay vehículo
                if (operadorId && !vehiculoId) {
                    e.preventDefault();
                    alert('⚠️ Si asigna un operador, debe también asignar un vehículo.');
                    document.getElementById('vehiculo_id').focus();
                    return false;
                }

                // Validar kilometraje inicial si hay vehículo
                if (vehiculoId && (!kilometrajeInicial || kilometrajeInicial < 0)) {
                    e.preventDefault();
                    alert('⚠️ Debe ingresar un kilometraje inicial válido para el vehículo asignado.');
                    document.getElementById('kilometraje_inicial').focus();
                    return false;
                }

                // Validar fechas
                const fechaInicio = document.getElementById('fecha_inicio').value;
                const fechaFin = document.getElementById('fecha_fin').value;

                if (fechaInicio && fechaFin) {
                    const inicio = new Date(fechaInicio);
                    const fin = new Date(fechaFin);

                    if (fin <= inicio) {
                        e.preventDefault();
                        alert('❌ La fecha de finalización debe ser posterior a la fecha de inicio.');
                        document.getElementById('fecha_fin').focus();
                        return false;
                    }
                }

                return true;
            });
        }
    });
</script>
@endpush
