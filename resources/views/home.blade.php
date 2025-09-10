@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true]
    ]" />
    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Bienvenido, {{ Auth::user()->name ?? 'Usuario' }}</h2>
        <p class="text-gray-600">Panel de control del sistema de Control Interno de Petrotekno</p>
        <p class="text-sm text-gray-500 mt-1">Última actualización: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <!-- Tarjetas de resumen con diseño consistente -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Activos</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_vehiculos'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Total de activos registrados</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Disponibles</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['vehiculos_disponibles'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Activos disponibles</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Asignados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['vehiculos_asignados'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Activos asignados</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Personal</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_personal'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Personal activo</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Obras</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['obras_activas'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM7 5h10v2H7V5zm0 4h10v2H7V9zm0 4h10v2H7v-2z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Obras en progreso</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Programados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['mantenimientos_programados'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Mantenimientos programados</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">Completados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['mantenimientos_completados'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Mantenimientos completados</p>
        </div>
    </div>

    <!-- Widget de Acceso Rápido para Kilometrajes -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Acceso Rápido - Kilometrajes</h3>
                <div class="flex space-x-2">
                    <button id="btn-carga-manual" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Carga Manual
                    </button>
                    <button id="btn-carga-masiva" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Carga Masiva
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Carga Individual</p>
                            <p class="text-xs text-gray-500">Registro manual de kilometraje</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Carga Masiva</p>
                            <p class="text-xs text-gray-500">Importar desde archivo Excel</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Plantilla Excel</p>
                            <p class="text-xs text-gray-500">Descargar formato estándar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Actividad Reciente
            </h3>
            <p class="text-sm text-gray-500 mt-1">Últimas acciones realizadas en el sistema</p>
        </div>
        
        <div class="p-6">
            @forelse($actividadReciente as $loop => $actividad)
            <div class="flex items-start space-x-4 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <!-- Icono de actividad -->
                <div class="flex-shrink-0">
                    @if(str_contains($actividad['descripcion'], 'Mantenimiento'))
                        <div class="bg-green-100 p-2 rounded-full">
                            <svg class="w-5 h-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                    @else
                        <div class="bg-blue-100 p-2 rounded-full">
                            <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                
                <!-- Contenido de la actividad -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900 truncate">
                            {{ $actividad['descripcion'] }}
                        </h4>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ str_contains($actividad['descripcion'], 'Mantenimiento') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ str_contains($actividad['descripcion'], 'Mantenimiento') ? 'Mantenimiento' : 'Kilometraje' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-1 flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                        </svg>
                        {{ $actividad['vehiculo'] }}
                    </div>
                    
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $actividad['fecha']->diffForHumans() }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $actividad['usuario'] }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-gray-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No hay actividad reciente</h3>
                <p class="text-sm text-gray-500">Las actividades del sistema aparecerán aquí cuando se realicen acciones.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal para Carga Manual -->
    <div id="modal-carga-manual" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Carga Manual de Kilometraje</h3>
                    <button onclick="cerrarModal('modal-carga-manual')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="form-carga-manual" onsubmit="submitCargaManual(event)">
                    @csrf
                    <div class="space-y-4">
                        <!-- Selector de Vehículo -->
                        <div>
                            <label for="vehiculo_search" class="block text-sm font-medium text-gray-700 mb-2">Buscar Vehículo</label>
                            <div class="relative">
                                <input type="text" id="vehiculo_search" placeholder="Buscar por ID, marca, modelo o placas..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       autocomplete="off">
                                <input type="hidden" name="vehiculo_id" id="vehiculo_id" required>
                                
                                <!-- Contenedor de sugerencias -->
                                <div id="sugerencias_container" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                                    <div id="sugerencias_list" class="py-1">
                                        <!-- Las sugerencias se cargarán aquí dinámicamente -->
                                    </div>
                                    <div id="no_results" class="px-3 py-2 text-gray-500 text-sm hidden">
                                        No se encontraron vehículos
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vehículo seleccionado -->
                            <div id="vehiculo_seleccionado" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md hidden">
                                <div class="flex justify-between items-center">
                                    <span id="vehiculo_info" class="text-sm text-blue-800"></span>
                                    <button type="button" onclick="limpiarSeleccion()" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fecha de Captura -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Captura</label>
                            <input type="date" id="fecha_captura" name="fecha_captura" required 
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Kilometraje -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kilometraje</label>
                            <input type="number" id="kilometraje" name="kilometraje" required min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ingrese el kilometraje actual">
                            <p id="km-info" class="text-xs text-gray-500 mt-1 hidden"></p>
                        </div>
                        
                        <!-- Cantidad de Combustible -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad de Combustible (Litros) - Opcional</label>
                            <input type="number" id="cantidad_combustible" name="cantidad_combustible" min="0" max="9999.99" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ej: 50.5">
                            <p class="text-xs text-gray-500 mt-1">Cantidad de combustible cargado en litros</p>
                        </div>
                        
                        <!-- Peso de Carga -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Peso de Carga (Toneladas) - Opcional</label>
                            <input type="number" id="peso_carga" name="peso_carga" min="0" max="999.99" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ej: 2.5">
                            <p class="text-xs text-gray-500 mt-1">Peso de la carga transportada en toneladas</p>
                        </div>
                        
                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones (Opcional)</label>
                            <textarea id="observaciones" name="observaciones" rows="3" maxlength="500"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Observaciones adicionales..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="cerrarModal('modal-carga-manual')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-submit-manual"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                            Registrar Kilometraje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Carga Masiva -->
    <div id="modal-carga-masiva" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Carga Masiva de Kilometrajes</h3>
                    <button onclick="cerrarModal('modal-carga-masiva')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Instrucciones:</h4>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                    <li>• Descarga la plantilla Excel haciendo clic en el botón de abajo</li>
                                    <li>• Completa los datos de kilometraje en el archivo</li>
                                    <li>• Sube el archivo completado para procesar los registros</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="form-carga-masiva" onsubmit="submitCargaMasiva(event)" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <!-- Descargar Plantilla -->
                        <div class="text-center">
                            <a href="{{ route('kilometrajes.descargar-plantilla') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Descargar Plantilla Excel
                            </a>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <!-- Subir Archivo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Archivo Excel</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="archivo_excel" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Subir archivo</span>
                                                <input id="archivo_excel" name="archivo_excel" type="file" accept=".xlsx,.xls" required class="sr-only" onchange="mostrarArchivoSeleccionado(this)">
                                            </label>
                                            <p class="pl-1">o arrastra y suelta</p>
                                        </div>
                                        <p class="text-xs text-gray-500">Solo archivos Excel (.xlsx, .xls)</p>
                                        <p id="archivo-seleccionado" class="text-sm text-green-600 font-medium hidden"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="cerrarModal('modal-carga-masiva')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-submit-masiva"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                            <span id="texto-submit-masiva">Procesar Archivo</span>
                            <svg id="loading-masiva" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                
                <!-- Barra de progreso -->
                <div id="progress-container" class="hidden mt-4">
                    <div class="bg-gray-200 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="progress-text" class="text-sm text-gray-600 mt-2">Procesando archivo...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Variables globales
