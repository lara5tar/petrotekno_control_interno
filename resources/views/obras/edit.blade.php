@extends('layouts.app')

@section('title', 'Editar Obra')

@section('header', 'Editar Obra')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Obras', 'url' => route('obras.index')],
        ['label' => 'Editar Obra']
    ]" />

    {{-- Mensajes de sesión --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

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
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Obra</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $obra->nombre_obra }}</p>
        </div>
        <div class="flex space-x-3">
            {{-- Verificar si tiene asignaciones del nuevo sistema o asignación directa --}}
            @php
                $tieneAsignacionDirecta = $obra->vehiculo_id && $obra->operador_id && !$obra->fecha_liberacion;
                $tieneAsignacionesActivas = method_exists($obra, 'asignacionesActivas') ? $obra->asignacionesActivas->count() > 0 : false;
                $totalAsignaciones = $tieneAsignacionDirecta ? 1 : 0;
                if ($tieneAsignacionesActivas) {
                    $totalAsignaciones += $obra->asignacionesActivas->count();
                }
            @endphp
            
            @if($totalAsignaciones > 0)
                <a href="{{ route('obras.show', $obra->id) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    Ver Asignaciones ({{ $totalAsignaciones }})
                </a>
            @else
                <a href="{{ route('obras.show', $obra->id) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nueva Asignación
                </a>
            @endif
            <a href="{{ route('obras.show', $obra->id) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                Ver Detalles
            </a>
            <a href="{{ route('obras.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al Listado
            </a>
        </div>
    </div>

    {{-- Alertas de estado --}}
    @php
        $tieneAsignacionDirecta = $obra->vehiculo_id && $obra->operador_id && !$obra->fecha_liberacion;
        $tieneAsignacionesActivas = method_exists($obra, 'asignacionesActivas') ? $obra->asignacionesActivas->count() > 0 : false;
        $totalAsignaciones = $tieneAsignacionDirecta ? 1 : 0;
        if ($tieneAsignacionesActivas) {
            $totalAsignaciones += $obra->asignacionesActivas->count();
        }
    @endphp
    
    @if($totalAsignaciones > 0)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <strong class="font-bold">Obra con Asignaciones Activas</strong>
                    <span class="block sm:inline">Esta obra tiene {{ $totalAsignaciones }} asignación(es) de vehículo(s) activa(s).</span>
                    @if($tieneAsignacionDirecta)
                        <span class="block text-sm mt-1">Sistema directo: {{ $obra->vehiculo->marca ?? 'N/A' }} {{ $obra->vehiculo->modelo ?? '' }} ({{ $obra->vehiculo->placas ?? 'N/A' }})</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario Principal -->
    <div class="bg-white rounded-lg shadow-lg p-6" x-data="obraFormController()">
        <form action="{{ route('obras.update', $obra->id) }}" method="POST" enctype="multipart/form-data" id="editObraForm" @submit="validateForm">
            @csrf
            @method('PUT')
            
            <div class="space-y-8">
                <!-- ================================ -->
                <!-- INFORMACIÓN BÁSICA DE LA OBRA -->
                <!-- ================================ -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Información Básica de la Obra</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre de la obra -->
                        <div class="md:col-span-2">
                            <label for="nombre_obra" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Obra <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre_obra" 
                                   name="nombre_obra" 
                                   required
                                   maxlength="255"
                                   value="{{ old('nombre_obra', $obra->nombre_obra) }}"
                                   placeholder="Ej: Construcción de puente vehicular"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nombre_obra') border-red-500 @enderror">
                            @error('nombre_obra') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Estatus -->
                        <div>
                            <label for="estatus" class="block text-sm font-medium text-gray-700 mb-2">
                                Estatus de la Obra <span class="text-red-500">*</span>
                            </label>
                            <select name="estatus" 
                                    id="estatus" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('estatus') border-red-500 @enderror">
                                <option value="">Seleccionar estatus...</option>
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

                        <!-- Avance -->
                        <div>
                            <label for="avance" class="block text-sm font-medium text-gray-700 mb-2">
                                Avance (%)
                            </label>
                            <input type="number" 
                                   id="avance" 
                                   name="avance" 
                                   min="0" 
                                   max="100"
                                   placeholder="0"
                                   value="{{ old('avance', $obra->avance ?? 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('avance') border-red-500 @enderror">
                            @error('avance') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                            
                            <!-- Barra de Progreso Visual -->
                            <div class="mt-3">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div id="progressBar" 
                                         class="h-3 rounded-full transition-all duration-300 bg-{{ $obra->avance >= 100 ? 'green' : ($obra->avance >= 75 ? 'blue' : ($obra->avance >= 50 ? 'yellow' : 'orange')) }}-600" 
                                         style="width: {{ $obra->avance ?? 0 }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1 text-center">
                                    <span id="progressText">{{ $obra->avance ?? 0 }}% completado</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   required
                                   value="{{ old('fecha_inicio', $obra->fecha_inicio ? $obra->fecha_inicio->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_inicio') border-red-500 @enderror">
                            @error('fecha_inicio') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Fecha de finalización -->
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Finalización (Estimada)
                            </label>
                            <input type="date" 
                                   id="fecha_fin" 
                                   name="fecha_fin"
                                   value="{{ old('fecha_fin', $obra->fecha_fin ? $obra->fecha_fin->format('Y-m-d') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('fecha_fin') border-red-500 @enderror">
                            @error('fecha_fin') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones
                            </label>
                            <textarea name="observaciones" 
                                    id="observaciones"
                                    rows="4" 
                                    maxlength="1000"
                                    placeholder="Agregue cualquier información adicional sobre la obra (características especiales, ubicación, etc.)" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones', $obra->observaciones) }}</textarea>
                            @error('observaciones') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                <span x-text="$refs.observaciones ? $refs.observaciones.value.length : 0" x-ref="observaciones"></span>/1000 caracteres
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ================================ -->
                <!-- ASIGNACIÓN DE RECURSOS -->
                <!-- ================================ -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Asignación de Recursos</h3>
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Sistema Directo</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Vehículo Asignado -->
                        <div>
                            <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehículo Asignado
                            </label>
                            <select name="vehiculo_id" 
                                    id="vehiculo_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehiculo_id') border-red-500 @enderror">
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

                        <!-- Operador Asignado -->
                        <div>
                            <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Operador Asignado
                            </label>
                            <select name="operador_id" 
                                    id="operador_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('operador_id') border-red-500 @enderror">
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

                        <!-- Encargado de Obra -->
                        <div>
                            <label for="encargado_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Encargado de Obra
                            </label>
                            <select name="encargado_id" 
                                    id="encargado_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('encargado_id') border-red-500 @enderror">
                                <option value="">Sin asignar</option>
                                @if($encargados && $encargados->count() > 0)
                                    @foreach($encargados as $encargado)
                                        <option value="{{ $encargado['id'] }}" 
                                                {{ old('encargado_id', $obra->encargado_id) == $encargado['id'] ? 'selected' : '' }}>
                                            {{ $encargado['nombre_completo'] }}
                                            <span class="text-gray-500">({{ $encargado['tipo'] == 'usuario' ? $encargado['rol'] ?? 'Usuario' : ($encargado['categoria'] ?? 'Personal') }})</span>
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('encargado_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ================================ -->
                <!-- CONTROL DE KILOMETRAJE -->
                <!-- ================================ -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Control de Kilometraje</h3>
                        <span class="ml-2 text-sm text-gray-500">(Para vehículo asignado)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kilometraje inicial -->
                        <div>
                            <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700 mb-2">
                                Kilometraje Inicial
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="kilometraje_inicial" 
                                       name="kilometraje_inicial" 
                                       min="0" 
                                       step="1"
                                       value="{{ old('kilometraje_inicial', $obra->kilometraje_inicial) }}"
                                       placeholder="Ejemplo: 45000"
                                       class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kilometraje_inicial') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-400 text-sm">km</span>
                                </div>
                            </div>
                            @error('kilometraje_inicial') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <!-- Kilometraje final -->
                        <div>
                            <label for="kilometraje_final" class="block text-sm font-medium text-gray-700 mb-2">
                                Kilometraje Final
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="kilometraje_final" 
                                       name="kilometraje_final" 
                                       min="0" 
                                       step="1"
                                       value="{{ old('kilometraje_final', $obra->kilometraje_final) }}"
                                       placeholder="Se registra al finalizar la obra"
                                       class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kilometraje_final') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-400 text-sm">km</span>
                                </div>
                            </div>
                            @error('kilometraje_final') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    @if($obra->kilometraje_inicial && $obra->kilometraje_final && $obra->kilometraje_final > $obra->kilometraje_inicial)
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-blue-800 font-medium">Kilometraje Recorrido: {{ number_format($obra->kilometraje_final - $obra->kilometraje_inicial) }} km</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- ================================ -->
                <!-- DOCUMENTOS DE LA OBRA -->
                <!-- ================================ -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Documentos de la Obra</h3>
                        <span class="ml-2 text-sm text-gray-500">(Reemplazar archivos existentes)</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Contrato -->
                        <div class="space-y-3">
                            <label class="flex text-sm font-medium text-gray-700 mb-3 items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Contrato
                            </label>
                            
                            @if($obra->archivo_contrato)
                                <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-blue-800">Archivo actual</span>
                                        </div>
                                        <a href="{{ $obra->getUrlContrato() }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Ver archivo</a>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="relative">
                                <input type="file" 
                                       id="archivo_contrato" 
                                       name="archivo_contrato" 
                                       accept=".pdf,.doc,.docx" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'contrato')" />
                                <label for="archivo_contrato" 
                                       class="cursor-pointer flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">{{ $obra->archivo_contrato ? 'Reemplazar Contrato' : 'Subir Contrato' }}</span>
                                </label>
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
                            
                            @if($obra->archivo_fianza)
                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-green-800">Archivo actual</span>
                                        </div>
                                        <a href="{{ $obra->getUrlFianza() }}" target="_blank" class="text-green-600 hover:text-green-800 text-sm">Ver archivo</a>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="relative">
                                <input type="file" 
                                       id="archivo_fianza" 
                                       name="archivo_fianza" 
                                       accept=".pdf,.doc,.docx" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'fianza')" />
                                <label for="archivo_fianza" 
                                       class="cursor-pointer flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">{{ $obra->archivo_fianza ? 'Reemplazar Fianza' : 'Subir Fianza' }}</span>
                                </label>
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
                            
                            @if($obra->archivo_acta_entrega_recepcion)
                                <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm text-yellow-800">Archivo actual</span>
                                        </div>
                                        <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" target="_blank" class="text-yellow-600 hover:text-yellow-800 text-sm">Ver archivo</a>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="relative">
                                <input type="file" 
                                       id="archivo_acta_entrega_recepcion" 
                                       name="archivo_acta_entrega_recepcion" 
                                       accept=".pdf,.doc,.docx" 
                                       class="hidden" 
                                       @change="handleFileInput($event, 'acta')" />
                                <label for="archivo_acta_entrega_recepcion" 
                                       class="cursor-pointer flex items-center justify-center w-full px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                    <svg class="h-8 w-8 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-base">{{ $obra->archivo_acta_entrega_recepcion ? 'Reemplazar Acta' : 'Subir Acta' }}</span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 text-center" x-text="fileStatus.acta || 'PDF, DOC, DOCX (máx. 10MB)'"></p>
                        </div>
                    </div>
                </div>

                <!-- ================================ -->
                <!-- INFORMACIÓN DE AUDITORÍA -->
                <!-- ================================ -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Información de Auditoría</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Fecha de Creación</label>
                            <div class="bg-gray-100 px-3 py-2 rounded text-sm">
                                {{ $obra->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Última Actualización</label>
                            <div class="bg-gray-100 px-3 py-2 rounded text-sm">
                                {{ $obra->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('obras.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200"
                        :disabled="isSubmitting"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }">
                    <span x-show="!isSubmitting">Actualizar Obra</span>
                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Actualizando...
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('obraFormController', () => ({
            fileStatus: {
                contrato: '',
                fianza: '',
                acta: ''
            },
            isSubmitting: false,
            
            handleFileInput(event, type) {
                const file = event.target.files[0];
                if (!file) {
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tamaño (10MB)
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    this.showError('El archivo es demasiado grande. Máximo 10MB');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    this.showError('Tipo de archivo no válido. Solo se permiten PDF, DOC y DOCX');
                    event.target.value = '';
                    this.fileStatus[type] = '';
                    return;
                }

                this.fileStatus[type] = `✓ ${file.name}`;
                console.log(`📄 Archivo ${type} seleccionado: ${file.name}`);
            },

            validateForm(event) {
                console.log('🔍 Validando formulario de edición...');
                
                // Validar campos básicos
                const nombreObra = document.getElementById('nombre_obra').value.trim();
                if (!nombreObra) {
                    event.preventDefault();
                    this.showError('El nombre de la obra es obligatorio');
                    document.getElementById('nombre_obra').focus();
                    return false;
                }

                // Validar estatus
                const estatus = document.getElementById('estatus').value;
                if (!estatus) {
                    event.preventDefault();
                    this.showError('Debe seleccionar un estatus para la obra');
                    document.getElementById('estatus').focus();
                    return false;
                }

                // Validar fechas
                const fechaInicio = document.getElementById('fecha_inicio').value;
                const fechaFin = document.getElementById('fecha_fin').value;
                
                if (!fechaInicio) {
                    event.preventDefault();
                    this.showError('La fecha de inicio es obligatoria');
                    document.getElementById('fecha_inicio').focus();
                    return false;
                }
                
                if (fechaFin && fechaInicio && new Date(fechaFin) < new Date(fechaInicio)) {
                    event.preventDefault();
                    this.showError('La fecha de finalización no puede ser anterior a la fecha de inicio');
                    document.getElementById('fecha_fin').focus();
                    return false;
                }

                // Validar kilometraje
                const kmInicial = document.getElementById('kilometraje_inicial').value;
                const kmFinal = document.getElementById('kilometraje_final').value;
                
                if (kmFinal && kmInicial && parseInt(kmFinal) < parseInt(kmInicial)) {
                    event.preventDefault();
                    this.showError('El kilometraje final no puede ser menor al inicial');
                    document.getElementById('kilometraje_final').focus();
                    return false;
                }
                
                this.isSubmitting = true;
                console.log('✅ Formulario válido, actualizando...');
                this.showSuccess('Actualizando obra...');
                return true;
            },

            showSuccess(message) {
                this.showNotification(message, 'success');
            },

            showError(message) {
                this.showNotification(message, 'error');
            },

            showNotification(message, type) {
                const div = document.createElement('div');
                div.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg transition-opacity duration-300 ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                div.textContent = message;
                document.body.appendChild(div);
                
                setTimeout(() => {
                    div.style.opacity = '0';
                    setTimeout(() => {
                        if (div.parentNode) {
                            div.parentNode.removeChild(div);
                        }
                    }, 300);
                }, 3000);
            }
        }));
    });

    // Funciones para manejar la barra de progreso
    document.addEventListener('DOMContentLoaded', function() {
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
        if (avanceInput) {
            avanceInput.addEventListener('input', updateProgressBar);
        }

        // Auto-actualizar el avance según el estatus
        if (estatusSelect) {
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
        }

        // Inicializar la barra de progreso
        updateProgressBar();
        
        console.log('📄 Formulario de edición de obra configurado correctamente');
    });
</script>
@endpush
