@extends('layouts.app')

@section('title', 'Editar Obra')

@section('header', 'Gestión de Obras')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Obras', 'url' => route('obras.index')],
        ['label' => 'Editar Obra']
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
                        Se encontraron {{ $errors->count() }} error(es):
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

    {{-- Header principal --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Editar Obra</h2>
        <div class="flex space-x-2">
            <a href="{{ route('obras.show', $obra->id) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                Ver Detalles
            </a>
            <a href="{{ route('obras.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al Listado
            </a>
        </div>
    </div>

    {{-- Formulario principal --}}
    <form action="{{ route('obras.update', $obra->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

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
                           value="{{ old('nombre_obra', $obra->nombre_obra) }}"
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
                           value="{{ old('ubicacion', $obra->ubicacion) }}"
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
                        @php
                            $estados = ['planificada', 'en_progreso', 'suspendida', 'completada', 'cancelada'];
                        @endphp
                        @foreach($estados as $estado)
                            <option value="{{ $estado }}" {{ old('estatus', $obra->estatus) == $estado ? 'selected' : '' }}>
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
                           value="{{ old('avance', $obra->avance ?? 0) }}"
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
                           value="{{ old('fecha_inicio', $obra->fecha_inicio ? $obra->fecha_inicio->format('Y-m-d') : '') }}"
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
                           value="{{ old('fecha_fin', $obra->fecha_fin ? $obra->fecha_fin->format('Y-m-d') : '') }}"
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
                        @if(isset($encargados))
                            @foreach($encargados as $encargado)
                                <option value="{{ $encargado['id'] }}" {{ old('encargado_id', $obra->encargado_id) == $encargado['id'] ? 'selected' : '' }}
                                        data-testid="encargado-option-{{ $encargado['id'] }}"
                                        data-categoria="{{ $encargado['categoria'] ?? 'Sin puesto' }}">
                                    {{ $encargado['nombre_completo'] ?? $encargado['nombre_usuario'] }}
                                    @if(isset($encargado['categoria']))
                                        - {{ $encargado['categoria'] }}
                                    @elseif(isset($encargado['rol']))
                                        - {{ $encargado['rol'] }}
                                    @endif
                                </option>
                            @endforeach
                        @endif
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
                              placeholder="Observaciones adicionales sobre la obra">{{ old('observaciones', $obra->observaciones) }}</textarea>
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
                Información de Vehículos Asignados
            </h3>
            <p class="text-sm text-gray-500 mb-6">
                <strong>Nota:</strong> La gestión de asignaciones de vehículos se realiza desde la 
                <a href="{{ route('obras.show', $obra->id) }}" class="text-blue-600 hover:underline font-medium">página de detalles de la obra</a>.
            </p>

            @php
                $tieneVehiculoDirecto = $obra->vehiculo_id && $obra->vehiculo;
                $tieneAsignacionesActivas = isset($obra->asignacionesActivas) && $obra->asignacionesActivas->count() > 0;
            @endphp

            @if($tieneVehiculoDirecto || $tieneAsignacionesActivas)
                <div class="space-y-3">
                    {{-- Vehículo directo --}}
                    @if($tieneVehiculoDirecto)
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-green-600 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $obra->vehiculo->marca }} {{ $obra->vehiculo->modelo }}</div>
                                    <div class="text-sm text-gray-600">Placas: {{ $obra->vehiculo->placas }}</div>
                                    @if($obra->operador)
                                        <div class="text-xs text-gray-600">Operador: {{ $obra->operador->nombre_completo }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold border border-green-300">
                                Asignado (Sistema Directo)
                            </div>
                        </div>
                    @endif

                    {{-- Asignaciones activas --}}
                    @if($tieneAsignacionesActivas)
                        @foreach($obra->asignacionesActivas as $asignacion)
                            <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        @if($asignacion->vehiculo)
                                            <div class="font-medium text-gray-900">{{ $asignacion->vehiculo->marca }} {{ $asignacion->vehiculo->modelo }}</div>
                                            <div class="text-sm text-gray-600">Placas: {{ $asignacion->vehiculo->placas }}</div>
                                        @else
                                            <div class="font-medium text-gray-500">Vehículo no disponible</div>
                                        @endif
                                        @if($asignacion->operador)
                                            <div class="text-xs text-gray-600">Operador: {{ $asignacion->operador->nombre_completo }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500">
                                            Asignado: {{ $asignacion->fecha_asignacion ? $asignacion->fecha_asignacion->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold border border-blue-300">
                                    Activo
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @else
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-sm text-gray-700 mt-3 font-medium">No hay vehículos asignados a esta obra</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Visite la <a href="{{ route('obras.show', $obra->id) }}" class="text-blue-600 hover:underline">página de detalles</a> para asignar vehículos
                    </p>
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
            <p class="text-sm text-gray-500 mb-6">Archivos relacionados con la obra (subir nuevos reemplazará los existentes)</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Contrato --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contrato</label>
                    
                    @if($obra->archivo_contrato)
                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">Archivo actual</span>
                                </div>
                                <a href="{{ asset('storage/' . $obra->archivo_contrato) }}" 
                                   target="_blank"
                                   class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Ver archivo
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="relative">
                        <input type="file" name="archivo_contrato" accept=".pdf,.doc,.docx" 
                               class="hidden" id="archivo_contrato" onchange="updateFileLabel('archivo_contrato')">
                        <label for="archivo_contrato" id="label_archivo_contrato"
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon_archivo_contrato">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base" id="text_archivo_contrato">
                                {{ $obra->archivo_contrato ? 'Reemplazar Contrato' : 'Subir Contrato' }}
                            </span>
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
                    
                    @if($obra->archivo_fianza)
                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">Archivo actual</span>
                                </div>
                                <a href="{{ asset('storage/' . $obra->archivo_fianza) }}" 
                                   target="_blank"
                                   class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Ver archivo
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="relative">
                        <input type="file" name="archivo_fianza" accept=".pdf,.doc,.docx" 
                               class="hidden" id="archivo_fianza" onchange="updateFileLabel('archivo_fianza')">
                        <label for="archivo_fianza" id="label_archivo_fianza"
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon_archivo_fianza">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base" id="text_archivo_fianza">
                                {{ $obra->archivo_fianza ? 'Reemplazar Fianza' : 'Subir Fianza' }}
                            </span>
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
                    
                    @if($obra->archivo_acta_entrega_recepcion)
                        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">Archivo actual</span>
                                </div>
                                <a href="{{ asset('storage/' . $obra->archivo_acta_entrega_recepcion) }}" 
                                   target="_blank"
                                   class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Ver archivo
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="relative">
                        <input type="file" name="archivo_acta_entrega_recepcion" accept=".pdf,.doc,.docx" 
                               class="hidden" id="archivo_acta_entrega_recepcion" onchange="updateFileLabel('archivo_acta_entrega_recepcion')">
                        <label for="archivo_acta_entrega_recepcion" id="label_archivo_acta_entrega_recepcion"
                               class="cursor-pointer inline-flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-petroyellow transition-colors">
                            <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon_archivo_acta_entrega_recepcion">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-base" id="text_archivo_acta_entrega_recepcion">
                                {{ $obra->archivo_acta_entrega_recepcion ? 'Reemplazar Acta' : 'Subir Acta' }}
                            </span>
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
                        {{ $obra->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Última Actualización</label>
                    <div class="bg-gray-50 border border-gray-200 px-4 py-3 rounded-lg text-sm text-gray-800">
                        {{ $obra->updated_at->format('d/m/Y H:i') }}
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
                Actualizar Obra
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
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
        
        // Restaurar texto original basado en si existe archivo previo
        const originalTexts = {
            'archivo_contrato': '{{ $obra->archivo_contrato ? "Reemplazar Contrato" : "Subir Contrato" }}',
            'archivo_fianza': '{{ $obra->archivo_fianza ? "Reemplazar Fianza" : "Subir Fianza" }}',
            'archivo_acta_entrega_recepcion': '{{ $obra->archivo_acta_entrega_recepcion ? "Reemplazar Acta" : "Subir Acta" }}'
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
