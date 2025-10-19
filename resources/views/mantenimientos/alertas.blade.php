@extends('layouts.app')

@section('title', 'Alertas de Mantenimiento')
@section('header', 'Alertas de Mantenimiento')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 flex items-center mb-2">
            <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20h4c0 1.1-.9 2-2 2s-2-.9-2-2zm10-2.65V19H4v-1.65l2-1.88v-5.15C6 7.4 7.56 5.1 10 4.34v-.38c0-1.42 1.49-2.5 2.99-1.76.65.32 1.01 1.03 1.01 1.76v.39c2.44.75 4 3.06 4 5.98v5.15l2 1.88z"/>
            </svg>
            Alertas de Mantenimiento
        </h1>
        <p class="text-gray-600">Activos que requieren mantenimiento preventivo basado en intervalos configurados</p>
    </div>
    <div class="flex space-x-2 mt-4 sm:mt-0">
        <a href="{{ route('mantenimientos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Mantenimientos
        </a>
        @can('crear_mantenimientos')
        <a href="{{ route('mantenimientos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Mantenimiento
        </a>
        @endcan
    </div>
</div>

@if(count($alertas) > 0)
<!-- Resumen de Alertas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $criticas = collect($alertas)->where('urgencia', 'critica')->count();
        $altas = collect($alertas)->where('urgencia', 'alta')->count();
        $normales = collect($alertas)->where('urgencia', 'normal')->count();
        $totalVehiculos = collect($alertas)->pluck('vehiculo_id')->unique()->count();
    @endphp
    
    <!-- Alertas Críticas -->
    <div class="bg-white border border-red-300 rounded-lg overflow-hidden">
        <div class="bg-red-50 px-4 py-3 border-b border-red-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Críticas</h3>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-red-600">{{ $criticas }}</p>
                <p class="ml-2 text-sm text-gray-600">alertas</p>
            </div>
        </div>
    </div>

    <!-- Alertas Altas -->
    <div class="bg-white border border-orange-300 rounded-lg overflow-hidden">
        <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-orange-800">Altas</h3>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-orange-600">{{ $altas }}</p>
                <p class="ml-2 text-sm text-gray-600">alertas</p>
            </div>
        </div>
    </div>

    <!-- Alertas Normales -->
    <div class="bg-white border border-yellow-300 rounded-lg overflow-hidden">
        <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Normales</h3>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-yellow-600">{{ $normales }}</p>
                <p class="ml-2 text-sm text-gray-600">alertas</p>
            </div>
        </div>
    </div>

    <!-- Total de Activos -->
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-800">Activos Afectados</h3>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-gray-600">{{ $totalVehiculos }}</p>
                <p class="ml-2 text-sm text-gray-600">activos</p>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Alertas por Activo -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
            </svg>
            Alertas por Activo
        </h2>
    </div>
    <div class="divide-y divide-gray-200">
        @foreach($alertas as $alerta)
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <!-- Badge de urgencia -->
                        @if($alerta['urgencia'] === 'critica')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
                                </svg>
                                Crítica
                            </span>
                        @elseif($alerta['urgencia'] === 'alta')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-3">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                                </svg>
                                Alta
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-3">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
                                </svg>
                                Normal
                            </span>
                        @endif

                        <!-- Información del activo -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $alerta['vehiculo_info']['nombre_completo'] }}
                            </h3>
                            <p class="text-sm text-gray-500">
                                Kilometraje actual: {{ number_format($alerta['intervalo_alcanzado']['kilometraje_actual']) }} km
                            </p>
                        </div>
                    </div>

                    <!-- Detalles del sistema que requiere mantenimiento -->
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($alerta['sistema_mantenimiento']['nombre_sistema']) }}
                            </span>
                            <span class="ml-2 text-sm text-gray-600">
                                Intervalo: {{ number_format($alerta['intervalo_alcanzado']['intervalo_configurado']) }} km
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mb-2">
                            {{ $alerta['sistema_mantenimiento']['descripcion_sistema'] }}
                        </p>
                        <div class="text-sm text-gray-600">
                            <p><strong>Último mantenimiento:</strong> 
                                @if($alerta['historial_mantenimientos']['cantidad_encontrada'] > 0)
                                    @php $ultimoMant = $alerta['historial_mantenimientos']['mantenimientos'][0]; @endphp
                                    {{ number_format($ultimoMant['kilometraje']) }} km 
                                    ({{ $ultimoMant['fecha'] }})
                                @else
                                    Sin registros previos
                                @endif
                            </p>
                            <p><strong>Exceso acumulado:</strong> 
                                <span class="font-medium text-{{ $alerta['urgencia'] === 'critica' ? 'red' : ($alerta['urgencia'] === 'alta' ? 'orange' : 'yellow') }}-600">
                                    {{ number_format($alerta['intervalo_alcanzado']['km_exceso']) }} km 
                                    ({{ $alerta['intervalo_alcanzado']['porcentaje_sobrepaso'] }})
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="ml-6 flex-shrink-0">
                    @can('crear_mantenimientos')
                    <button onclick="openMantenimientoModal({{ $alerta['vehiculo_id'] }}, '{{ $alerta['vehiculo_info']['nombre_completo'] }}', '{{ $alerta['sistema_mantenimiento']['nombre_sistema'] }}')"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Registrar Mantenimiento
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@else
<!-- Estado vacío -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
    <svg class="mx-auto h-12 w-12 text-green-400" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
    </svg>
    <h3 class="mt-4 text-lg font-medium text-gray-900">¡Excelente! No hay alertas de mantenimiento</h3>
    <p class="mt-2 text-gray-500">
        Todos los activos están al día con sus mantenimientos preventivos basados en los intervalos configurados.
    </p>
    <div class="mt-6 flex justify-center space-x-3">
        <a href="{{ route('mantenimientos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Ver Historial
        </a>
        @can('crear_mantenimientos')
        <a href="{{ route('mantenimientos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Registro
        </a>
        @endcan
    </div>
</div>
@endif

<!-- Información adicional -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
            </svg>
            <h4 class="text-red-600 font-semibold">Alertas Críticas</h4>
        </div>
        <p class="text-gray-700">
            Se consideran críticas cuando el activo ha superado en más del 20% el intervalo de mantenimiento configurado.
            Estos activos requieren atención inmediata.
        </p>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
            </svg>
            <h4 class="text-orange-600 font-semibold">Alertas Altas</h4>
        </div>
        <p class="text-gray-700">
            Se consideran altas cuando el activo ha superado entre 10% y 20% el intervalo de mantenimiento. 
            Es recomendable programar el mantenimiento pronto.
        </p>
    </div>
</div>

<!-- Modal para Registrar Mantenimiento -->
<div id="registrar-mantenimiento-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Registrar Nuevo Mantenimiento
            </h3>
            <button onclick="closeMantenimientoModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div id="vehiculo-info" class="mb-4 text-sm text-gray-600">
            <p><strong>Activo:</strong> <span id="vehiculo-nombre">Seleccionar activo</span></p>
        </div>

        <form id="registrar-mantenimiento-form" method="POST" action="{{ route('mantenimientos.store') }}">
            @csrf
            <input type="hidden" id="vehiculo_id_input" name="vehiculo_id" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tipo de Servicio -->
                <div>
                    <label for="tipo_servicio" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select id="tipo_servicio" name="tipo_servicio" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar tipo</option>
                        <option value="PREVENTIVO">Preventivo</option>
                        <option value="CORRECTIVO">Correctivo</option>
                    </select>
                </div>

                <!-- Sistema del Activo -->
                <div>
                    <label for="sistema_vehiculo" class="block text-sm font-medium text-gray-700 mb-2">Sistema del Activo *</label>
                    <select id="sistema_vehiculo" name="sistema_vehiculo" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar sistema</option>
                        <option value="motor">Motor</option>
                        <option value="transmision">Transmisión</option>
                        <option value="hidraulico">Hidráulico</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <!-- Proveedor -->
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                    <input type="text" id="proveedor" name="proveedor" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nombre del taller o proveedor">
                </div>

                <!-- Kilometraje -->
                <div>
                    <label for="kilometraje_servicio" class="block text-sm font-medium text-gray-700 mb-2">Kilometraje del Servicio *</label>
                    <input type="number" id="kilometraje_servicio" name="kilometraje_servicio" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="ej: 15000">
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio *</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Dejar vacío si el mantenimiento está en progreso</p>
                </div>

                <!-- Costo -->
                <div class="md:col-span-2">
                    <label for="costo" class="block text-sm font-medium text-gray-700 mb-2">Costo (opcional)</label>
                    <input type="number" step="0.01" id="costo" name="costo" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="ej: 250.00">
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe el mantenimiento realizado o por realizar..."></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeMantenimientoModal()" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Registrar Mantenimiento
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Funciones para el modal de registrar mantenimiento
    function openMantenimientoModal(vehiculoId, vehiculoNombre, sistemaRecomendado = '') {
        // Obtener el modal específico
        const modal = document.getElementById('registrar-mantenimiento-modal');
        if (!modal) {
            console.error('Modal registrar-mantenimiento-modal no encontrado');
            return;
        }
        
        // Establecer la información del vehículo
        document.getElementById('vehiculo_id_input').value = vehiculoId;
        document.getElementById('vehiculo-nombre').textContent = vehiculoNombre;
        
        // Establecer la fecha de hoy como valor por defecto
        const today = new Date().toISOString().split('T')[0];
        const fechaInput = document.getElementById('fecha_inicio');
        if (fechaInput) {
            fechaInput.value = today;
        }
        
        // Preseleccionar tipo PREVENTIVO por defecto
        const tipoServicioSelect = document.getElementById('tipo_servicio');
        if (tipoServicioSelect) {
            tipoServicioSelect.value = 'PREVENTIVO';
        }
        
        // Preseleccionar el sistema si viene de la alerta
        if (sistemaRecomendado) {
            const sistemaSelect = document.getElementById('sistema_vehiculo');
            if (sistemaSelect) {
                // Mapear el nombre del sistema a los valores del select
                const sistemaValue = sistemaRecomendado.toLowerCase();
                if (['motor', 'transmision', 'hidraulico', 'general'].includes(sistemaValue)) {
                    sistemaSelect.value = sistemaValue;
                }
            }
            
            // Agregar el sistema recomendado a la descripción
            const descripcionInput = document.getElementById('descripcion');
            if (descripcionInput) {
                descripcionInput.value = `Mantenimiento preventivo de ${sistemaRecomendado} - `;
                descripcionInput.focus();
                descripcionInput.setSelectionRange(descripcionInput.value.length, descripcionInput.value.length);
            }
        }
        
        // Asegurar que el modal esté visible
        modal.classList.remove('hidden');
        modal.style.display = 'block';
    }

    function closeMantenimientoModal() {
        const modal = document.getElementById('registrar-mantenimiento-modal');
        if (!modal) {
            console.error('Modal registrar-mantenimiento-modal no encontrado');
            return;
        }
        
        // Cerrar el modal
        modal.classList.add('hidden');
        modal.style.display = 'none';
        
        // Limpiar el formulario
        const form = document.getElementById('registrar-mantenimiento-form');
        if (form) {
            form.reset();
        }
    }

    // Manejar envío del formulario de registrar mantenimiento
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registrar-mantenimiento-form');
        
        if (!form) {
            return;
        }
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validación básica del formulario
            const vehiculoId = document.getElementById('vehiculo_id_input').value;
            const tipoServicio = document.getElementById('tipo_servicio').value;
            const sistemaVehiculo = document.getElementById('sistema_vehiculo').value;
            const descripcion = document.getElementById('descripcion').value;
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const kilometraje = document.getElementById('kilometraje_servicio').value;
            
            // Validar campos requeridos
            if (!vehiculoId || !tipoServicio || !sistemaVehiculo || !descripcion || !fechaInicio || !kilometraje) {
                const camposFaltantes = [];
                if (!vehiculoId) camposFaltantes.push('Activo');
                if (!tipoServicio) camposFaltantes.push('Tipo de Servicio');
                if (!sistemaVehiculo) camposFaltantes.push('Sistema del Activo');
                if (!descripcion) camposFaltantes.push('Descripción');
                if (!fechaInicio) camposFaltantes.push('Fecha de Inicio');
                if (!kilometraje) camposFaltantes.push('Kilometraje');
                
                alert('Por favor complete los siguientes campos requeridos:\n- ' + camposFaltantes.join('\n- '));
                return;
            }
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Deshabilitar botón mientras se procesa
            submitButton.disabled = true;
            submitButton.textContent = 'Registrando...';
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
            const token = csrfToken ? (csrfToken.getAttribute('content') || csrfToken.value) : '';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    alertDiv.innerHTML = '✅ Mantenimiento registrado exitosamente';
                    document.body.appendChild(alertDiv);
                    
                    closeMantenimientoModal();
                    
                    // Remover alerta después de 3 segundos
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);
                    
                    // Recargar la página para actualizar las alertas
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert('❌ ' + (data.message || data.error || 'Error al registrar el mantenimiento'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al registrar el mantenimiento: ' + error.message);
            })
            .finally(() => {
                // Rehabilitar botón
                submitButton.disabled = false;
                submitButton.textContent = 'Registrar Mantenimiento';
            });
        });
    });

    // Event listener para cerrar modal al hacer clic en el fondo
    document.getElementById('registrar-mantenimiento-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMantenimientoModal();
        }
    });
</script>
@endpush
