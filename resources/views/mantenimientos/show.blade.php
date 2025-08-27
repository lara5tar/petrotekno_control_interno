@extends('layouts.app')

@section('title', 'Control de Mantenimientos')

@section('header', 'Control de Mantenimientos de Petrotekno')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Mantenimientos', 'url' => route('mantenimientos.index')],
    ['label' => 'Mantenimiento #' . $mantenimiento->id]
]" />

<!-- Contenido Principal -->
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo - Datos Generales -->
        <div class="space-y-6">
            <!-- Datos Generales del Mantenimiento -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos Generales del Mantenimiento</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID Mantenimiento</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                #{{ str_pad($mantenimiento->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estado</label>
                            <div class="bg-{{ $mantenimiento->fecha_fin ? 'green' : 'yellow' }}-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-{{ $mantenimiento->fecha_fin ? 'green' : 'yellow' }}-300 rounded-full mr-2"></span>
                                {{ $mantenimiento->fecha_fin ? 'Completado' : 'En Proceso' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tipo de Servicio</label>
                            <div class="bg-{{ $mantenimiento->tipo_servicio === 'PREVENTIVO' ? 'green' : 'orange' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $mantenimiento->tipo_servicio }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Sistema del Activo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $mantenimiento->sistema_vehiculo ?: 'General' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Proveedor</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $mantenimiento->proveedor ?: 'No especificado' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Costo Total</label>
                            <div class="bg-{{ $mantenimiento->costo ? 'green' : 'gray' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                @if($mantenimiento->costo)
                                    ${{ number_format($mantenimiento->costo, 2) }}
                                @else
                                    No especificado
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje del Servicio</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ number_format($mantenimiento->kilometraje_servicio) }} km
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Activo -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Información del Activo</h3>
                </div>
                <div class="p-4 space-y-4">
                    @if($mantenimiento->vehiculo)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Marca y Modelo</label>
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                    {{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Año</label>
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                    {{ $mantenimiento->vehiculo->anio }}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Placas</label>
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                    {{ $mantenimiento->vehiculo->placas }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Número de Serie</label>
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                    {{ $mantenimiento->vehiculo->n_serie }}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Kilometraje Actual</label>
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                    {{ number_format($mantenimiento->vehiculo->kilometraje_actual) }} km
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Información del activo no disponible</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Cronología y Descripción -->
        <div class="space-y-6">
            <!-- Cronología del Mantenimiento -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Cronología del Mantenimiento</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full mb-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Fecha de Inicio</h3>
                            <p class="text-sm text-gray-600">
                                {{ $mantenimiento->fecha_inicio ? \Carbon\Carbon::parse($mantenimiento->fecha_inicio)->format('d/m/Y') : 'No especificada' }}
                            </p>
                            @if($mantenimiento->fecha_inicio)
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ \Carbon\Carbon::parse($mantenimiento->fecha_inicio)->diffForHumans() }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 {{ $mantenimiento->fecha_fin ? 'bg-green-100' : 'bg-gray-100' }} rounded-full mb-3">
                                @if($mantenimiento->fecha_fin)
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Fecha de Finalización</h3>
                            <p class="text-sm text-gray-600">
                                {{ $mantenimiento->fecha_fin ? \Carbon\Carbon::parse($mantenimiento->fecha_fin)->format('d/m/Y') : 'En proceso' }}
                            </p>
                            @if($mantenimiento->fecha_fin)
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ \Carbon\Carbon::parse($mantenimiento->fecha_fin)->diffForHumans() }}
                                </p>
                            @endif
                        </div>

                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-orange-100 rounded-full mb-3">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Duración</h3>
                            <p class="text-sm text-gray-600">
                                @if($mantenimiento->fecha_inicio && $mantenimiento->fecha_fin)
                                    @php
                                        $fechaInicio = \Carbon\Carbon::parse($mantenimiento->fecha_inicio);
                                        $fechaFin = \Carbon\Carbon::parse($mantenimiento->fecha_fin);
                                        $diasCompletos = $fechaInicio->diffInDays($fechaFin);
                                        // Si es el mismo día, mostrar como mínimo 1 día
                                        if ($diasCompletos == 0) {
                                            $diasCompletos = 1;
                                        }
                                    @endphp
                                    {{ $diasCompletos }} {{ $diasCompletos == 1 ? 'día' : 'días' }}
                                @elseif($mantenimiento->fecha_inicio)
                                    @php
                                        $fechaInicio = \Carbon\Carbon::parse($mantenimiento->fecha_inicio);
                                        $ahora = \Carbon\Carbon::now();
                                        $diasCompletos = $fechaInicio->diffInDays($ahora);
                                        // Si es el mismo día, mostrar como mínimo 1 día
                                        if ($diasCompletos == 0) {
                                            $diasCompletos = 1;
                                        }
                                    @endphp
                                    {{ $diasCompletos }} {{ $diasCompletos == 1 ? 'día' : 'días' }} hasta hoy
                                @else
                                    No calculable
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción del servicio -->
            @if($mantenimiento->descripcion_servicio)
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Descripción del Servicio</h3>
                    </div>
                    <div class="p-4">
                        <p class="text-gray-700 leading-relaxed">{{ $mantenimiento->descripcion_servicio }}</p>
                    </div>
                </div>
            @endif

            <!-- Observaciones -->
            @if($mantenimiento->observaciones)
                <div class="bg-white border border-gray-300 rounded-lg">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                        <h3 class="font-semibold text-gray-800">Observaciones</h3>
                    </div>
                    <div class="p-4">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <p class="text-yellow-700">{{ $mantenimiento->observaciones }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Acciones principales -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
        <div class="px-6 py-4">
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar Mantenimiento
                </a>
                
                <form method="POST" 
                      action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" 
                      class="inline"
                      onsubmit="return confirm('¿Está seguro de eliminar este mantenimiento?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Eliminar
                    </button>
                </form>
                
                <a href="{{ route('mantenimientos.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Volver al Listado
                </a>

                <a href="{{ route('mantenimientos.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Mantenimiento
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