window.vehiculosData = <?php echo json_encode($vehiculos ?? []); ?>;

// Funciones para abrir y cerrar modales
function abrirModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    
    // Limpiar formularios
    if (modalId === 'modal-carga-manual') {
        document.getElementById('form-carga-manual').reset();
        document.getElementById('km-info').classList.add('hidden');
    } else if (modalId === 'modal-carga-masiva') {
        document.getElementById('form-carga-masiva').reset();
        document.getElementById('archivo-seleccionado').classList.add('hidden');
        document.getElementById('progress-container').classList.add('hidden');
    }
}

// Event listeners para los botones del widget
document.addEventListener('DOMContentLoaded', function() {
    // Botón Carga Manual
    document.getElementById('btn-carga-manual').addEventListener('click', function() {
        abrirModal('modal-carga-manual');
    });
    
    // Botón Carga Masiva
    document.getElementById('btn-carga-masiva').addEventListener('click', function() {
        abrirModal('modal-carga-masiva');
    });
    
    // Cerrar modal al hacer clic fuera
    document.getElementById('modal-carga-manual').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal('modal-carga-manual');
        }
    });
    
    document.getElementById('modal-carga-masiva').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal('modal-carga-masiva');
        }
    });
    
    // Configurar validación de kilometraje en tiempo real
    const kilometrajeInput = document.getElementById('kilometraje');
    if (kilometrajeInput) {
        kilometrajeInput.addEventListener('input', validarKilometraje);
    }
        
        // Configurar buscador de vehículos con sugerencias
        configurarBuscadorVehiculos();
        
        // Cerrar sugerencias al hacer clic fuera
        document.addEventListener('click', function(e) {
            const container = document.getElementById('sugerencias_container');
            const searchInput = document.getElementById('vehiculo_search');
            
            if (!searchInput.contains(e.target) && !container.contains(e.target)) {
                container.classList.add('hidden');
            }
        });
    });

// Función para mostrar archivo seleccionado
function mostrarArchivoSeleccionado(input) {
    const archivoSeleccionado = document.getElementById('archivo-seleccionado');
    if (input.files && input.files[0]) {
        archivoSeleccionado.textContent = `Archivo seleccionado: ${input.files[0].name}`;
        archivoSeleccionado.classList.remove('hidden');
    } else {
        archivoSeleccionado.classList.add('hidden');
    }
}

// Función para enviar carga manual
function submitCargaManual(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('btn-submit-manual');
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Registrando...';
    
    fetch('{{ route("kilometrajes.carga-manual") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje de éxito
            mostrarNotificacion('Kilometraje registrado exitosamente', 'success');
            cerrarModal('modal-carga-manual');
            // Recargar página para actualizar estadísticas
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            mostrarNotificacion(data.message || 'Error al registrar el kilometraje', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('Error al procesar la solicitud', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Registrar Kilometraje';
    });
}

// Función para enviar carga masiva
function submitCargaMasiva(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('btn-submit-masiva');
    const textoSubmit = document.getElementById('texto-submit-masiva');
    const loadingIcon = document.getElementById('loading-masiva');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    textoSubmit.textContent = 'Procesando...';
    loadingIcon.classList.remove('hidden');
    progressContainer.classList.remove('hidden');
    
    // Simular progreso
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 200);
    
    fetch('{{ route("kilometrajes.procesar-carga-masiva") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        
        if (data.success) {
            progressText.textContent = `Procesamiento completado: ${data.procesados} registros procesados`;
            mostrarNotificacion(`Carga masiva completada: ${data.procesados} kilometrajes procesados`, 'success');
            
            setTimeout(() => {
                cerrarModal('modal-carga-masiva');
                window.location.reload();
            }, 2000);
        } else {
            progressText.textContent = 'Error en el procesamiento';
            mostrarNotificacion(data.message || 'Error al procesar el archivo', 'error');
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Error:', error);
        progressText.textContent = 'Error en el procesamiento';
        mostrarNotificacion('Error al procesar la solicitud', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        textoSubmit.textContent = 'Procesar Archivo';
        loadingIcon.classList.add('hidden');
    });
}

// Función para configurar el buscador de vehículos
function configurarBuscadorVehiculos() {
    const searchInput = document.getElementById('vehiculo_search');
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    const sugerenciasList = document.getElementById('sugerencias_list');
    const noResults = document.getElementById('no_results');
    const vehiculos = window.vehiculosData || [];
    
    let timeoutId;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Limpiar timeout anterior
        clearTimeout(timeoutId);
        
        if (query.length === 0) {
            sugerenciasContainer.classList.add('hidden');
            return;
        }
        
        // Debounce para evitar búsquedas excesivas
        timeoutId = setTimeout(() => {
            buscarVehiculos(query, vehiculos, sugerenciasList, noResults, sugerenciasContainer);
        }, 150);
    });
    
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0) {
            sugerenciasContainer.classList.remove('hidden');
        }
    });
}

// Función para buscar vehículos
function buscarVehiculos(query, vehiculos, sugerenciasList, noResults, container) {
    const queryLower = query.toLowerCase();
    
    // Filtrar vehículos que coincidan con la búsqueda
    const resultados = vehiculos.filter(vehiculo => {
        const searchText = `${vehiculo.id} ${vehiculo.marca} ${vehiculo.modelo} ${vehiculo.placas}`.toLowerCase();
        return searchText.includes(queryLower);
    });
    
    // Limpiar lista anterior
    sugerenciasList.innerHTML = '';
    
    if (resultados.length === 0) {
        noResults.classList.remove('hidden');
        sugerenciasList.classList.add('hidden');
    } else {
        noResults.classList.add('hidden');
        sugerenciasList.classList.remove('hidden');
        
        // Mostrar máximo 8 resultados
        resultados.slice(0, 8).forEach((vehiculo, index) => {
            const item = crearItemSugerencia(vehiculo, query);
            sugerenciasList.appendChild(item);
        });
        
        if (resultados.length > 8) {
            const moreItem = document.createElement('div');
            moreItem.className = 'px-3 py-2 text-xs text-gray-500 border-t';
            moreItem.textContent = `+${resultados.length - 8} resultados más...`;
            sugerenciasList.appendChild(moreItem);
        }
    }
    
    container.classList.remove('hidden');
}

