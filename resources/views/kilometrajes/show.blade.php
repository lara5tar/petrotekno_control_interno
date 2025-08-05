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

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detalles del Registro de Kilometraje</h2>
            <p class="text-gray-600 mt-1">
                Registro del {{ $kilometraje->fecha_captura->format('d/m/Y') }} - {{ $kilometraje->vehiculo->placas }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('kilometrajes.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al listado
            </a>
            @can('editar_kilometrajes')
            <a href="{{ route('kilometrajes.edit', $kilometraje) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-yellow-900 font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Editar
            </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Información del Registro
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vehículo -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                            </svg>
                            Vehículo
                        </label>
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $kilometraje->vehiculo->placas }}</div>
                                <div class="text-gray-600">{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</div>
                                <small class="text-gray-500">Año {{ $kilometraje->vehiculo->anio }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Kilometraje -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            Kilometraje Registrado
                        </label>
                        <div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-blue-100 text-blue-800">
                                {{ number_format($kilometraje->kilometraje) }} km
                            </span>
                        </div>
                    </div>

                    <!-- Fecha -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            Fecha de Captura
                        </label>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $kilometraje->fecha_captura->format('d/m/Y') }}</div>
                            <small class="text-gray-500">{{ $kilometraje->fecha_captura->diffForHumans() }}</small>
                        </div>
                    </div>

                    <!-- Usuario -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            Registrado por
                        </label>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $kilometraje->usuarioCaptura->name }}</div>
                            <small class="text-gray-500">{{ $kilometraje->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>

                    <!-- Obra -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                            </svg>
                            Obra Asociada
                        </label>
                        <div>
                            @if($kilometraje->obra)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $kilometraje->obra->nombre_obra }}
                                </span>
                            @else
                                <span class="text-gray-500">Sin obra específica</span>
                            @endif
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H9.414l-3.707 3.707A1 1 0 014 14.414V11a1 1 0 01-1-1V4z" clip-rule="evenodd" />
                            </svg>
                            Observaciones
                        </label>
                        <div>
                            @if($kilometraje->observaciones)
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-md border-l-4 border-l-blue-500">
                                    {{ $kilometraje->observaciones }}
                                </div>
                            @else
                                <span class="text-gray-500">Sin observaciones adicionales</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="space-y-6">
            <!-- Estado del Vehículo -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Estado del Vehículo
                </h3>
                <div class="text-center">
                    <div class="mb-3">
                        @php
                            $statusColors = [
                                'Disponible' => 'bg-green-100 text-green-800',
                                'Asignado' => 'bg-blue-100 text-blue-800', 
                                'En Mantenimiento' => 'bg-yellow-100 text-yellow-800',
                                'Fuera de Servicio' => 'bg-red-100 text-red-800'
                            ];
                            $statusColor = $statusColors[$kilometraje->vehiculo->estatus->nombre_estatus] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusColor }}">
                            {{ $kilometraje->vehiculo->estatus->nombre_estatus }}
                        </span>
                    </div>
                    <div class="text-gray-600">
                        <small>{{ $kilometraje->vehiculo->estatus->descripcion }}</small>
                    </div>
                </div>
            </div>

            <!-- Alertas de Mantenimiento -->
            @if(isset($alertasMantenimiento) && count($alertasMantenimiento) > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Alertas de Mantenimiento
                </h3>
                <div class="space-y-3">
                    @foreach($alertasMantenimiento as $alerta)
                    <div class="p-3 rounded-md {{ $alerta['urgente'] ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold {{ $alerta['urgente'] ? 'text-red-800' : 'text-yellow-800' }}">{{ $alerta['tipo'] }}</div>
                                <small class="{{ $alerta['urgente'] ? 'text-red-600' : 'text-yellow-600' }}">{{ number_format($alerta['proximo_km']) }} km</small>
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
            @endif

            <!-- Información del Sistema -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Información del Sistema
                </h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>ID de Registro:</span>
                        <span class="font-semibold">#{{ $kilometraje->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Creado:</span>
                        <span>{{ $kilometraje->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($kilometraje->updated_at != $kilometraje->created_at)
                    <div class="flex justify-between">
                        <span>Última actualización:</span>
                        <span>{{ $kilometraje->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                </svg>
                Acciones Disponibles
            </h3>
            <div class="flex flex-wrap gap-3">
                @can('ver_kilometrajes')
                <a href="{{ route('kilometrajes.historial', $kilometraje->vehiculo_id) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    Ver Historial del Vehículo
                </a>
                @endcan

                @can('crear_kilometrajes')
                <a href="{{ route('kilometrajes.create') }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Nuevo Registro
                </a>
                @endcan

                @can('editar_kilometrajes')
                <a href="{{ route('kilometrajes.edit', $kilometraje) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-yellow-900 font-medium py-2 px-4 rounded flex items-center transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Editar Registro
                </a>
                @endcan

                @can('eliminar_kilometrajes')
                <form method="POST" action="{{ route('kilometrajes.destroy', $kilometraje) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Eliminar Registro
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
@endsection
