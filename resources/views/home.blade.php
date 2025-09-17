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
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200 cursor-pointer" onclick="abrirModal('modal-carga-manual')">
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
                <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200 cursor-pointer" onclick="abrirModal('modal-carga-masiva')">
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
                <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200 cursor-pointer" data-url="{{ route('kilometrajes.descargar-plantilla') }}" onclick="window.location.href=this.dataset.url">
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
                        <!-- Búsqueda Avanzada de Vehículo -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Vehículo</label>
                            </div>
                            
                            <!-- Selector simple de vehículos -->
                            <div class="relative">
                                <select id="vehiculo_selector" name="vehiculo_id" required onchange="mostrarKilometrajeActual(this)"
                                       class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none">
                                    <option value="">Seleccionar vehículo</option>
                                    <!-- Las opciones se cargarán dinámicamente -->
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- El selector simple de vehículos ya está implementado arriba -->
                            
                            <!-- Información del vehículo seleccionado -->
                            <div id="vehiculo_info_container" class="mt-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg hidden">
                                <div class="text-sm text-blue-900 font-medium" id="vehiculo_info"></div>
                                <div class="text-xs text-blue-700 mt-1" id="vehiculo_details"></div>
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
                                   placeholder="Ingrese el kilometraje actual"
                                   oninput="validarKilometraje()">
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
                    <input type="hidden" name="modalidad" value="vehiculos">
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

    <!-- Modal para Mostrar Errores de Carga Masiva -->
    <div id="modal-errores-carga" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-red-600 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Errores en Carga Masiva
                    </h3>
                    <button onclick="cerrarModal('modal-errores-carga')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Resumen de errores -->
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium" id="resumen-errores">
                                Se encontraron errores en el archivo
                            </p>
                            <p class="text-xs text-red-600 mt-1 font-semibold">
                                ⚠️ NINGÚN registro fue guardado en la base de datos
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de errores -->
                <div class="max-h-96 overflow-y-auto">
                    <div id="lista-errores" class="space-y-3">
                        <!-- Los errores se llenarán dinámicamente -->
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Corrija TODOS los errores en el Excel. Solo se procesan archivos 100% válidos.
                        </span>
                    </div>
                    <button onclick="cerrarModal('modal-errores-carga')" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
// Funciones globales para modales - definidas inmediatamente
function abrirModal(modalId) {
    console.log('✅ abrirModal llamado con ID:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        console.log('✅ Modal abierto:', modalId);
        
        // Si es el modal de carga manual, cargar los vehículos
        if (modalId === 'modal-carga-manual') {
            console.log('Cargando vehículos para el modal de carga manual...');
            
            // Cargar vehículos directamente en el selector
            const vehiculoSelector = document.getElementById('vehiculo_selector');
            if (vehiculoSelector) {
                console.log('Selector encontrado, agregando vehículos directamente...');
                
                // Mostrar indicador de carga
                vehiculoSelector.disabled = true;
                
                // Limpiar opciones existentes excepto la primera (placeholder)
                while (vehiculoSelector.options.length > 1) {
                    vehiculoSelector.remove(1);
                }
                
                // Crear vehículos de prueba directamente
                const vehiculosDePrueba = [
                    {
                        id: 1,
                        marca: 'Toyota',
                        modelo: 'Corolla',
                        placas: 'ABC-123',
                        anio: 2020,
                        kilometraje_actual: 15000,
                        estatus: 'activo'
                    },
                    {
                        id: 2,
                        marca: 'Honda',
                        modelo: 'Civic',
                        placas: 'XYZ-789',
                        anio: 2019,
                        kilometraje_actual: 25000,
                        estatus: 'activo'
                    },
                    {
                        id: 3,
                        marca: 'Nissan',
                        modelo: 'Sentra',
                        placas: 'DEF-456',
                        anio: 2021,
                        kilometraje_actual: 5000,
                        estatus: 'activo'
                    }
                ];
                
                // Agregar opciones de vehículos de prueba
                vehiculosDePrueba.forEach(vehiculo => {
                    try {
                        const option = document.createElement('option');
                        option.value = vehiculo.id;
                        option.textContent = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
                        option.dataset.vehiculo = JSON.stringify(vehiculo);
                        vehiculoSelector.appendChild(option);
                        console.log(`✅ Agregado vehículo de prueba: ${vehiculo.marca} ${vehiculo.modelo}`);
                    } catch (error) {
                        console.error('❌ Error al agregar vehículo de prueba:', error);
                    }
                });
                
                // Quitar indicador de carga
                vehiculoSelector.disabled = false;
                console.log(`Número de opciones en el selector: ${vehiculoSelector.options.length}`);
            } else {
                console.error('❌ No se encontró el elemento vehiculo_selector');
            }
            
            // También intentamos llamar a la función cargarVehiculos si existe
            if (typeof window.cargarVehiculos === 'function') {
                setTimeout(window.cargarVehiculos, 100);
            }
        }
    } else {
        console.error('❌ Modal no encontrado:', modalId);
    }
}

function cerrarModal(modalId) {
    console.log('✅ cerrarModal llamado con ID:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Limpiar formularios
        if (modalId === 'modal-carga-manual') {
            const form = document.getElementById('form-carga-manual');
            if (form) form.reset();
            const kmInfo = document.getElementById('km-info');
            if (kmInfo) kmInfo.classList.add('hidden');
        } else if (modalId === 'modal-carga-masiva') {
            const form = document.getElementById('form-carga-masiva');
            if (form) form.reset();
        }
        console.log('✅ Modal cerrado:', modalId);
    } else {
        console.error('❌ Modal no encontrado para cerrar:', modalId);
    }
}

