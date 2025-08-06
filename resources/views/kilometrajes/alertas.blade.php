@extends('layouts.app')

@section('title', 'Alertas de Mantenimiento')
@section('header', 'Alertas de Mantenimiento')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 flex items-center mb-2">
            <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            Alertas de Mantenimiento
        </h1>
        <p class="text-gray-600">Vehículos que requieren mantenimiento preventivo basado en kilometraje</p>
    </div>
    <div class="flex space-x-2 mt-4 sm:mt-0">
        <a href="{{ route('kilometrajes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
        @can('crear_kilometrajes')
        <a href="{{ route('kilometrajes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Registro
        </a>
        @endcan
    </div>
</div>

@if($alertas->count() > 0)
<!-- Resumen de Alertas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $urgentes = $alertas->where('alerta.urgente', true)->count();
        $proximas = $alertas->where('alerta.urgente', false)->count();
        $totalVehiculos = $alertas->pluck('vehiculo.id')->unique()->count();
    @endphp
    
    <!-- Mantenimientos Urgentes -->
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-red-50 px-4 py-3 border-b border-red-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Mantenimientos Urgentes</h3>
                </div>
            </div>
        </div>
        <div class="px-4 py-5">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-red-600">{{ $urgentes }}</p>
                <p class="ml-2 text-sm text-gray-600">vehículos</p>
            </div>
        </div>
    </div>

    <!-- Próximos Mantenimientos -->
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Próximos Mantenimientos</h3>
                </div>
            </div>
        </div>
        <div class="px-4 py-5">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-yellow-600">{{ $proximas }}</p>
                <p class="ml-2 text-sm text-gray-600">vehículos</p>
            </div>
        </div>
    </div>

    <!-- Vehículos Afectados -->
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7h-3V6a3 3 0 0 0-3-3H5a3 3 0 0 0-3 3v8a1 1 0 0 0 1 1h1.5a2.5 2.5 0 0 1 5 0h3a2.5 2.5 0 0 1 5 0H19a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zM7 16.5A1.5 1.5 0 1 1 7 14a1.5 1.5 0 0 1 0 2.5zm10 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Vehículos Afectados</h3>
                </div>
            </div>
        </div>
        <div class="px-4 py-5">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-blue-600">{{ $totalVehiculos }}</p>
                <p class="ml-2 text-sm text-gray-600">únicos</p>
            </div>
        </div>
    </div>

    <!-- Total de Alertas -->
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-green-50 px-4 py-3 border-b border-green-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Total de Alertas</h3>
                </div>
            </div>
        </div>
        <div class="px-4 py-5">
            <div class="flex items-baseline">
                <p class="text-3xl font-semibold text-green-600">{{ $alertas->count() }}</p>
                <p class="ml-2 text-sm text-gray-600">alertas</p>
            </div>
        </div>
    </div>
</div>

