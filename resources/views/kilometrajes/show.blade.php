@extends('layouts.app')

@section('title', 'Detalles del Kilometraje')

@section('header', 'Detalles del Registro de Kilometraje')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Kilometrajes', 'url' => route('kilometrajes.index')],
        ['label' => 'Detalles del Registro']
    ]" />

<!-- Contenido Principal -->
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo - Datos del Registro -->
        <div class="space-y-6">
            <!-- Información del Registro -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos del Kilometraje</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje Registrado</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-blue-300 rounded-full mr-2"></span>
                                {{ number_format($kilometraje->kilometraje) }} km
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Captura</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->fecha_captura->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Registrado por</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->usuarioCaptura->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID de Registro</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                #{{ str_pad($kilometraje->id, 4, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Obra Asociada</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                @if($kilometraje->obra)
                                    {{ $kilometraje->obra->nombre_obra }}
                                @else
                                    Sin obra específica
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($kilometraje->observaciones)
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Observaciones</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->observaciones }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Estado del Vehículo -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-green-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Estado del Vehículo
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estado Actual</label>
                            @php
                                $statusColors = [
                                    'Disponible' => 'bg-green-600',
                                    'Asignado' => 'bg-blue-600', 
                                    'En Mantenimiento' => 'bg-yellow-600',
                                    'Fuera de Servicio' => 'bg-red-600'
                                ];
                                $statusColor = $statusColors[$kilometraje->vehiculo->estatus->nombre_estatus] ?? 'bg-gray-600';
                            @endphp
                            <div class="{{ $statusColor }} text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 opacity-75"></span>
                                {{ $kilometraje->vehiculo->estatus->nombre_estatus }}
                            </div>
                        </div>
                    </div>

                    @if($kilometraje->vehiculo->estatus->descripcion)
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Descripción del Estado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->vehiculo->estatus->descripcion }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Información del Vehículo y Alertas -->
        <div class="space-y-6">
            <!-- Información del Vehículo -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Información del Vehículo</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Placas</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->vehiculo->placas }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Año</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->vehiculo->anio }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Marca y Modelo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Registro</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Hace</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $kilometraje->fecha_captura->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de Mantenimiento -->
            @if(isset($alertasMantenimiento) && count($alertasMantenimiento) > 0)
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-yellow-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 13.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        Alertas de Mantenimiento
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($alertasMantenimiento as $alerta)
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
            </div>
            @else
            <!-- Mensaje cuando no hay alertas -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-green-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Estado de Mantenimiento
                    </h3>
                </div>
                <div class="p-4">
                    <div class="text-center py-4">
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Sin alertas de mantenimiento
                        </div>
                        <p class="text-sm text-gray-600 mt-2">El vehículo está al día con su mantenimiento</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    @can('ver_kilometrajes')
    <!-- Botón Ver Historial -->
    <a href="{{ route('kilometrajes.historial', $kilometraje->vehiculo_id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Ver Historial del Vehículo">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
        </svg>
        <span>Historial</span>
    </a>
    @endcan

    @can('editar_kilometrajes')
    <!-- Botón Editar -->
    <a href="{{ route('kilometrajes.edit', $kilometraje) }}" 
       class="bg-yellow-500 hover:bg-yellow-600 text-yellow-900 font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Editar Registro">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>
    @endcan

    @can('eliminar_kilometrajes')
    <!-- Botón Eliminar -->
    <form action="{{ route('kilometrajes.destroy', $kilometraje) }}" 
          method="POST" 
          class="inline" 
          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este registro de kilometraje? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
                title="Eliminar Registro">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>Eliminar</span>
        </button>
    </form>
    @endcan
</div>
@endsection