// Función para crear un item de sugerencia
function crearItemSugerencia(vehiculo, query) {
    const item = document.createElement('div');
    item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0';
    
    // Resaltar texto coincidente
    const texto = `ID: ${vehiculo.id} - ${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
    const textoResaltado = resaltarCoincidencias(texto, query);
    
    item.innerHTML = `
        <div class="flex justify-between items-center">
            <div>
                <div class="text-sm font-medium text-gray-900">${textoResaltado}</div>
                <div class="text-xs text-gray-500">KM actual: ${vehiculo.kilometraje_actual || 0}</div>
            </div>
        </div>
    `;
    
    item.addEventListener('click', () => {
        seleccionarVehiculo(vehiculo);
    });
    
    return item;
}

// Función para resaltar coincidencias
function resaltarCoincidencias(texto, query) {
    if (!query) return texto;
    
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\\\$&')})`, 'gi');
    return texto.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
}

// Función para seleccionar un vehículo
function seleccionarVehiculo(vehiculo) {
    const searchInput = document.getElementById('vehiculo_search');
    const vehiculoIdInput = document.getElementById('vehiculo_id');
    const vehiculoSeleccionado = document.getElementById('vehiculo_seleccionado');
    const vehiculoInfo = document.getElementById('vehiculo_info');
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    
    // Establecer valores
    searchInput.value = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
    vehiculoIdInput.value = vehiculo.id;
    
    // Mostrar información del vehículo seleccionado
    vehiculoInfo.innerHTML = `
        <strong>ID:</strong> ${vehiculo.id} | 
        <strong>Vehículo:</strong> ${vehiculo.marca} ${vehiculo.modelo} | 
        <strong>Placas:</strong> ${vehiculo.placas} | 
        <strong>KM actual:</strong> ${vehiculo.kilometraje_actual || 0}
    `;
    
    vehiculoSeleccionado.classList.remove('hidden');
    sugerenciasContainer.classList.add('hidden');
    
    // Actualizar validación de kilometraje
    validarKilometraje();
}

// Función para limpiar selección
function limpiarSeleccion() {
    const searchInput = document.getElementById('vehiculo_search');
    const vehiculoIdInput = document.getElementById('vehiculo_id');
    const vehiculoSeleccionado = document.getElementById('vehiculo_seleccionado');
    const errorDiv = document.getElementById('error-kilometraje');
    
    searchInput.value = '';
    vehiculoIdInput.value = '';
    vehiculoSeleccionado.classList.add('hidden');
    
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    
    searchInput.focus();
}

// Función para validar kilometraje
function validarKilometraje() {
    const vehiculoIdInput = document.getElementById('vehiculo_id');
    const kilometrajeInput = document.getElementById('kilometraje');
    const kmInfo = document.getElementById('km-info');
    
    if (vehiculoIdInput.value) {
        const vehiculos = window.vehiculosData || [];
        const vehiculo = vehiculos.find(v => v.id == vehiculoIdInput.value);
        
        if (vehiculo && vehiculo.kilometraje_actual) {
            const kmActual = parseInt(vehiculo.kilometraje_actual);
            const nuevoKm = parseInt(kilometrajeInput.value);
            
            kilometrajeInput.min = kmActual + 1;
            kmInfo.textContent = `El kilometraje debe ser mayor a ${kmActual.toLocaleString()} km (último registrado)`;
            kmInfo.classList.remove('hidden');
            
            // Validar valor actual
            if (nuevoKm && nuevoKm <= kmActual) {
                kilometrajeInput.setCustomValidity(`El kilometraje debe ser mayor a ${kmActual.toLocaleString()} km`);
                return false;
            } else {
                kilometrajeInput.setCustomValidity('');
                return true;
            }
        } else {
            kilometrajeInput.min = 1;
            kmInfo.classList.add('hidden');
            kilometrajeInput.setCustomValidity('');
            return true;
        }
    }
    return true;
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    
    if (tipo === 'success') {
        notificacion.classList.add('bg-green-500', 'text-white');
    } else if (tipo === 'error') {
        notificacion.classList.add('bg-red-500', 'text-white');
    } else {
        notificacion.classList.add('bg-blue-500', 'text-white');
    }
    
    notificacion.innerHTML = `
        <div class="flex items-center">
            <span class="flex-1">${mensaje}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notificacion);
    
    // Animar entrada
    setTimeout(() => {
        notificacion.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        notificacion.classList.add('translate-x-full');
        setTimeout(() => {
            if (notificacion.parentElement) {
                notificacion.remove();
            }
        }, 300);
    }, 5000);
}
</script>
@endpush
