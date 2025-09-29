@extends('layouts.app')

@section('title', 'Reporte de Kilometrajes')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl sm:truncate">
                        Reporte de Kilometrajes
                    </h1>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Registros de kilometraje organizados por vehículo
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <div class="flex space-x-3">
                        <a href="{{ route('reportes.kilometrajes', array_merge(request()->query(), ['formato' => 'excel'])) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel
                        </a>
                        <a href="{{ route('reportes.kilometrajes', array_merge(request()->query(), ['formato' => 'pdf'])) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200">
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
                'vehiculo_id' => $vehiculoId,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ]"
            :rutaLimpiar="route('reportes.kilometrajes')"
        />

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Registros -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Total Registros</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($estadisticas['total_registros']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehículos con Kilometraje -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Vehículos Activos</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($estadisticas['vehiculos_con_kilometraje']) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kilometraje Promedio -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Km Promedio</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($estadisticas['kilometraje_promedio'], 0) }}</p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combustible Total -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">Combustible Total</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($kilometrajes->sum('cantidad_combustible'), 1) }} L
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
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
                <form method="GET" action="{{ route('reportes.kilometrajes') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-2">Vehículo</label>
                            <select name="vehiculo_id" id="vehiculo_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Todos los vehículos</option>
                                @foreach($vehiculosDisponibles as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <div class="flex items-end">
                            <div class="flex space-x-2 w-full">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filtrar
                                </button>
                                <a href="{{ route('reportes.kilometrajes') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center justify-center">
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

        <!-- Registros de Kilometrajes Organizados por Vehículo -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v10z"></path>
                    </svg>
                    Registros de Kilometrajes por Vehículo ({{ number_format($kilometrajes->count()) }} registros)
                </h2>
                <p class="text-sm text-gray-600 mt-1">Detalle de kilometrajes organizados por vehículo con separación clara</p>
            </div>
            
            @if($kilometrajes->count() > 0)
                @php
                    $kilometrajesPorVehiculo = $kilometrajes->groupBy('vehiculo_id');
                @endphp
                
                @foreach($kilometrajesPorVehiculo as $vehiculoId => $registrosVehiculo)
                    @php
                        $vehiculo = $registrosVehiculo->first()->vehiculo;
                        $totalCombustible = $registrosVehiculo->sum('cantidad_combustible');
                        $kmMinimo = $registrosVehiculo->min('kilometraje');
                        $kmMaximo = $registrosVehiculo->max('kilometraje');
                    @endphp
                    
                    <!-- Contenedor individual para cada vehículo -->
                    <div class="border-b-8 border-gray-100 {{ !$loop->last ? 'mb-6' : '' }}">
                        <!-- Encabezado del Vehículo Rediseñado -->
                        <div class="bg-white border-2 border-gray-200 rounded-t-2xl px-6 py-6 shadow-sm">
                            <!-- Información Principal del Vehículo -->
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-3 shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h3>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gradient-to-r from-indigo-100 to-indigo-200 text-indigo-800 border border-indigo-300">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $vehiculo->anio }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chips de Información del Vehículo -->
                            <div class="flex flex-wrap gap-3 mb-4">
                                <!-- Chip de Placas -->
                                <div class="inline-flex items-center px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-50 to-emerald-100 border-2 border-emerald-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span class="text-sm font-bold text-emerald-800">{{ $vehiculo->placas ?: 'Sin placas' }}</span>
                                </div>

                                <!-- Chip de Ubicación -->
                                <div class="inline-flex items-center px-4 py-2 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-bold text-purple-800">
                                        {{ $vehiculo->estado ?: 'Sin estado' }}{{ $vehiculo->municipio ? ', ' . $vehiculo->municipio : '' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Estadísticas Mini en Chips -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Chip de Registros -->
                                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Registros</p>
                                            <p class="text-2xl font-bold mt-1">{{ $registrosVehiculo->count() }}</p>
                                        </div>
                                        <div class="bg-white bg-opacity-20 rounded-xl p-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chip de Combustible -->
                                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-amber-100 text-xs font-medium uppercase tracking-wider">Combustible</p>
                                            <p class="text-2xl font-bold mt-1">{{ number_format($totalCombustible, 1) }}L</p>
                                        </div>
                                        <div class="bg-white bg-opacity-20 rounded-xl p-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chip de Rango Km -->
                                <div class="bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-emerald-100 text-xs font-medium uppercase tracking-wider">Rango Km</p>
                                            <p class="text-2xl font-bold mt-1">{{ number_format($kmMaximo - $kmMinimo) }}</p>
                                        </div>
                                        <div class="bg-white bg-opacity-20 rounded-xl p-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de registros del vehículo con bordes claros -->
                        <div class="overflow-x-auto border-l-4 border-r-4 border-blue-200">
                            <table class="w-full border-collapse">
                                <thead class="bg-gray-50 border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Kilometraje</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Combustible</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Obra Asignada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Registrado por</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach($registrosVehiculo->sortByDesc('created_at_registro') as $kilometraje)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200 border-b border-gray-100">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $kilometraje->fecha_captura->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-100">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    {{ number_format($kilometraje->kilometraje) }} km
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-100">
                                                @if($kilometraje->cantidad_combustible)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                                        </svg>
                                                        {{ number_format($kilometraje->cantidad_combustible, 2) }} L
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                        Sin registro
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-100">
                                                @if($kilometraje->obra)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                        {{ $kilometraje->obra->nombre }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                        Sin obra asignada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap border-r border-gray-100">
                                                @if($kilometraje->usuarioCaptura)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        {{ $kilometraje->usuarioCaptura->nombre_completo }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                        Usuario no disponible
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $kilometraje->observaciones }}">
                                                    {{ $kilometraje->observaciones ?: 'Sin observaciones' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen del vehículo con diseño de chips Petrotekno -->
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-6 border-l-4 border-r-4 border-b-4 border-yellow-400 rounded-b-2xl shadow-lg">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Chips de Estadísticas de Kilometraje - Estilo Petrotekno -->
                                <div class="flex flex-wrap gap-3">
                                    <!-- Chip Km Mínimo - Amarillo Petrotekno -->
                                    <div class="inline-flex items-center px-5 py-4 rounded-2xl bg-gradient-to-br from-yellow-100 to-yellow-200 border-2 border-yellow-300 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full p-2.5 mr-3 shadow-md">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-yellow-700 uppercase tracking-wider mb-1">Km Mínimo</p>
                                            <p class="text-xl font-black text-yellow-800">{{ number_format($kmMinimo) }}</p>
                                        </div>
                                    </div>

                                    <!-- Chip Km Máximo - Amarillo Petrotekno -->
                                    <div class="inline-flex items-center px-5 py-4 rounded-2xl bg-gradient-to-br from-yellow-100 to-yellow-200 border-2 border-yellow-300 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full p-2.5 mr-3 shadow-md">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-yellow-700 uppercase tracking-wider mb-1">Km Máximo</p>
                                            <p class="text-xl font-black text-yellow-800">{{ number_format($kmMaximo) }}</p>
                                        </div>
                                    </div>

                                    <!-- Chip Diferencia - Amarillo Petrotekno -->
                                    <div class="inline-flex items-center px-5 py-4 rounded-2xl bg-gradient-to-br from-yellow-100 to-yellow-200 border-2 border-yellow-300 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full p-2.5 mr-3 shadow-md">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-yellow-700 uppercase tracking-wider mb-1">Diferencia</p>
                                            <p class="text-xl font-black text-yellow-800">{{ number_format($kmMaximo - $kmMinimo) }} km</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chip de Total Combustible - Amarillo Petrotekno Destacado -->
                                <div class="inline-flex items-center px-6 py-4 rounded-2xl bg-gradient-to-br from-yellow-200 to-amber-200 border-3 border-yellow-400 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                                    <div class="bg-gradient-to-br from-yellow-600 to-amber-600 rounded-full p-3 mr-4 shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-yellow-700 uppercase tracking-wider mb-1">Total Combustible</p>
                                        <p class="text-2xl font-black text-yellow-800">{{ number_format($totalCombustible, 2) }} L</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $kilometrajes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <div class="text-4xl mb-2">📊</div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No hay registros de kilometrajes</h3>
                        <p class="text-sm text-gray-500">No se encontraron registros con los filtros aplicados.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
