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
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Agregar Nueva Obra</h2>
            <p class="text-sm text-gray-600 mt-1">Complete todos los campos requeridos para crear una nueva obra en el sistema</p>
        </div>
        <a href="{{ route('obras.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver al listado
        </a>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('obras.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- 1. INFORMACIÓN BÁSICA DE LA OBRA --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-petroyellow rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Información Básica de la Obra</h3>
                    <p class="text-sm text-gray-600">Datos principales y estado del proyecto</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Nombre de la Obra --}}
                <div class="md:col-span-2 lg:col-span-3">
                    <label for="nombre_obra" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de la Obra
                        <span class="text-gray-500">*</span>
                    </label>
                    <input type="text" 
                           id="nombre_obra" 
                           name="nombre_obra" 
                           value="{{ old('nombre_obra') }}" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('nombre_obra') border-gray-400 @enderror"
                           placeholder="Ejemplo: Construcción de Puente Vehicular Principal">
                    @error('nombre_obra')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estatus --}}
                <div>
                    <label for="estatus" class="block text-sm font-medium text-gray-700 mb-2">
                        Estatus del Proyecto
                        <span class="text-gray-500">*</span>
                    </label>
                    <select id="estatus" 
                            name="estatus" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('estatus') border-gray-400 @enderror">
                        <option value="">Seleccione el estatus</option>
                        <option value="planificada" {{ old('estatus') == 'planificada' ? 'selected' : '' }}>Planificada</option>
                        <option value="en_progreso" {{ old('estatus') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="pausada" {{ old('estatus') == 'pausada' ? 'selected' : '' }}>Pausada</option>
                        <option value="completada" {{ old('estatus') == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ old('estatus') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Avance --}}
                <div>
                    <label for="avance" class="block text-sm font-medium text-gray-700 mb-2">
                        Avance del Proyecto (%)
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="avance" 
                               name="avance" 
                               value="{{ old('avance', 0) }}" 
                               min="0" 
                               max="100"
                               class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('avance') border-gray-400 @enderror"
                               placeholder="0">
                        <span class="absolute right-3 top-2 text-gray-500">%</span>
                    </div>

                    @error('avance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Observaciones Generales --}}
                <div class="md:col-span-2 lg:col-span-1">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones Generales
                    </label>
                    <textarea id="observaciones" 
                              name="observaciones" 
                              rows="3"
                              placeholder="Observaciones adicionales sobre el proyecto..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('observaciones') border-gray-400 @enderror">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- 2. CRONOGRAMA DEL PROYECTO --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-petroyellow rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Cronograma del Proyecto</h3>
                    <p class="text-sm text-gray-600">Fechas de planificación y ejecución de la obra</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Fecha de Inicio --}}
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Inicio
                        <span class="text-gray-500">*</span>
                    </label>
                    <input type="date" 
                           id="fecha_inicio" 
                           name="fecha_inicio" 
                           value="{{ old('fecha_inicio') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('fecha_inicio') border-gray-400 @enderror">
                    @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha de Fin --}}
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Finalización Prevista
                    </label>
                    <input type="date" 
                           id="fecha_fin" 
                           name="fecha_fin" 
                           value="{{ old('fecha_fin') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('fecha_fin') border-gray-400 @enderror">
                    @error('fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    {{-- Error de fechas será mostrado aquí --}}
                    {{-- Duración del proyecto será mostrada aquí --}}
                </div>
            </div>
        </div>

        {{-- ASIGNACIÓN DE RECURSOS --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-petroyellow rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Asignación de Recursos</h3>
                    <p class="text-sm text-gray-600">Personal y equipo asignado para la ejecución del proyecto</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{-- Vehículo --}}
                <div>
                    <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vehículo Asignado
                    </label>
                    <select id="vehiculo_id" 
                            name="vehiculo_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('vehiculo_id') border-gray-400 @enderror">
                        <option value="">Sin vehículo asignado</option>
                        @if(isset($vehiculosDisponibles))
                            @foreach($vehiculosDisponibles as $vehiculo)
                                <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                    {{ $vehiculo->placas }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('vehiculo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Operador --}}
                <div>
                    <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Operador Asignado
                    </label>
                    <select id="operador_id" 
                            name="operador_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('operador_id') border-gray-400 @enderror">
                        <option value="">Sin operador asignado</option>
                        @if(isset($operadoresDisponibles))
                            @foreach($operadoresDisponibles as $operador)
                                <option value="{{ $operador->id }}" {{ old('operador_id') == $operador->id ? 'selected' : '' }}>
                                    {{ $operador->nombre_completo }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('operador_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Encargado --}}
                <div>
                    <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Encargado de Obra
                    </label>
                    <select id="encargado_id" 
                            name="encargado_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('encargado_id') border-gray-400 @enderror">
                        <option value="">Sin encargado asignado</option>
                        @if(isset($encargadosDisponibles))
                            @foreach($encargadosDisponibles as $encargado)
                                <option value="{{ $encargado->id }}" {{ old('encargado_id') == $encargado->id ? 'selected' : '' }}>
                                    {{ $encargado->nombre_completo }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('encargado_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Fechas de Asignación --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Fecha de Asignación --}}
                <div>
                    <label for="fecha_asignacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Asignación
                    </label>
                    <input type="datetime-local" 
                           id="fecha_asignacion" 
                           name="fecha_asignacion" 
                           value="{{ old('fecha_asignacion', date('Y-m-d\TH:i')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('fecha_asignacion') border-gray-400 @enderror">
                    @error('fecha_asignacion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha de Liberación --}}
                <div>
                    <label for="fecha_liberacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Liberación Prevista
                        <span class="text-gray-400">(Opcional)</span>
                    </label>
                    <input type="datetime-local" 
                           id="fecha_liberacion" 
                           name="fecha_liberacion" 
                           value="{{ old('fecha_liberacion') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('fecha_liberacion') border-gray-400 @enderror">
                    @error('fecha_liberacion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Alerta de validación de asignación --}}
            {{-- Las alertas de asignación se mostrarán aquí --}}
        </div>

        {{-- 4. CONTROL DE KILOMETRAJE --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-petroyellow rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Control de Kilometraje</h3>
                    <p class="text-sm text-gray-600">Registro de kilometraje inicial y final del vehículo asignado</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kilometraje Inicial --}}
                <div>
                    <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700 mb-2">
                        Kilometraje Inicial
                        <span class="text-gray-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="kilometraje_inicial" 
                               name="kilometraje_inicial" 
                               value="{{ old('kilometraje_inicial') }}"
                               min="0"
                               step="1"
                               required
                               placeholder="Ejemplo: 45000"
                               class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('kilometraje_inicial') border-gray-400 @enderror">
                        <span class="absolute right-3 top-2 text-gray-500 text-sm">km</span>
                    </div>
                    @error('kilometraje_inicial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kilometraje Final --}}
                <div>
                    <label for="kilometraje_final" class="block text-sm font-medium text-gray-700 mb-2">
                        Kilometraje Final
                        <span class="text-gray-400">(Se registra al finalizar)</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="kilometraje_final" 
                               name="kilometraje_final" 
                               value="{{ old('kilometraje_final') }}"
                               min="0"
                               step="1"
                               placeholder="Se llenará al finalizar la obra"
                               class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('kilometraje_final') border-gray-400 @enderror">
                        <span class="absolute right-3 top-2 text-gray-500 text-sm">km</span>
                    </div>
                    @error('kilometraje_final')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


        {{-- 5. GESTIÓN DE DOCUMENTOS --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-petroyellow rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Documentos de la Obra</h3>
                    <p class="text-gray-400 text-sm">Gestión de archivos y documentación legal del proyecto</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                        <!-- Contrato -->
                        <div>
                            <label for="archivo_contrato" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                Contrato
                            </label>
                            <input type="file" 
                                   class="w-full px-3 py-2 border-2 border-dashed border-petroyellow rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('archivo_contrato') border-red-400 @enderror" 
                                   id="archivo_contrato" 
                                   name="archivo_contrato" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <p class="mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Formato: PDF, JPG, PNG, DOC (máx. 10MB)
                            </p>
                            @error('archivo_contrato')
                                <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fianza -->
                        <div>
                            <label for="archivo_fianza" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                                </svg>
                                Fianza
                            </label>
                            <input type="file" 
                                   class="w-full px-3 py-2 border-2 border-dashed border-petroyellow rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('archivo_fianza') border-red-400 @enderror" 
                                   id="archivo_fianza" 
                                   name="archivo_fianza" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <p class="mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Formato: PDF, JPG, PNG, DOC (máx. 10MB)
                            </p>
                            @error('archivo_fianza')
                                <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                            @enderror
                        </div>
                </div>
                <div class="space-y-6">
                        <!-- Acta Entrega-Recepción -->
                        <div>
                            <label for="archivo_acta_entrega_recepcion" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                                </svg>
                                Acta Entrega-Recepción
                            </label>
                            <input type="file" 
                                   class="w-full px-3 py-2 border-2 border-dashed border-petroyellow rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-petroyellow focus:border-petroyellow @error('archivo_acta_entrega_recepcion') border-red-400 @enderror" 
                                   id="archivo_acta_entrega_recepcion" 
                                   name="archivo_acta_entrega_recepcion" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <p class="mt-1 text-xs text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Formato: PDF, JPG, PNG, DOC (máx. 10MB)
                            </p>
                            @error('archivo_acta_entrega_recepcion')
                                <p class="mt-1 text-sm text-gray-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alert informativo -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-3 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">Información:</p>
                                    <p class="text-sm text-gray-600">
                                        Los documentos son opcionales en la creación. Puedes subirlos ahora o editarlos posteriormente desde la vista de detalles de la obra.
                                    </p>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>


        {{-- Botones de acción --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('obras.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-petroyellow transition duration-200 text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 border border-transparent rounded-md shadow-sm bg-petroyellow text-white font-medium hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-petroyellow transition duration-200 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Crear Obra Completa
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
{{-- Alpine.js script comentado temporalmente para debug
<script>
    function obraController() {
        return {
            // Estados básicos
            estatus: '{{ old('estatus') }}',
            avance: {{ old('avance', 0) }},
            
            // Fechas
            fechaInicio: '{{ old('fecha_inicio') }}',
            fechaFin: '{{ old('fecha_fin') }}',
            
            // Asignación
            vehiculoId: '{{ old('vehiculo_id') }}',
            operadorId: '{{ old('operador_id') }}',
            showAssignmentFields: false,
            
            // Validaciones
            dateError: false,
            dateErrorMessage: '',
            assignmentError: false,
            assignmentErrorMessage: '',

            init() {
                // Inicializar campos de asignación si hay datos
                this.toggleAssignmentFields();
                this.checkAssignmentConsistency();
            },

            updateAvanceBasedOnStatus() {
                // Actualizar avance según el estatus seleccionado
                if (this.estatus === 'completada') {
                    this.avance = 100;
                } else if (this.estatus === 'planificada') {
                    this.avance = 0;
                } else if (this.estatus === 'en_progreso' && this.avance === 0) {
                    this.avance = 10;
                } else if (this.estatus === 'cancelada') {
                    // Mantener el avance actual para obras canceladas
                }
            },

            toggleAssignmentFields() {
                // Mostrar campos de asignación si hay vehículo u operador seleccionado
                this.showAssignmentFields = this.vehiculoId || this.operadorId;
            },

            checkAssignmentConsistency() {
                this.assignmentError = false;
                this.assignmentErrorMessage = '';

                // Validar consistencia en la asignación
                if (this.vehiculoId && !this.operadorId) {
                    this.assignmentError = true;
                    this.assignmentErrorMessage = '⚠️ Si asigna un vehículo, debe también asignar un operador para garantizar su uso adecuado.';
                } else if (this.operadorId && !this.vehiculoId) {
                    this.assignmentError = true;
                    this.assignmentErrorMessage = '⚠️ Si asigna un operador, debe también asignar un vehículo para que pueda trabajar.';
                }
            },

            validateDates() {
                this.dateError = false;
                this.dateErrorMessage = '';

                if (this.fechaInicio && this.fechaFin) {
                    const inicio = new Date(this.fechaInicio);
                    const fin = new Date(this.fechaFin);
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);

                    if (fin <= inicio) {
                        this.dateError = true;
                        this.dateErrorMessage = '❌ La fecha de finalización debe ser posterior a la fecha de inicio';
                    } else if (inicio < hoy) {
                        // Advertencia si la fecha de inicio es en el pasado
                        this.dateError = false;
                        this.dateErrorMessage = '';
                    }
                }
            },

            calculateDuration() {
                if (this.fechaInicio && this.fechaFin) {
                    const inicio = new Date(this.fechaInicio);
                    const fin = new Date(this.fechaFin);
                    const diferencia = fin - inicio;
                    const dias = Math.ceil(diferencia / (1000 * 60 * 60 * 24));
                    
                    if (dias === 1) {
                        return '1 día';
                    } else if (dias < 7) {
                        return `${dias} días`;
                    } else if (dias < 30) {
                        const semanas = Math.floor(dias / 7);
                        const diasRestantes = dias % 7;
                        let resultado = `${semanas} ${semanas === 1 ? 'semana' : 'semanas'}`;
                        if (diasRestantes > 0) {
                            resultado += ` y ${diasRestantes} ${diasRestantes === 1 ? 'día' : 'días'}`;
                        }
                        return resultado;
                    } else if (dias < 365) {
                        const meses = Math.floor(dias / 30);
                        const diasRestantes = dias % 30;
                        let resultado = `${meses} ${meses === 1 ? 'mes' : 'meses'}`;
                        if (diasRestantes > 0) {
                            const semanas = Math.floor(diasRestantes / 7);
                            if (semanas > 0) {
                                resultado += ` y ${semanas} ${semanas === 1 ? 'semana' : 'semanas'}`;
                            } else if (diasRestantes > 0) {
                                resultado += ` y ${diasRestantes} ${diasRestantes === 1 ? 'día' : 'días'}`;
                            }
                        }
                        return resultado;
                    } else {
                        const años = Math.floor(dias / 365);
                        const diasRestantes = dias % 365;
                        let resultado = `${años} ${años === 1 ? 'año' : 'años'}`;
                        if (diasRestantes > 0) {
                            const meses = Math.floor(diasRestantes / 30);
                            if (meses > 0) {
                                resultado += ` y ${meses} ${meses === 1 ? 'mes' : 'meses'}`;
                            }
                        }
                        return resultado;
                    }
                }
                return '';
            },

            // Formatear números para campos de combustible y costo
            formatCurrency(value) {
                if (!value) return '';
                return new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                }).format(value);
            },

            formatNumber(value) {
                if (!value) return '';
                return new Intl.NumberFormat('es-MX').format(value);
            }
        };
    }

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
--}}
@endpush
