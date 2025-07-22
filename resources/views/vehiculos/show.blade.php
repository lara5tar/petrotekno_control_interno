@extends('layouts.app')

@section('title', 'Detalle del Vehículo: ' . $vehiculo->placas)

@section('header', 'Detalle del Vehículo')

@section('content')
    <div class="mb-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-petroyellow">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('vehiculos.index') }}" class="text-gray-700 hover:text-petroyellow ml-1 md:ml-2 text-sm font-medium">Gestionar Vehículos</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2 text-sm font-medium">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} [{{ $vehiculo->placas }}]</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        @if($vehiculo->trashed())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Este vehículo fue eliminado el <strong>{{ $vehiculo->deleted_at->format('d/m/Y H:i') }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Encabezado con acciones -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})</h2>
            <div class="flex space-x-2">
                @if(!$vehiculo->trashed())
                    <a href="{{ route('vehiculos.edit', $vehiculo->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Editar
                    </a>
                @else
                    <form method="POST" action="{{ route('vehiculos.restore', $vehiculo->id) }}" class="inline"
                          onsubmit="return confirm('¿Estás seguro de que quieres restaurar este vehículo?');">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                            Restaurar
                        </button>
                    </form>
                @endif
                <a href="{{ route('vehiculos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Volver al listado
                </a>
            </div>
        </div>

        <!-- Datos Generales y Estado -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
            <!-- Columna 1-3: Datos Generales -->
            <div class="lg:col-span-3">
                <div class="border rounded-lg p-4 h-full">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Datos Generales</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Marca y Modelo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                            <input type="text" value="{{ $vehiculo->marca }}" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo (Año)</label>
                            <input type="text" value="{{ $vehiculo->modelo }} ({{ $vehiculo->anio }})" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        
                        <!-- Identificador (VIN) y Placas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Serie (VIN)</label>
                            <input type="text" value="{{ $vehiculo->n_serie }}" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Placas</label>
                            <input type="text" value="{{ $vehiculo->placas }}" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50 font-mono" readonly>
                        </div>
                        
                        <!-- Estado y Kilometraje -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado Actual</label>
                            <input type="text" value="{{ $vehiculo->estatus->nombre_estatus }}" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50 
                                @if($vehiculo->estatus->nombre_estatus == 'Activo') 
                                    text-green-800 bg-green-50
                                @elseif(str_contains(strtolower($vehiculo->estatus->nombre_estatus), 'mantenimiento')) 
                                    text-yellow-800 bg-yellow-50
                                @elseif(str_contains(strtolower($vehiculo->estatus->nombre_estatus), 'fuera') || str_contains(strtolower($vehiculo->estatus->nombre_estatus), 'inactivo')) 
                                    text-red-800 bg-red-50
                                @else 
                                    text-blue-800 bg-blue-50
                                @endif" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje Actual</label>
                            <input type="text" value="{{ number_format($vehiculo->kilometraje_actual) }} km" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna 4: Información de estado -->
            <div>
                <div class="border rounded-lg p-4 h-full">
                    <div class="text-center mb-4">
                        <p class="text-sm font-medium text-gray-700">Último Kilometraje</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($vehiculo->kilometraje_actual) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-700 mb-2">Estado Actual</p>
                        <span class="inline-flex px-3 py-2 text-sm font-medium rounded-md w-full justify-center
                            @if($vehiculo->estatus->nombre_estatus == 'Activo') 
                                bg-green-100 text-green-800
                            @elseif(str_contains(strtolower($vehiculo->estatus->nombre_estatus), 'mantenimiento')) 
                                bg-yellow-100 text-yellow-800
                            @elseif(str_contains(strtolower($vehiculo->estatus->nombre_estatus), 'fuera') || str_contains(strtolower($vehiculo->estatus->nombre_estatus), 'inactivo')) 
                                bg-red-100 text-red-800
                            @else 
                                bg-blue-100 text-blue-800
                            @endif">
                            {{ $vehiculo->estatus->nombre_estatus }}
                        </span>
                        @if(!$vehiculo->trashed())
                            <a href="{{ route('asignaciones.create') }}?vehiculo={{ $vehiculo->id }}" 
                               class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded block text-center">
                               Nueva Asignación
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Intervalos de mantenimiento -->
        @if($vehiculo->intervalo_km_motor || $vehiculo->intervalo_km_transmision || $vehiculo->intervalo_km_hidraulico)
            <div class="bg-white rounded-lg border mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Intervalos de Mantenimiento</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($vehiculo->intervalo_km_motor)
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">Motor</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ number_format($vehiculo->intervalo_km_motor) }}</p>
                                <p class="text-sm text-blue-600">kilómetros</p>
                            </div>
                        @endif
                        @if($vehiculo->intervalo_km_transmision)
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">Transmisión</h4>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($vehiculo->intervalo_km_transmision) }}</p>
                                <p class="text-sm text-green-600">kilómetros</p>
                            </div>
                        @endif
                        @if($vehiculo->intervalo_km_hidraulico)
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-800 mb-2">Hidráulico</h4>
                                <p class="text-2xl font-bold text-purple-600">{{ number_format($vehiculo->intervalo_km_hidraulico) }}</p>
                                <p class="text-sm text-purple-600">kilómetros</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Pestañas de navegación -->
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex -mb-px">
                <button class="py-2 px-4 border-b-2 border-petroyellow text-petroyellow font-medium">
                    Operación
                </button>
                <button class="py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Documentos
                </button>
                <button class="py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Mantenimientos
                </button>
            </nav>
        </div>

        <!-- Contenido de la pestaña Operación -->
        <div>
            <!-- Información de Obra -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Obra Actual</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Obra</label>
                        <input type="text" value="Libramiento Monterrey" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lugar</label>
                        <input type="text" value="Monterrey, N.L." class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asignación de Vehículos</label>
                        <input type="text" value="Titular de los agregados" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asignación de Personal</label>
                        <input type="text" value="Titular de los agregados" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de Obra</label>
                        <input type="text" value="1/02/2023" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Término de Obra</label>
                        <input type="text" value="1/04/2024" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Inicial</label>
                        <input type="text" value="1" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Final</label>
                        <input type="text" value="200" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                    <input type="text" value="Juan Pérez" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                </div>
            </div>
            
            <!-- Información del Operador -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-800">Operador Actual</h3>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded text-sm">Cambiar Operador</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" value="Marco Alfredo" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                        <input type="text" value="Delgado Reyes" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NSS</label>
                        <input type="text" value="140726883" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Domicilio</label>
                        <input type="text" value="Monterrey, Nuevo León" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                        <input type="text" value="8344" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Identificación Electoral</label>
                        <div class="flex">
                            <input type="text" value="DEMBR20112384" class="p-2 border border-gray-300 rounded-l-md w-full bg-gray-50" readonly>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 rounded-r-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Licencia de Manejo</label>
                        <div class="flex">
                            <input type="text" value="1687" class="p-2 border border-gray-300 rounded-l-md w-full bg-gray-50" readonly>
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 rounded-r-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio de Obra</label>
                        <input type="text" value="1/02/2023" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Término de Obra</label>
                        <input type="text" value="1/04/2024" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Inicial</label>
                        <input type="text" value="1" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Km Final</label>
                        <input type="text" value="200" class="p-2 border border-gray-300 rounded-md w-full bg-gray-50" readonly>
                    </div>
                </div>
            </div>
            
            <!-- Fotografía del vehículo -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Fotografía</h3>
                <div class="border rounded-lg overflow-hidden">
                    <img src="{{ asset('images/nissan_np300.jpg') }}" alt="Nissan NP300" class="w-full h-auto">
        </div>
        
        <!-- Información de fechas -->
        <div class="border rounded-lg p-4 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Fechas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <label class="block font-medium text-gray-600 mb-1">Fecha de Creación</label>
                    <p class="text-gray-900">{{ $vehiculo->created_at->format('d/m/Y H:i:s') }}</p>
                    <p class="text-gray-500">{{ $vehiculo->created_at->diffForHumans() }}</p>
                </div>
                @if($vehiculo->updated_at != $vehiculo->created_at)
                    <div>
                        <label class="block font-medium text-gray-600 mb-1">Última Actualización</label>
                        <p class="text-gray-900">{{ $vehiculo->updated_at->format('d/m/Y H:i:s') }}</p>
                        <p class="text-gray-500">{{ $vehiculo->updated_at->diffForHumans() }}</p>
                    </div>
                @endif
                @if($vehiculo->deleted_at)
                    <div>
                        <label class="block font-medium text-red-600 mb-1">Fecha de Eliminación</label>
                        <p class="text-red-900">{{ $vehiculo->deleted_at->format('d/m/Y H:i:s') }}</p>
                        <p class="text-red-500">{{ $vehiculo->deleted_at->diffForHumans() }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-between">
        <a href="{{ route('vehiculos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Regresar
        </a>
        
        @if(!$vehiculo->trashed())
            <!-- Zona de acciones peligrosas -->
            <div class="flex space-x-2">
                <a href="{{ route('kilometrajes.create') }}?vehiculo={{ $vehiculo->id }}" 
                   class="bg-purple-500 hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    Registrar KM
                </a>
                
                <a href="{{ route('mantenimientos.create') }}?vehiculo={{ $vehiculo->id }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Nuevo Mantenimiento
                </a>
                
                <form method="POST" action="{{ route('vehiculos.destroy', $vehiculo->id) }}" class="inline"
                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este vehículo? Esta acción se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v4a1 1 0 11-2 0V7zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V7a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Eliminar
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection