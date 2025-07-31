@extends('layouts.app')

@section('title', 'Editar Obra')

@section('header', 'Editar Obra')

@section('content')
<div class="p-6">
    <!-- Header con Breadcrumb -->
    <div class="mb-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L9 5.414V17a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V5.414l2.293 2.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Inicio</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('obras.index') }}" class="text-gray-700 hover:text-blue-600">Obras</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="text-gray-500">Editar Obra</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-2xl font-bold text-gray-800">Editar Obra</h1>
    </div>

    <!-- Formulario de Edición -->
    <form action="{{ route('obras.update', $obra->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Alertas de Errores -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Hay errores en el formulario:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Panel Izquierdo - Datos Principales -->
            <div class="space-y-6">
                <!-- Información General -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Información General</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label for="nombre_obra" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre de la Obra *
                            </label>
                            <input type="text" 
                                   id="nombre_obra" 
                                   name="nombre_obra" 
                                   value="{{ old('nombre_obra', $obra->nombre_obra) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre_obra') border-red-500 @enderror" 
                                   required>
                            @error('nombre_obra')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="estatus" class="block text-sm font-medium text-gray-700 mb-1">
                                    Estado *
                                </label>
                                <select id="estatus" 
                                        name="estatus" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estatus') border-red-500 @enderror" 
                                        required>
                                    <option value="">Selecciona un estado</option>
                                    @foreach($estatusOptions as $valor => $nombre)
                                        <option value="{{ $valor }}" {{ old('estatus', $obra->estatus) == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estatus')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('avance') border-red-500 @enderror">
                                @error('avance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Barra de Progreso Visual -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Progreso Visual</label>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div id="progressBar" 
                                     class="h-3 rounded-full transition-all duration-300 bg-{{ $obra->avance >= 100 ? 'green' : ($obra->avance >= 75 ? 'blue' : ($obra->avance >= 50 ? 'yellow' : 'orange')) }}-600" 
                                     style="width: {{ $obra->avance ?? 0 }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 text-center">
                                <span id="progressText">{{ $obra->avance ?? 0 }}% completado</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de Inicio *
                                </label>
                                <input type="date" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="{{ old('fecha_inicio', $obra->fecha_inicio ? $obra->fecha_inicio->format('Y-m-d') : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_inicio') border-red-500 @enderror" 
                                       required>
                                @error('fecha_inicio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de Fin
                                </label>
                                <input type="date" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="{{ old('fecha_fin', $obra->fecha_fin ? $obra->fecha_fin->format('Y-m-d') : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_fin') border-red-500 @enderror">
                                @error('fecha_fin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asignación de Recursos -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Asignación de Recursos</h3>
                        <p class="text-sm text-gray-600 mt-1">Los campos de asignación son opcionales</p>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Vehículo Asignado
                                </label>
                                <select id="vehiculo_id" 
                                        name="vehiculo_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('vehiculo_id') border-red-500 @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}" 
                                                {{ old('vehiculo_id', $obra->vehiculo_id) == $vehiculo->id ? 'selected' : '' }}>
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehiculo_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="fecha_asignacion" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de Asignación
                                </label>
                                <input type="date" 
                                       id="fecha_asignacion" 
                                       name="fecha_asignacion" 
                                       value="{{ old('fecha_asignacion', $obra->fecha_asignacion ? $obra->fecha_asignacion->format('Y-m-d') : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_asignacion') border-red-500 @enderror">
                                @error('fecha_asignacion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Operador
                                </label>
                                <select id="operador_id" 
                                        name="operador_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('operador_id') border-red-500 @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($operadores as $operador)
                                        <option value="{{ $operador->id }}" 
                                                {{ old('operador_id', $obra->operador_id) == $operador->id ? 'selected' : '' }}>
                                            {{ $operador->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('operador_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Encargado
                                </label>
                                <select id="encargado_id" 
                                        name="encargado_id" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('encargado_id') border-red-500 @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($encargados as $encargado)
                                        <option value="{{ $encargado->id }}" 
                                                {{ old('encargado_id', $obra->encargado_id) == $encargado->id ? 'selected' : '' }}>
                                            {{ $encargado->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('encargado_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="fecha_liberacion" class="block text-sm font-medium text-gray-700 mb-1">
                                Fecha de Liberación
                            </label>
                            <input type="date" 
                                   id="fecha_liberacion" 
                                   name="fecha_liberacion" 
                                   value="{{ old('fecha_liberacion', $obra->fecha_liberacion ? $obra->fecha_liberacion->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_liberacion') border-red-500 @enderror">
                            @error('fecha_liberacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Control de Kilometrajes -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Control de Kilometrajes</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kilometraje Inicial
                                </label>
                                <input type="number" 
                                       id="kilometraje_inicial" 
                                       name="kilometraje_inicial" 
                                       value="{{ old('kilometraje_inicial', $obra->kilometraje_inicial) }}"
                                       min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kilometraje_inicial') border-red-500 @enderror">
                                @error('kilometraje_inicial')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="kilometraje_final" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kilometraje Final
                                </label>
                                <input type="number" 
                                       id="kilometraje_final" 
                                       name="kilometraje_final" 
                                       value="{{ old('kilometraje_final', $obra->kilometraje_final) }}"
                                       min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kilometraje_final') border-red-500 @enderror">
                                @error('kilometraje_final')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if($obra->kilometraje_inicial && $obra->kilometraje_final && $obra->kilometraje_final > $obra->kilometraje_inicial)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-blue-800 font-medium">Kilometraje Recorrido: {{ number_format($obra->kilometraje_final - $obra->kilometraje_inicial) }} km</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel Derecho - Información Complementaria -->
            <div class="space-y-6">
                <!-- Control de Combustible -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Control de Combustible</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="combustible_inicial" class="block text-sm font-medium text-gray-700 mb-1">
                                    Combustible Inicial (L)
                                </label>
                                <input type="number" 
                                       id="combustible_inicial" 
                                       name="combustible_inicial" 
                                       value="{{ old('combustible_inicial', $obra->combustible_inicial) }}"
                                       min="0" 
                                       step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('combustible_inicial') border-red-500 @enderror">
                                @error('combustible_inicial')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="combustible_final" class="block text-sm font-medium text-gray-700 mb-1">
                                    Combustible Final (L)
                                </label>
                                <input type="number" 
                                       id="combustible_final" 
                                       name="combustible_final" 
                                       value="{{ old('combustible_final', $obra->combustible_final) }}"
                                       min="0" 
                                       step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('combustible_final') border-red-500 @enderror">
                                @error('combustible_final')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="combustible_suministrado" class="block text-sm font-medium text-gray-700 mb-1">
                                    Combustible Suministrado (L)
                                </label>
                                <input type="number" 
                                       id="combustible_suministrado" 
                                       name="combustible_suministrado" 
                                       value="{{ old('combustible_suministrado', $obra->combustible_suministrado) }}"
                                       min="0" 
                                       step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('combustible_suministrado') border-red-500 @enderror">
                                @error('combustible_suministrado')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="costo_combustible" class="block text-sm font-medium text-gray-700 mb-1">
                                    Costo Combustible ($)
                                </label>
                                <input type="number" 
                                       id="costo_combustible" 
                                       name="costo_combustible" 
                                       value="{{ old('costo_combustible', $obra->costo_combustible) }}"
                                       min="0" 
                                       step="0.01" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('costo_combustible') border-red-500 @enderror">
                                @error('costo_combustible')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Observaciones</h3>
                    </div>
                    <div class="p-4">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                            Observaciones y Notas
                        </label>
                        <textarea id="observaciones" 
                                  name="observaciones" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('observaciones') border-red-500 @enderror"
                                  placeholder="Escriba observaciones sobre la obra, asignaciones, problemas, etc.">{{ old('observaciones', $obra->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="text-xs text-gray-500 mt-1">
                            Máximo 1000 caracteres
                        </div>
                    </div>
                </div>

                <!-- Documentos Principales -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <!-- Documentos -->
                        <div class="row mb-4 mt-2">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Documentos de la Obra
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Actualizar o subir documentos importantes de la obra. Los archivos deben ser PDF, JPG, PNG o DOC (máximo 10MB cada uno).
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Contrato -->
                            <div class="col-md-6 mb-3">
                                <label for="archivo_contrato" class="form-label">
                                    <i class="fas fa-file-contract me-1"></i>
                                    Contrato
                                </label>
                                
                                @if($obra->archivo_contrato)
                                    <div class="alert alert-info mb-3" role="alert">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf me-2"></i>
                                                <span class="small">Archivo actual</span>
                                            </div>
                                            <a href="{{ $obra->getUrlContrato() }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Ver archivo
                                            </a>
                                        </div>
                                        <small class="text-muted">Subido: {{ $obra->fecha_subida_contrato?->format('d/m/Y H:i') }}</small>
                                    </div>
                                @endif
                                
                                <input type="file" 
                                       class="form-control @error('archivo_contrato') is-invalid @enderror" 
                                       id="archivo_contrato" 
                                       name="archivo_contrato" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="form-text text-muted">PDF, DOC, DOCX (máx. 10MB)</small>
                                @error('archivo_contrato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fianza -->
                            <div class="col-md-6 mb-3">
                                <label for="archivo_fianza" class="form-label">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>
                                    Fianza
                                </label>
                                
                                @if($obra->archivo_fianza)
                                    <div class="alert alert-success mb-3" role="alert">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf me-2"></i>
                                                <span class="small">Archivo actual</span>
                                            </div>
                                            <a href="{{ $obra->getUrlFianza() }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-eye me-1"></i>Ver archivo
                                            </a>
                                        </div>
                                        <small class="text-muted">Subido: {{ $obra->fecha_subida_fianza?->format('d/m/Y H:i') }}</small>
                                    </div>
                                @endif
                                
                                <input type="file" 
                                       class="form-control @error('archivo_fianza') is-invalid @enderror" 
                                       id="archivo_fianza" 
                                       name="archivo_fianza" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="form-text text-muted">PDF, DOC, DOCX (máx. 10MB)</small>
                                @error('archivo_fianza')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Acta Entrega-Recepción -->
                            <div class="col-md-6 mb-3">
                                <label for="archivo_acta_entrega_recepcion" class="form-label">
                                    <i class="fas fa-file-signature me-1"></i>
                                    Acta Entrega-Recepción
                                </label>
                                
                                @if($obra->archivo_acta_entrega_recepcion)
                                    <div class="alert alert-warning mb-3" role="alert">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf me-2"></i>
                                                <span class="small">Archivo actual</span>
                                            </div>
                                            <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-eye me-1"></i>Ver archivo
                                            </a>
                                        </div>
                                        <small class="text-muted">Subido: {{ $obra->fecha_subida_acta?->format('d/m/Y H:i') }}</small>
                                    </div>
                                @endif
                                
                                <input type="file" 
                                       class="form-control @error('archivo_acta_entrega_recepcion') is-invalid @enderror" 
                                       id="archivo_acta_entrega_recepcion" 
                                       name="archivo_acta_entrega_recepcion" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="form-text text-muted">PDF, DOC, DOCX (máx. 10MB)</small>
                                @error('archivo_acta_entrega_recepcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="alert alert-info" role="alert">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Información sobre archivos:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Subir un nuevo archivo reemplazará el anterior</li>
                                        <li>Deje vacío si no desea cambiar el archivo actual</li>
                                        <li>Los archivos anteriores se conservan como respaldo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Auditoría -->
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Información de Auditoría</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Creado:</span>
                                <div class="font-medium">{{ $obra->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Última actualización:</span>
                                <div class="font-medium">{{ $obra->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        @if($obra->fecha_eliminacion)
                        <div class="bg-red-50 border border-red-200 rounded p-2">
                            <span class="text-red-600 text-sm">Eliminado:</span>
                            <div class="text-red-800 font-medium">{{ $obra->fecha_eliminacion->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-between pt-6 border-t border-gray-200">
            <div class="flex space-x-3">
                <a href="{{ route('obras.show', $obra->id) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Ver Obra</span>
                </a>
                <a href="{{ route('obras.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <span>Volver al Listado</span>
                </a>
            </div>
            <div class="flex space-x-3">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                    </svg>
                    <span>Actualizar Obra</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const estatusSelect = document.getElementById('estatus');
    const avanceInput = document.getElementById('avance');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');

    // Función para actualizar la barra de progreso
    function updateProgressBar() {
        const avance = parseInt(avanceInput.value) || 0;
        progressBar.style.width = avance + '%';
        progressText.textContent = avance + '% completado';
        
        // Cambiar color según el avance
        progressBar.className = 'h-3 rounded-full transition-all duration-300 ';
        if (avance >= 100) {
            progressBar.classList.add('bg-green-600');
        } else if (avance >= 75) {
            progressBar.classList.add('bg-blue-600');
        } else if (avance >= 50) {
            progressBar.classList.add('bg-yellow-500');
        } else {
            progressBar.classList.add('bg-orange-500');
        }
    }

    // Actualizar barra de progreso cuando cambia el avance
    avanceInput.addEventListener('input', updateProgressBar);

    // Auto-actualizar el avance según el estatus
    estatusSelect.addEventListener('change', function() {
        if (this.value === 'completada') {
            avanceInput.value = 100;
        } else if (this.value === 'planificada') {
            avanceInput.value = 0;
        } else if (this.value === 'en_progreso' && avanceInput.value == 0) {
            avanceInput.value = 10;
        }
        updateProgressBar();
    });

    // Validación del kilometraje final
    const kmInicialInput = document.getElementById('kilometraje_inicial');
    const kmFinalInput = document.getElementById('kilometraje_final');

    function validateKilometraje() {
        const kmInicial = parseInt(kmInicialInput.value) || 0;
        const kmFinal = parseInt(kmFinalInput.value) || 0;
        
        if (kmFinal > 0 && kmInicial > 0 && kmFinal < kmInicial) {
            kmFinalInput.setCustomValidity('El kilometraje final debe ser mayor o igual al inicial');
        } else {
            kmFinalInput.setCustomValidity('');
        }
    }

    kmInicialInput.addEventListener('input', validateKilometraje);
    kmFinalInput.addEventListener('input', validateKilometraje);

    // Inicializar la barra de progreso
    updateProgressBar();
});
</script>

@endsection
