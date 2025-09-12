@extends('layouts.app')

@section('title', 'Inventario de Vehículos - Reportes')

@section('content')
<!-- Fondo con gradiente sutil -->
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header profesional -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-1">
                            <a href="{{ route('reportes.index') }}" class="hover:text-blue-600 transition-colors">Reportes</a>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span>Inventario de Vehículos</span>
                            @if($estatus)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="capitalize font-medium">
                                    @switch($estatus)
                                        @case('disponible')
                                            <span class="text-green-600">Disponibles</span>
                                            @break
                                        @case('asignado')
                                            <span class="text-blue-600">Asignados</span>
                                            @break
                                        @case('en_mantenimiento')
                                            <span class="text-yellow-600">En Mantenimiento</span>
                                            @break
                                        @case('fuera_de_servicio')
                                            <span class="text-orange-600">Fuera de Servicio</span>
                                            @break
                                        @case('baja')
                                            <span class="text-red-600">Dados de Baja</span>
                                            @break
                                        @default
                                            <span>{{ ucfirst(str_replace('_', ' ', $estatus)) }}</span>
                                    @endswitch
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                            @if($estatus)
                                Vehículos 
                                @switch($estatus)
                                    @case('disponible')
                                        <span class="text-green-600">Disponibles</span>
                                        @break
                                    @case('asignado')
                                        <span class="text-blue-600">Asignados</span>
                                        @break
                                    @case('en_mantenimiento')
                                        <span class="text-yellow-600">en Mantenimiento</span>
                                        @break
                                    @case('fuera_de_servicio')
                                        <span class="text-orange-600">Fuera de Servicio</span>
                                        @break
                                    @case('baja')
                                        <span class="text-red-600">Dados de Baja</span>
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $estatus)) }}
                                @endswitch
                            @else
                                Inventario de Vehículos
                            @endif
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            @if($estatus)
                                Reporte específico de vehículos con estado: {{ ucfirst(str_replace('_', ' ', $estatus)) }}
                            @else
                                Reporte completo del inventario vehicular con último kilometraje registrado
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Botones de exportación -->
                    <div class="flex space-x-2">
                        <a href="{{ request()->fullUrlWithQuery(['formato' => 'excel']) }}" 
                           class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Excel
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['formato' => 'pdf']) }}" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Componente de Filtros Aplicados -->
        <x-filtros-aplicados 
            :filtros="[
                'estatus' => $estatus,
                'marca' => $marca,
                'anio' => $anio
            ]"
            :rutaLimpiar="route('reportes.inventario-vehiculos')"
        />
        
        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Vehículos -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total Vehículos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($estadisticas['total_vehiculos']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Con Km Registrado -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Con Km Registrado</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($vehiculos->where('kilometraje_actual', '>', 0)->count()) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Km Total -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Km Total</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($estadisticas['kilometraje_total']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Km Promedio -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Km Promedio</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($estadisticas['kilometraje_promedio'] ?? 0) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                    </svg>
                    Filtros de Búsqueda
                </h2>
                <p class="text-sm text-gray-600 mt-1">Personaliza los criterios de búsqueda para el reporte</p>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('reportes.inventario-vehiculos') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="estatus" class="block text-sm font-medium text-gray-700 mb-2">Estatus</label>
                            <select name="estatus" id="estatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Todos los estatus</option>
                                @foreach($estatusDisponibles as $estatusEnum)
                                    <option value="{{ $estatusEnum->value }}" 
                                            {{ $estatus === $estatusEnum->value ? 'selected' : '' }}>
                                        {{ $estatusEnum->nombre() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                            <select name="marca" id="marca" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Todas las marcas</option>
                                @foreach($marcasDisponibles as $marcaOption)
                                    <option value="{{ $marcaOption }}" 
                                            {{ $marca === $marcaOption ? 'selected' : '' }}>
                                        {{ $marcaOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                            <select name="anio" id="anio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Todos los años</option>
                                @foreach($aniosDisponibles->reverse() as $anioOption)
                                    <option value="{{ $anioOption }}" 
                                            {{ $anio == $anioOption ? 'selected' : '' }}>
                                        {{ $anioOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <div class="flex space-x-2 w-full">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filtrar
                                </button>
                                <a href="{{ route('reportes.inventario-vehiculos') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Distribución por Estatus -->
        @if($estadisticas['por_estatus']->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Distribución por Estatus
                </h2>
                <p class="text-sm text-gray-600 mt-1">Visualización de la distribución de vehículos por estado</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($estadisticas['por_estatus'] as $statusKey => $count)
                        @php
                            $statusEnum = \App\Enums\EstadoVehiculo::fromValue($statusKey);
                            $percentage = $estadisticas['total_vehiculos'] > 0 ? round(($count / $estadisticas['total_vehiculos']) * 100, 1) : 0;
                            $colorClass = match($statusEnum->color()) {
                                'green' => 'bg-emerald-100 text-emerald-800',
                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                'red' => 'bg-red-100 text-red-800',
                                'orange' => 'bg-orange-100 text-orange-800',
                                'blue' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <div class="text-center p-4 rounded-xl {{ $colorClass }} hover:shadow-md transition-shadow duration-200">
                            <div class="text-sm font-medium mb-2">{{ $statusEnum->nombre() }}</div>
                            <div class="text-2xl font-bold mb-1">{{ $count }}</div>
                            <div class="text-xs opacity-75">{{ $percentage }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Tabla de Resultados -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10z"></path>
                    </svg>
                    Inventario Detallado ({{ number_format($vehiculos->count()) }} vehículos)
                </h2>
                <p class="text-sm text-gray-600 mt-1">Listado completo de vehículos con detalles de kilometraje</p>
            </div>
            <div class="overflow-x-auto">
                @if($vehiculos->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Registro</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vehiculos as $vehiculo)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $vehiculo->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</div>
                                        <div class="text-sm text-gray-500">{{ $vehiculo->anio }} - {{ $vehiculo->n_serie }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'Sin tipo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $vehiculo->placas }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColorClass = match($vehiculo->estado_enum->color()) {
                                                'green' => 'bg-emerald-100 text-emerald-800',
                                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                                'red' => 'bg-red-100 text-red-800',
                                                'orange' => 'bg-orange-100 text-orange-800',
                                                'blue' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColorClass }}">
                                            {{ $vehiculo->estado_enum->nombre() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $vehiculo->estado ?: 'Sin estado' }}
                                        </div>
                                        @if($vehiculo->municipio)
                                            <div class="text-sm text-gray-500">
                                                {{ $vehiculo->municipio }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vehiculo->necesita_registro_km)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.992-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                Necesita registro
                                            </span>
                                            @if($vehiculo->dias_sin_registro !== null)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $vehiculo->dias_sin_registro }} días sin registro
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Al día
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron vehículos</h3>
                        <p class="text-gray-500">
                            Intente modificar los filtros de búsqueda para obtener resultados.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Notas del Reporte -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Notas del Reporte
                </h2>
                <p class="text-sm text-gray-600 mt-1">Información adicional sobre criterios y cálculos del reporte</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Criterios de Estado de Registro</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center space-x-2">
                                <div class="h-2 w-2 bg-emerald-500 rounded-full"></div>
                                <span><strong>Al día:</strong> Kilometraje registrado en los últimos 7 días</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="h-2 w-2 bg-yellow-500 rounded-full"></div>
                                <span><strong>Necesita registro:</strong> Más de 7 días sin registro de kilometraje</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="h-2 w-2 bg-gray-500 rounded-full"></div>
                                <span><strong>Sin registro:</strong> Vehículo sin ningún registro de kilometraje</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="h-8 w-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Información del Reporte</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center space-x-2">
                                <div class="h-2 w-2 bg-purple-500 rounded-full"></div>
                                <span><strong>Estado:</strong> Estado actual del vehículo</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                                <span><strong>Ubicación:</strong> Estado y municipio donde se encuentra el vehículo</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-start space-x-3">
                        <div class="h-6 w-6 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-medium text-blue-900 mb-1">Recomendación</p>
                            <p class="text-blue-800 leading-relaxed">
                                Mantenga los registros de kilometraje actualizados para obtener un mejor control del uso vehicular y programación de mantenimientos preventivos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit del formulario cuando cambien los filtros (opcional)
    const filtroSelects = document.querySelectorAll('#estatus, #marca, #anio');
    
    filtroSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Opcional: Auto-submit cuando cambie algún filtro
            // this.form.submit();
        });
    });

    // Mejorar la experiencia visual de la tabla
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush
