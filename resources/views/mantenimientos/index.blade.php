@extends('layouts.app')

@section('title', 'Mantenimientos')
@section('header', 'Gestión de Mantenimientos')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header con botón de crear -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Listado de Mantenimientos</h2>
            <a href="{{ route('mantenimientos.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Mantenimiento
            </a>
        </div>

        <!-- Filtros -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               name="buscar" 
                               placeholder="Buscar por proveedor o descripción" 
                               value="{{ request('buscar') }}">
                    </div>
                    <div>
                        <select name="vehiculo_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos los vehículos</option>
                            @foreach($vehiculosOptions as $vehiculo)
                                <option value="{{ $vehiculo->id }}" 
                                        {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="tipo_servicio" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos los tipos</option>
                            @foreach($tiposServicioOptions as $tipo)
                                <option value="{{ $tipo->id }}" 
                                        {{ request('tipo_servicio') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre_tipo_servicio }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               name="fecha_desde" 
                               value="{{ request('fecha_desde') }}"
                               title="Fecha desde">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['buscar', 'vehiculo_id', 'tipo_servicio', 'fecha_desde']))
                    <div class="flex justify-start">
                        <a href="{{ route('mantenimientos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Limpiar filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Tabla de mantenimientos -->
        <div class="overflow-x-auto">
            @if($mantenimientos->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sistema</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mantenimientos as $mantenimiento)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $mantenimiento->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($mantenimiento->vehiculo)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $mantenimiento->vehiculo->placas }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">Vehículo no disponible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $mantenimiento->tipo_servicio === 'PREVENTIVO' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $mantenimiento->tipo_servicio }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="capitalize">{{ $mantenimiento->sistema_vehiculo ?: 'General' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $mantenimiento->proveedor ?: 'No especificado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $mantenimiento->fecha_inicio ? \Carbon\Carbon::parse($mantenimiento->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($mantenimiento->kilometraje_servicio) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($mantenimiento->costo)
                                        ${{ number_format($mantenimiento->costo, 2) }}
                                    @else
                                        <span class="text-gray-400">No especificado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($mantenimiento->fecha_fin)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Completado
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            En Proceso
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                           title="Ver detalles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                           title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" 
                                              class="inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar este mantenimiento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Mostrando {{ $mantenimientos->firstItem() ?: 0 }} - {{ $mantenimientos->lastItem() ?: 0 }} 
                            de {{ $mantenimientos->total() }} mantenimientos
                        </div>
                        <div>
                            {{ $mantenimientos->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>

            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4zM6.7 8.8c-.7.7-1.9.7-2.6 0-.7-.7-.7-1.9 0-2.6.7-.7 1.9-.7 2.6 0 .7.7.7 1.9 0 2.6z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay mantenimientos registrados</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request()->hasAny(['buscar', 'vehiculo_id', 'tipo_servicio', 'fecha_desde']))
                            No se encontraron mantenimientos con los filtros aplicados.
                        @else
                            Comience registrando el primer mantenimiento de su flota vehicular.
                        @endif
                    </p>
                    <a href="{{ route('mantenimientos.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-flex items-center transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Registrar Mantenimiento
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