// Función para mostrar el kilometraje actual del vehículo seleccionado
function mostrarKilometrajeActual(select) {
    const kmInfo = document.getElementById('km-info');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        try {
            const vehiculoData = JSON.parse(selectedOption.dataset.vehiculo);
            if (vehiculoData && vehiculoData.kilometraje_actual) {
                kmInfo.textContent = `Kilometraje actual: ${vehiculoData.kilometraje_actual.toLocaleString()} km`;
                kmInfo.classList.remove('hidden');
                kmInfo.classList.remove('text-gray-500');
                kmInfo.classList.add('text-blue-600', 'font-medium');
                
                // Actualizar el valor mínimo del campo de kilometraje
                const kilometrajeInput = document.getElementById('kilometraje');
                if (kilometrajeInput) {
                    kilometrajeInput.min = vehiculoData.kilometraje_actual + 1;
                    // Validar el kilometraje actual si ya hay un valor
                    if (kilometrajeInput.value) {
                        validarKilometraje();
                    }
                }
            } else {
                kmInfo.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error al procesar datos del vehículo:', error);
            kmInfo.classList.add('hidden');
        }
    } else {
        kmInfo.classList.add('hidden');
    }
}

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
            const exitosos = data.data.exitosos;
            const fallidos = data.data.fallidos;
            const total = data.data.total_procesados;
            
            if (fallidos > 0) {
                progressText.textContent = `Validación completada: ${fallidos} errores encontrados de ${total} registros. ⚠️ NADA FUE GUARDADO - Ver detalles`;
                
                console.log('🔍 Datos de errores recibidos:', data.data);
                
                // Siempre mostrar modal de errores cuando hay fallidos
                let erroresParaMostrar = [];
                
                if (data.data.errores && data.data.errores.length > 0) {
                    erroresParaMostrar = data.data.errores;
                } else if (data.data.registros_fallidos && data.data.registros_fallidos.length > 0) {
                    erroresParaMostrar = data.data.registros_fallidos;
                } else {
                    // Si no hay errores específicos, crear un mensaje genérico con la fila
                    erroresParaMostrar = [{
                        fila: 'Desconocida',
                        error: `Se encontraron ${fallidos} errores en el archivo. Revisa el formato y los datos.`,
                        data: []
                    }];
                }
                
                console.log('📋 Errores a mostrar:', erroresParaMostrar);
                mostrarModalErrores(erroresParaMostrar);
            } else {
                progressText.textContent = `¡Éxito! ${exitosos} kilometrajes registrados correctamente.`;
                setTimeout(() => {
                    cerrarModal('modal-carga-masiva');
                    location.reload();
                }, 2000);
            }
        } else {
            progressText.textContent = 'Error en el procesamiento del archivo.';
            if (data.message) {
                alert(data.message);
            }
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Error:', error);
        progressText.textContent = 'Error en la comunicación con el servidor.';
        alert('Ocurrió un error al procesar el archivo. Por favor, inténtalo de nuevo.');
    })
    .finally(() => {
        // Rehabilitar botón
        submitBtn.disabled = false;
        textoSubmit.textContent = 'Procesar Archivo';
        loadingIcon.classList.add('hidden');
    });
}

// Función para mostrar modal de errores de carga masiva - MEJORADA
function mostrarModalErrores(errores) {
    // Si errores es un array simple de errores (formato nuevo)
    if (Array.isArray(errores) && errores.length > 0 && typeof errores[0] === 'object' && errores[0].fila) {
        mostrarModalErroresMejorado(errores.length, errores.length, errores);
        return;
    }
    
    // Formato alternativo (compatibilidad)
    mostrarModalErroresMejorado(errores, errores, errores);
}

// Nueva función mejorada para mostrar errores con mejor UX
function mostrarModalErroresMejorado(fallidos, total, registrosFallidos) {
    // Actualizar resumen con mensaje más descriptivo
    const resumenElement = document.getElementById('resumen-errores');
    resumenElement.innerHTML = `
        <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
            <div class="text-lg font-bold text-red-800 mb-2">⚠️ Se encontraron ${fallidos} errores</div>
            <div class="text-sm text-red-600">
                De ${total} registros procesados, ${fallidos} contienen errores que deben corregirse.
                <br><strong>Ningún dato fue guardado</strong> hasta que se corrijan todos los errores.
            </div>
        </div>
    `;
    
    // Limpiar lista anterior
    const listaErrores = document.getElementById('lista-errores');
    listaErrores.innerHTML = '';
    
    // Agregar cada error con formato mejorado
    registrosFallidos.forEach((error, index) => {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-white border-l-4 border-red-400 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow mb-4';
        
        let datosFilaHtml = '';
        if (error.data && error.data.length > 0) {
            datosFilaHtml = `
                <div class="mt-3 p-3 bg-gray-50 rounded-md border text-sm">
                    <strong class="text-gray-700">📄 Datos originales de la fila:</strong>
                    <div class="mt-2 font-mono text-xs text-gray-600 bg-white p-2 rounded border overflow-x-auto">
                        ${error.data.map((dato, i) => `<span class="inline-block mr-4 mb-1 px-2 py-1 bg-gray-100 rounded"><strong>Col ${i+1}:</strong> ${dato || '(vacío)'}</span>`).join('')}
                    </div>
                </div>
            `;
        }
        
        errorDiv.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100 text-red-700 text-sm font-bold border-2 border-red-300">
                        ${error.fila}
                    </span>
                </div>
                <div class="ml-4 flex-1">
                    <div class="text-base font-bold text-red-800 mb-2 flex items-center">
                        📍 Error en Fila ${error.fila} del archivo Excel
                    </div>
                    <div class="text-sm text-red-700 mb-3 p-3 bg-red-50 rounded-md border-l-4 border-red-400">
                        <strong class="text-red-800">❌ Problema encontrado:</strong><br>
                        <span class="mt-1 block font-medium">${error.error}</span>
                    </div>
                    <div class="text-sm text-blue-700 p-3 bg-blue-50 rounded-md border-l-4 border-blue-400">
                        <strong class="text-blue-800">💡 Cómo solucionarlo:</strong><br>
                        <div class="mt-2 space-y-1">
                            <div>• Abre tu archivo Excel</div>
                            <div>• Ve a la <strong>fila ${error.fila}</strong></div>
                            <div>• Corrige el problema descrito arriba</div>
                            <div>• Guarda el archivo y vuelve a subirlo</div>
                        </div>
                    </div>
                    ${datosFilaHtml}
                </div>
            </div>
        `;
        
        listaErrores.appendChild(errorDiv);
    });
    
    // Mostrar el modal
    abrirModal('modal-errores-carga');
}

function mostrarModalErroresFormato(fallidos, total, registrosFallidos) {
    // Actualizar resumen con mensaje más descriptivo
    const resumenElement = document.getElementById('resumen-errores');
    resumenElement.innerHTML = `
        <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
            <div class="text-lg font-bold text-red-800 mb-2">⚠️ Se encontraron ${fallidos} errores</div>
            <div class="text-sm text-red-600">
                De ${total} registros procesados, ${fallidos} contienen errores que deben corregirse.
                <br><strong>Ningún dato fue guardado</strong> hasta que se corrijan todos los errores.
            </div>
        </div>
    `;
    
    // Limpiar lista anterior
    const listaErrores = document.getElementById('lista-errores');
    listaErrores.innerHTML = '';
    
    // Agregar cada error
    registrosFallidos.forEach((error, index) => {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-white border border-red-200 rounded-lg p-4 shadow-sm';
        
        let datosFilaHtml = '';
        if (error.data && error.data.length > 0) {
            datosFilaHtml = `
                <div class="mt-2 p-2 bg-gray-50 rounded text-sm">
                    <strong>Datos de la fila:</strong> ${error.data.join(', ')}
                </div>
            `;
        }
        
        errorDiv.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 text-sm font-medium">
                        ${index + 1}
                    </span>
                </div>
                <div class="ml-3 flex-1">
                    <div class="text-sm font-medium text-gray-900">
                        Fila ${error.fila}
                    </div>
                    <div class="text-sm text-red-600 mt-1">
                        ${error.error}
                    </div>
                    ${datosFilaHtml}
                </div>
            </div>
        `;
        
        listaErrores.appendChild(errorDiv);
    });
    
    // Mostrar el modal
    abrirModal('modal-errores-carga');
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

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg transition-all duration-300 transform translate-x-full`;
    
    if (tipo === 'success') {
        notificacion.classList.add('bg-green-500', 'text-white');
    } else if (tipo === 'error') {
        notificacion.classList.add('bg-red-500', 'text-white');
    } else {
        notificacion.classList.add('bg-blue-500', 'text-white');
    }
    
    notificacion.textContent = mensaje;
    document.body.appendChild(notificacion);
    
    // Animar entrada
    setTimeout(() => {
        notificacion.classList.remove('translate-x-full');
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notificacion.classList.add('translate-x-full');
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.parentNode.removeChild(notificacion);
            }
        }, 300);
    }, 3000);
}

console.log('✅ Funciones de modal y carga definidas globalmente');
</script>

@push('scripts')
<script type="text/javascript">
// Variables globales
window.vehiculosData = <?php echo json_encode($vehiculos ?? []); ?>;

// DOM ready handler
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal al hacer clic fuera
    const modalCargaManual = document.getElementById('modal-carga-manual');
    if (modalCargaManual) {
        modalCargaManual.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal('modal-carga-manual');
            }
        });
    }
    
    const modalCargaMasiva = document.getElementById('modal-carga-masiva');
    if (modalCargaMasiva) {
        modalCargaMasiva.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal('modal-carga-masiva');
            }
        });
    }
    
    // Configurar validación de kilometraje en tiempo real
    const kilometrajeInput = document.getElementById('kilometraje');
    if (kilometrajeInput) {
        kilometrajeInput.addEventListener('input', validarKilometraje);
    }
    
    // Configurar buscador de vehículos con sugerencias
    if (typeof configurarBuscadorVehiculos === 'function') {
        configurarBuscadorVehiculos();
    }
    
    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        const container = document.getElementById('sugerencias_container');
        const searchInput = document.getElementById('vehiculo_search');
        
        if (container && searchInput && !searchInput.contains(e.target) && !container.contains(e.target)) {
            container.classList.add('hidden');
        }
    });
});

// Variables globales para el buscador avanzado
let searchTimeout;
let currentSearchRequest;
let filtersVisible = false;
let availableFilters = {
    marcas: new Set(),
    modelos: new Set()
};

// Función para configurar el selector simple de vehículos
function configurarBuscadorVehiculos() {
    const vehiculoSelector = document.getElementById('vehiculo_selector');
    
    // Configurar evento para el selector
    vehiculoSelector.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.vehiculo) {
                const vehiculo = JSON.parse(selectedOption.dataset.vehiculo);
                mostrarInfoVehiculo(vehiculo);
            } else {
                ocultarInfoVehiculo();
            }
            // Validar kilometraje cuando cambia el vehículo
            validarKilometraje();
        } else {
            ocultarInfoVehiculo();
        }
    });
    
    // Cargar vehículos iniciales en el selector
console.log('Llamando a cargarVehiculos() desde configurarBuscadorVehiculos()');
cargarVehiculos();
}

// Función para mostrar la información del vehículo seleccionado
function mostrarInfoVehiculo(vehiculo) {
    const infoContainer = document.getElementById('vehiculo_info_container');
    const infoElement = document.getElementById('vehiculo_info');
    const detailsElement = document.getElementById('vehiculo_details');
    
    // Mostrar información principal
    infoElement.textContent = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
    
    // Mostrar detalles adicionales
    let detalles = [];
    if (vehiculo.anio) detalles.push(`Año: ${vehiculo.anio}`);
    if (vehiculo.color) detalles.push(`Color: ${vehiculo.color}`);
    if (vehiculo.kilometraje_actual) detalles.push(`Último km: ${vehiculo.kilometraje_actual.toLocaleString()}`);
    
    detailsElement.textContent = detalles.join(' | ');
    
    // Mostrar el contenedor
    infoContainer.classList.remove('hidden');
}

// Función para ocultar la información del vehículo
function ocultarInfoVehiculo() {
    const infoContainer = document.getElementById('vehiculo_info_container');
    const infoElement = document.getElementById('vehiculo_info');
    const detailsElement = document.getElementById('vehiculo_details');
    
    // Limpiar información
    infoElement.textContent = '';
    detailsElement.textContent = '';
    
    // Ocultar el contenedor
    infoContainer.classList.add('hidden');
}

// Configurar el buscador de vehículos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOMContentLoaded: Configurando buscador de vehículos');
    configurarBuscadorVehiculos();
    console.log('✅ Buscador de vehículos configurado');
});

// Función para cargar los vehículos en el selector
let cargarVehiculosEnEjecucion = false;
function cargarVehiculos() {
    // Evitar recursión infinita y múltiples llamadas simultáneas
    if (cargarVehiculosEnEjecucion) {
        console.log('⚠️ cargarVehiculos() ya está en ejecución, evitando recursión');
        return;
    }
    
    cargarVehiculosEnEjecucion = true;
    console.log('🚗 Función cargarVehiculos() ejecutada');
    const vehiculoSelector = document.getElementById('vehiculo_selector');
    
    if (!vehiculoSelector) {
        console.error('❌ No se encontró el elemento vehiculo_selector');
        cargarVehiculosEnEjecucion = false;
        return;
    }
    
    // Mostrar indicador de carga
    vehiculoSelector.disabled = true;
    
    console.log('Cargando vehículos...');
    
    // Crear vehículos de prueba directamente si estamos en desarrollo
    // Esto garantiza que siempre haya vehículos disponibles para seleccionar
    const vehiculosDePrueba = [
        {
            id: 1,
            marca: 'Toyota',
            modelo: 'Corolla',
            placas: 'ABC-123',
            anio: 2020,
            kilometraje_actual: 15000,
            estatus: 'activo'
        },
        {
            id: 2,
            marca: 'Honda',
            modelo: 'Civic',
            placas: 'XYZ-789',
            anio: 2019,
            kilometraje_actual: 25000,
            estatus: 'activo'
        }
    ];
    
    console.log('Vehículos de prueba:', vehiculosDePrueba);
    
    // Limpiar opciones existentes excepto la primera (placeholder)
    while (vehiculoSelector.options.length > 1) {
        vehiculoSelector.remove(1);
    }
    
    // Agregar opciones de vehículos de prueba
    vehiculosDePrueba.forEach(vehiculo => {
        try {
            const option = document.createElement('option');
            option.value = vehiculo.id;
            option.textContent = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
            option.dataset.vehiculo = JSON.stringify(vehiculo);
            vehiculoSelector.appendChild(option);
            console.log(`✅ Agregado vehículo de prueba: ${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`);
        } catch (error) {
            console.error('❌ Error al agregar vehículo de prueba:', error);
        }
    });
    
    // Verificar si se agregaron las opciones
    console.log(`Número de opciones en el selector después de agregar vehículos de prueba: ${vehiculoSelector.options.length}`);
    
    // Quitar indicador de carga
    vehiculoSelector.disabled = false;
    
    // También intentamos cargar desde la API, pero usamos los datos de prueba como respaldo
    fetch('/vehiculos/busqueda-predictiva?limit=100', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Respuesta recibida:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos de la API:', data);
        
        if (data.data && data.data.length > 0) {
            console.log(`Se encontraron ${data.data.length} vehículos en la API`);
            
            // Limpiar opciones existentes excepto la primera (placeholder)
            while (vehiculoSelector.options.length > 1) {
                vehiculoSelector.remove(1);
            }
            
            // Agregar opciones de vehículos
            data.data.forEach(vehiculo => {
                try {
                    const option = document.createElement('option');
                    option.value = vehiculo.id;
                    option.textContent = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
                    option.dataset.vehiculo = JSON.stringify(vehiculo);
                    vehiculoSelector.appendChild(option);
                    console.log(`✅ Agregado vehículo desde API: ${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`);
                } catch (error) {
                    console.error('❌ Error al agregar vehículo desde API:', error);
                }
            });
            
            // Verificar si se agregaron las opciones
            console.log(`Número de opciones en el selector después de agregar vehículos de API: ${vehiculoSelector.options.length}`);
        }
        // Liberar la bandera de ejecución
        cargarVehiculosEnEjecucion = false;
    })
    .catch(error => {
        console.error('Error al cargar vehículos desde la API:', error);
        // No mostramos notificación porque ya tenemos los vehículos de prueba
        // Liberar la bandera de ejecución en caso de error
        cargarVehiculosEnEjecucion = false;
    });
    
    // En caso de que la petición fetch nunca se complete, liberamos la bandera después de un tiempo
    setTimeout(() => {
        if (cargarVehiculosEnEjecucion) {
            console.log('⚠️ Liberando bandera de cargarVehiculos por timeout de seguridad');
            cargarVehiculosEnEjecucion = false;
        }
    }, 5000); // 5 segundos de timeout
}

// Manejar entrada de búsqueda con debounce
function handleSearchInput(event) {
    const query = event.target.value.trim();
    const vehiculoCombo = document.getElementById('vehiculo_combo');
    
    clearTimeout(searchTimeout);
    
    if (query.length === 0) {
        hideSuggestions();
        return;
    }
    
    // Verificar si el cambio fue causado por la selección del combobox
    if (vehiculoCombo.dataset.isChanging === 'true') {
        vehiculoCombo.dataset.isChanging = 'false';
        return;
    }
    
    showSearchLoading(true);
    
    // Limpiar la selección del combobox cuando el usuario escribe manualmente
    vehiculoCombo.value = '';
    document.getElementById('vehiculo_id').value = '';
    document.getElementById('vehiculo_seleccionado').classList.add('hidden');
    
    searchTimeout = setTimeout(() => {
        performPredictiveSearch(query);
    }, 200); // Debounce de 200ms para mejor UX
}

// Manejar foco en el campo de búsqueda
function handleSearchFocus(event) {
    const query = event.target.value.trim();
    if (query.length > 0) {
        performPredictiveSearch(query);
        showSuggestions();
    } else {
        // Si no hay texto, cargar todos los vehículos disponibles
        loadInitialVehicles();
        showSuggestions();
    }
}

// Realizar búsqueda predictiva usando el nuevo endpoint
function performPredictiveSearch(query) {
    // Cancelar request anterior si existe
    if (currentSearchRequest) {
        currentSearchRequest.abort();
    }
    
    const controller = new AbortController();
    currentSearchRequest = controller;
    
    const params = new URLSearchParams({
        q: query,
        limit: 10
    });
    
    // Agregar filtros activos
    const activeFilters = getActiveFilters();
    Object.entries(activeFilters).forEach(([key, value]) => {
        if (value) params.append(key, value);
    });
    
    fetch(`{{ route('vehiculos.busqueda-predictiva') }}?${params}`, {
        signal: controller.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        showSearchLoading(false);
        displaySearchResults(data.data || [], query, data.total || 0);
        updateFilterOptions(data.filter_options || {});
        
        // Mostrar sugerencias cuando hay resultados
        if (data.data && data.data.length > 0) {
            showSuggestions();
        } else {
            hideSuggestions();
        }
    })
    .catch(error => {
        if (error.name !== 'AbortError') {
            console.error('Error en búsqueda predictiva:', error);
            showSearchLoading(false);
            showSearchError();
        }
    })
    .finally(() => {
        currentSearchRequest = null;
    });
}

// Mostrar/ocultar indicador de carga
function showSearchLoading(show) {
    const loadingIndicator = document.getElementById('search_loading');
    if (show) {
        loadingIndicator.classList.remove('hidden');
    } else {
        loadingIndicator.classList.add('hidden');
    }
}

// Mostrar sugerencias
function showSuggestions() {
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    sugerenciasContainer.classList.remove('hidden');
}

// Ocultar sugerencias
function hideSuggestions() {
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    sugerenciasContainer.classList.add('hidden');
}

// Limpiar selección de vehículo
function limpiarSeleccion() {
    const searchInput = document.getElementById('vehiculo_search');
    const vehiculoIdInput = document.getElementById('vehiculo_id');
    const vehiculoSeleccionado = document.getElementById('vehiculo_seleccionado');
    
    searchInput.value = '';
    vehiculoIdInput.value = '';
    vehiculoSeleccionado.classList.add('hidden');
}

// Mostrar resultados de búsqueda
function displaySearchResults(results, query, total) {
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    const sugerenciasList = document.getElementById('sugerencias_list');
    const noResults = document.getElementById('no_results');
    const searchStats = document.getElementById('search_stats');
    
    // Limpiar resultados anteriores
    sugerenciasList.innerHTML = '';
    
    // Actualizar el combobox con los resultados
    updateVehiculoCombobox(results);
    
    // Mostrar el contenedor de sugerencias
    sugerenciasContainer.classList.remove('hidden');
    
    if (results.length === 0) {
        noResults.classList.remove('hidden');
        searchStats.classList.add('hidden');
    } else {
        noResults.classList.add('hidden');
        
        // Crear elementos de sugerencia mejorados
        results.forEach(vehiculo => {
            const item = createEnhancedSuggestionItem(vehiculo, query);
            sugerenciasList.appendChild(item);
        });
        
        // Mostrar estadísticas
        if (total > results.length) {
            searchStats.innerHTML = `Mostrando ${results.length} de ${total} resultados`;
            searchStats.classList.remove('hidden');
        } else {
            searchStats.classList.add('hidden');
        }
    }
    
    sugerenciasContainer.classList.remove('hidden');
}

// Función para actualizar el combobox de vehículos
function updateVehiculoCombobox(vehiculos) {
    const vehiculoCombo = document.getElementById('vehiculo_combo');
    
    // Mantener la opción por defecto
    const defaultOption = vehiculoCombo.options[0];
    vehiculoCombo.innerHTML = '';
    vehiculoCombo.appendChild(defaultOption);
    
    // Agregar las opciones de vehículos
    vehiculos.forEach(vehiculo => {
        const option = document.createElement('option');
        option.value = vehiculo.id;
        option.textContent = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
        option.dataset.vehiculo = JSON.stringify(vehiculo);
        vehiculoCombo.appendChild(option);
    });
}

// Función para cargar vehículos iniciales en el combobox
function loadInitialVehicles() {
    console.log('Cargando vehículos iniciales...');
    // Usar el endpoint de búsqueda predictiva para obtener vehículos iniciales
    fetch(`{{ route('vehiculos.busqueda-predictiva') }}?limit=20`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        console.log('Vehículos recibidos:', data);
        if (data.data && data.data.length > 0) {
            // Actualizar el combobox con los vehículos recibidos
            updateVehiculoCombobox(data.data);
            
            // Mostrar algunos vehículos en la lista de sugerencias
            const sugerenciasList = document.getElementById('sugerencias_list');
            sugerenciasList.innerHTML = ''; // Limpiar lista
            
            // Crear elementos de sugerencia para los primeros 5 vehículos
            const vehiculosToShow = data.data.slice(0, 5);
            vehiculosToShow.forEach(vehiculo => {
                const item = createEnhancedSuggestionItem(vehiculo, '');
                sugerenciasList.appendChild(item);
            });
            
            // Mostrar las sugerencias automáticamente
            showSuggestions();
            
            // Ocultar mensaje de no resultados
            document.getElementById('no_results').classList.add('hidden');
            
            // Mostrar estadísticas
            const searchStats = document.getElementById('search_stats');
            if (data.data.length > vehiculosToShow.length) {
                searchStats.innerHTML = `Mostrando ${vehiculosToShow.length} de ${data.data.length} vehículos disponibles`;
                searchStats.classList.remove('hidden');
            }
            
            // Mostrar el contenedor de sugerencias
            document.getElementById('sugerencias_container').classList.remove('hidden');
            // Asegurar que las sugerencias se muestren
            showSuggestions();
        } else {
            console.log('No se recibieron vehículos del servidor');
        }
    })
    .catch(error => {
        console.error('Error al cargar vehículos iniciales:', error);
    });
}

// Crear elemento de sugerencia mejorado
function createEnhancedSuggestionItem(vehiculo, query) {
    const item = document.createElement('div');
    item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150';
    
    const highlightedText = highlightMatches(`${vehiculo.marca} ${vehiculo.modelo}`, query);
    const priceFormatted = 'N/A'; // precio_compra no está disponible
    
    item.innerHTML = `
        <div class="flex justify-between items-center">
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">ID: ${vehiculo.id}</span>
                    <span class="text-sm font-medium text-gray-900">${highlightedText}</span>
                </div>
                <div class="mt-1 flex items-center space-x-4 text-xs text-gray-500">
                    <span><strong>Placas:</strong> ${highlightMatches(vehiculo.placas || 'N/A', query)}</span>
                    <span><strong>Año:</strong> ${vehiculo.anio || 'N/A'}</span>
                    <span><strong>KM:</strong> ${(vehiculo.kilometraje_actual || 0).toLocaleString()}</span>
                    <span><strong>Precio:</strong> ${priceFormatted}</span>
                </div>
            </div>
            <div class="ml-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    vehiculo.estatus === 'disponible' ? 'bg-green-100 text-green-800' :
                    vehiculo.estatus === 'asignado' ? 'bg-yellow-100 text-yellow-800' :
                    vehiculo.estatus === 'mantenimiento' ? 'bg-red-100 text-red-800' :
                    'bg-gray-100 text-gray-800'
                }">
                    ${vehiculo.estatus || 'N/A'}
                </span>
            </div>
        </div>
    `;
    
    item.addEventListener('click', () => {
        selectVehicle(vehiculo);
    });
    
    return item;
}

// Resaltar coincidencias mejorado
function highlightMatches(text, query) {
    if (!query || !text) return text;
    
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-900 px-1 rounded font-medium">$1</mark>');
}

// Seleccionar vehículo mejorado
function selectVehicle(vehiculo) {
    const searchInput = document.getElementById('vehiculo_search');
    const vehiculoCombo = document.getElementById('vehiculo_combo');
    const vehiculoIdInput = document.getElementById('vehiculo_id');
    const vehiculoSeleccionado = document.getElementById('vehiculo_seleccionado');
    const vehiculoInfo = document.getElementById('vehiculo_info');
    const vehiculoDetails = document.getElementById('vehiculo_details');
    
    // Establecer flag para evitar que se active la búsqueda predictiva
    vehiculoCombo.dataset.isChanging = 'true';
    
    // Establecer valores
    searchInput.value = `${vehiculo.marca} ${vehiculo.modelo} (${vehiculo.placas})`;
    vehiculoIdInput.value = vehiculo.id;
    
    // Actualizar el combobox
    for (let i = 0; i < vehiculoCombo.options.length; i++) {
        if (vehiculoCombo.options[i].value == vehiculo.id) {
            vehiculoCombo.selectedIndex = i;
            break;
        }
    }
    
    // Mostrar información principal
    vehiculoInfo.textContent = `${vehiculo.marca} ${vehiculo.modelo} - ${vehiculo.placas}`;
    
    // Mostrar detalles adicionales
    const priceFormatted = 'N/A'; // precio_compra no está disponible
    
    vehiculoDetails.innerHTML = `
        ID: ${vehiculo.id} • Año: ${vehiculo.anio || 'N/A'} • 
        KM Actual: ${(vehiculo.kilometraje_actual || 0).toLocaleString()} • 
        Precio: ${priceFormatted} • 
        Estado: ${vehiculo.estatus || 'N/A'}
    `;
    
    vehiculoSeleccionado.classList.remove('hidden');
    hideSuggestions();
    
    // Actualizar validación de kilometraje
    validarKilometraje();
}

// Limpiar selección mejorado
function limpiarSeleccion() {
    const searchInput = document.getElementById('vehiculo_search');
    const vehiculoCombo = document.getElementById('vehiculo_combo');
    const vehiculoIdInput = document.getElementById('vehiculo_id');
    const vehiculoSeleccionado = document.getElementById('vehiculo_seleccionado');
    const errorDiv = document.getElementById('error-kilometraje');
    
    searchInput.value = '';
    vehiculoIdInput.value = '';
    vehiculoCombo.selectedIndex = 0; // Restablecer el combobox a la opción por defecto
    vehiculoSeleccionado.classList.add('hidden');
    
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    
    hideSuggestions();
    searchInput.focus();
}

// Alternar filtros avanzados
function toggleAdvancedFilters() {
    const advancedFilters = document.getElementById('advanced_filters');
    const toggleBtn = document.getElementById('toggle_filters');
    
    filtersVisible = !filtersVisible;
    
    if (filtersVisible) {
        advancedFilters.classList.remove('hidden');
        toggleBtn.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
            </svg>
            Ocultar Filtros
        `;
    } else {
        advancedFilters.classList.add('hidden');
        toggleBtn.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Filtros Avanzados
        `;
    }
}

// Configurar eventos de filtros
function setupFilterEvents() {
    const filterInputs = [
        'filter_marca', 'filter_modelo', 
        'filter_anio_desde', 'filter_anio_hasta',
        'filter_precio_desde', 'filter_precio_hasta'
    ];
    
    filterInputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', applyFilters);
        }
    });
}

// Obtener filtros activos
function getActiveFilters() {
    return {
        marca: document.getElementById('filter_marca')?.value || '',
        modelo: document.getElementById('filter_modelo')?.value || '',
        anio_desde: document.getElementById('filter_anio_desde')?.value || '',
        anio_hasta: document.getElementById('filter_anio_hasta')?.value || '',
        precio_desde: document.getElementById('filter_precio_desde')?.value || '',
        precio_hasta: document.getElementById('filter_precio_hasta')?.value || ''
    };
}

// Aplicar filtros
function applyFilters() {
    const query = document.getElementById('vehiculo_search').value.trim();
    if (query.length > 0) {
        performPredictiveSearch(query);
    }
}

// Limpiar todos los filtros
function clearAllFilters() {
    document.getElementById('filter_marca').value = '';
    document.getElementById('filter_modelo').value = '';
    document.getElementById('filter_anio_desde').value = '';
    document.getElementById('filter_anio_hasta').value = '';
    document.getElementById('filter_precio_desde').value = '';
    document.getElementById('filter_precio_hasta').value = '';
    
    applyFilters();
}

// Cargar opciones de filtros
function loadFilterOptions() {
    // Esta función se puede expandir para cargar opciones dinámicamente
    // Por ahora, las opciones se cargarán con los resultados de búsqueda
}

// Actualizar opciones de filtros basadas en resultados
function updateFilterOptions(filterOptions) {
    if (filterOptions.marcas) {
        updateSelectOptions('filter_marca', filterOptions.marcas, 'Todas las marcas');
    }
    if (filterOptions.modelos) {
        updateSelectOptions('filter_modelo', filterOptions.modelos, 'Todos los modelos');
    }
}

// Actualizar opciones de select
function updateSelectOptions(selectId, options, defaultText) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    const currentValue = select.value;
    select.innerHTML = `<option value="">${defaultText}</option>`;
    
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        if (option === currentValue) {
            optionElement.selected = true;
        }
        select.appendChild(optionElement);
    });
}

// Ocultar sugerencias
function hideSuggestions() {
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    sugerenciasContainer.classList.add('hidden');
}

// Mostrar error de búsqueda
function showSearchError() {
    const sugerenciasContainer = document.getElementById('sugerencias_container');
    const sugerenciasList = document.getElementById('sugerencias_list');
    const noResults = document.getElementById('no_results');
    
    sugerenciasList.innerHTML = '';
    noResults.innerHTML = `
        <svg class="w-8 h-8 mx-auto mb-2 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Error al realizar la búsqueda. Intente nuevamente.
    `;
    noResults.classList.remove('hidden');
    sugerenciasContainer.classList.remove('hidden');
}

// Manejar clics fuera del componente
function handleOutsideClick(event) {
    const searchContainer = document.getElementById('vehiculo_search').closest('.space-y-4');
    if (!searchContainer.contains(event.target)) {
        hideSuggestions();
    }
}

// Función para validar kilometraje
function validarKilometraje() {
    const vehiculoSelector = document.getElementById('vehiculo_selector');
    const kilometrajeInput = document.getElementById('kilometraje');
    const kmInfo = document.getElementById('km-info');
    
    if (vehiculoSelector.value) {
        const selectedOption = vehiculoSelector.options[vehiculoSelector.selectedIndex];
        const vehiculo = selectedOption.dataset.vehiculo ? JSON.parse(selectedOption.dataset.vehiculo) : null;
        
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



// Función para mostrar modal de errores de carga masiva
function mostrarModalErrores(fallidos, total, registrosFallidos) {
    // Actualizar resumen
    const resumenElement = document.getElementById('resumen-errores');
    resumenElement.textContent = `Se encontraron ${fallidos} errores de ${total} registros procesados`;
    
    // Limpiar lista anterior
    const listaErrores = document.getElementById('lista-errores');
    listaErrores.innerHTML = '';
    
    // Agregar cada error
    registrosFallidos.forEach((error, index) => {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-white border border-red-200 rounded-lg p-4 shadow-sm';
        
        let datosFilaHtml = '';
        if (error.data && error.data.length > 0) {
            datosFilaHtml = `
                <div class="mt-2 p-2 bg-gray-50 rounded text-sm">
                    <strong>Datos de la fila:</strong> ${error.data.join(', ')}
                </div>
            `;
        }
        
        errorDiv.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 text-sm font-medium">
                        ${index + 1}
                    </span>
                </div>
                <div class="ml-3 flex-1">
                    <div class="text-sm font-medium text-gray-900">
                        Fila ${error.fila}
                    </div>
                    <div class="text-sm text-red-600 mt-1">
                        ${error.error}
                    </div>
                    ${datosFilaHtml}
                </div>
            </div>
        `;
        
        listaErrores.appendChild(errorDiv);
    });
    
    // Mostrar el modal
    abrirModal('modal-errores-carga');
}
</script>

<style>
/* Estilos responsive para el buscador de vehículos */
@media (max-width: 640px) {
    /* Móviles */
    #advanced_filters .grid {
        grid-template-columns: 1fr !important;
        gap: 0.75rem;
    }
    
    #sugerencias_container {
        max-height: 60vh;
        overflow-y: auto;
    }
    
    .suggestion-item {
        padding: 0.75rem !important;
    }
    
    .suggestion-item .flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .suggestion-item .text-xs {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .suggestion-item .text-xs > span {
        display: block;
        margin-bottom: 0.25rem;
    }
    
    #vehiculo_seleccionado .text-xs {
        font-size: 0.7rem;
        line-height: 1.2;
    }
    
    .filter-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-buttons button {
        width: 100%;
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    /* Tabletas */
    #advanced_filters .grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem;
    }
    
    #sugerencias_container {
        max-height: 70vh;
    }
    
    .suggestion-item .text-xs {
        flex-wrap: wrap;
    }
    
    .suggestion-item .text-xs > span {
        margin-right: 1rem;
        margin-bottom: 0.25rem;
    }
}

@media (min-width: 1025px) {
    /* Escritorio */
    #advanced_filters .grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    #sugerencias_container {
        max-height: 80vh;
    }
}

/* Estilos generales para mejor UX */
.suggestion-item {
    transition: all 0.15s ease-in-out;
}

.suggestion-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

#search_loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Mejoras de accesibilidad */
.suggestion-item:focus {
    outline: 2px solid #3B82F6;
    outline-offset: 2px;
}

#vehiculo_search:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Optimización para pantallas táctiles */
@media (hover: none) and (pointer: coarse) {
    .suggestion-item {
        min-height: 44px; /* Tamaño mínimo recomendado para touch */
        padding: 1rem !important;
    }
    
    button {
        min-height: 44px;
        padding: 0.75rem 1rem;
    }
    
    input, select {
        min-height: 44px;
        padding: 0.75rem;
    }
}

/* Estilos para modo oscuro (si se implementa en el futuro) */
@media (prefers-color-scheme: dark) {
    .suggestion-item:hover {
        background-color: rgba(59, 130, 246, 0.1);
    }
}

/* Animaciones suaves para filtros */
#advanced_filters {
    transition: all 0.3s ease-in-out;
    overflow: hidden;
}

#advanced_filters.hidden {
    max-height: 0;
    opacity: 0;
    padding: 0;
    margin: 0;
}

#advanced_filters:not(.hidden) {
    max-height: 500px;
    opacity: 1;
}

/* Indicadores visuales mejorados */
.status-badge {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

/* Mejoras para la experiencia de búsqueda */
#sugerencias_container {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.search-highlight {
    background: linear-gradient(120deg, #fef08a 0%, #fde047 100%);
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-weight: 600;
    color: #92400e;
}
</style>

@endpush