<!-- Listado de Alertas por Vehículo -->
<div class="bg-white border border-gray-300 rounded-lg">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-300">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20h4c0 1.1-.9 2-2 2s-2-.9-2-2zm10-2.65V19H4v-1.65l2-1.88v-5.15C6 7.4 7.56 5.1 10 4.34v-.38c0-1.42 1.49-2.5 2.99-1.76.65.32 1.01 1.03 1.01 1.76v.39c2.44.75 4 3.06 4 5.98v5.15l2 1.88z"/>
            </svg>
            Alertas por Vehículo
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($alertas->groupBy('vehiculo.id') as $vehiculoId => $alertasVehiculo)
            @php 
                $vehiculo = $alertasVehiculo->first()['vehiculo']; 
                $tieneUrgentes = $alertasVehiculo->where('alerta.urgente', true)->count() > 0;
            @endphp
            <div class="bg-white border {{ $tieneUrgentes ? 'border-red-300' : 'border-yellow-300' }} rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <!-- Header del vehículo -->
                <div class="{{ $tieneUrgentes ? 'bg-red-500' : 'bg-yellow-500' }} text-white px-4 py-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7h-3V6a3 3 0 0 0-3-3H5a3 3 0 0 0-3 3v8a1 1 0 0 0 1 1h1.5a2.5 2.5 0 0 1 5 0h3a2.5 2.5 0 0 1 5 0H19a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zM7 16.5A1.5 1.5 0 1 1 7 14a1.5 1.5 0 0 1 0 2.5zm10 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                </svg>
                                {{ $vehiculo->placas }}
                            </h4>
                            <p class="text-sm opacity-90">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                        </div>
                        @if($tieneUrgentes)
                        <span class="bg-white text-red-600 px-2 py-1 rounded-full text-xs font-bold flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
                            </svg>
                            URGENTE
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Kilometraje Actual -->
                <div class="px-4 py-4 text-center border-b border-gray-200">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ number_format($alertasVehiculo->first()['ultimo_kilometraje']->kilometraje) }} km
                    </div>
                    <div class="text-sm text-gray-600">Kilometraje actual</div>
                </div>

                <!-- Alertas del vehículo -->
                <div class="px-4 py-4 space-y-3">
                    @foreach($alertasVehiculo as $alertaData)
                    @php $alerta = $alertaData['alerta']; @endphp
                    <div class="p-3 rounded-md {{ $alerta['urgente'] ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold {{ $alerta['urgente'] ? 'text-red-800' : 'text-yellow-800' }}">{{ $alerta['tipo'] }}</div>
                                <small class="{{ $alerta['urgente'] ? 'text-red-600' : 'text-yellow-600' }}">Próximo: {{ number_format($alerta['proximo_km']) }} km</small>
                            </div>
                            <div class="text-right">
                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $alerta['urgente'] ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ number_format($alerta['km_restantes']) }} km
                                </div>
                                <div><small class="{{ $alerta['urgente'] ? 'text-red-600' : 'text-yellow-600' }}">restantes</small></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Acciones -->
                <div class="px-4 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex space-x-2">
                        @can('ver_kilometrajes')
                        <a href="{{ route('kilometrajes.historial', $vehiculo->id) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Historial
                        </a>
                        @endcan
                        
                        @can('crear_kilometrajes')
                        <a href="{{ route('kilometrajes.create') }}?vehiculo_id={{ $vehiculo->id }}" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Registro
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@else
<!-- Sin Alertas -->
<div class="bg-white border border-gray-300 rounded-lg p-12 text-center">
    <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
    </svg>
    <h3 class="text-xl font-semibold text-green-600 mb-2">¡Excelente! No hay alertas de mantenimiento</h3>
    <p class="text-gray-600 mb-6">
        Todos los vehículos están al día con sus mantenimientos preventivos basados en kilometraje.
    </p>
    <div class="flex justify-center space-x-4">
        @can('ver_kilometrajes')
        <a href="{{ route('kilometrajes.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Ver Kilometrajes
        </a>
        @endcan
        @can('crear_kilometrajes')
        <a href="{{ route('kilometrajes.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Registro
        </a>
        @endcan
    </div>
</div>
@endif

@if($alertas->count() > 0)
<!-- Información Adicional -->
<div class="mt-8 bg-white border border-gray-300 rounded-lg">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-300">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
            </svg>
            Información sobre las Alertas
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h4 class="text-red-600 font-semibold mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/>
                    </svg>
                    Mantenimientos Urgentes
                </h4>
                <p class="text-gray-700">
                    Se consideran urgentes cuando faltan 1,000 km o menos para el próximo mantenimiento. 
                    Estos vehículos deben programarse para mantenimiento inmediato.
                </p>
            </div>
            <div>
                <h4 class="text-yellow-600 font-semibold mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
                    </svg>
                    Próximos Mantenimientos
                </h4>
                <p class="text-gray-700">
                    Vehículos que se acercan al kilometraje de mantenimiento. Es recomendable comenzar 
                    a planificar el mantenimiento preventivo.
                </p>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h4 class="text-blue-600 font-semibold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                </svg>
                Tipos de Mantenimiento
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-blue-800 mb-2">Motor</h5>
                    <p class="text-blue-700 text-sm">Cambio de aceite y filtros del motor</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-green-800 mb-2">Transmisión</h5>
                    <p class="text-green-700 text-sm">Cambio de aceite de transmisión</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-purple-800 mb-2">Hidráulico</h5>
                    <p class="text-purple-700 text-sm">Cambio de aceite del sistema hidráulico</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
