@extends('layouts.app')

@section('title', 'Detalles del Tipo de Activo')

@section('header', 'Detalles del Tipo de Activo')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Tipos de Activos', 'url' => route('tipos-activos.index')],
        ['label' => $tipoActivo->nombre]
    ]" />

    <!-- Encabezado con botones de acción -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $tipoActivo->nombre }}</h2>
        <div class="flex space-x-3">
            <a href="{{ route('tipos-activos.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver al Listado
            </a>
            @hasPermission('editar_catalogos')
            <a href="{{ route('tipos-activos.edit', $tipoActivo) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Editar Tipo de Activo
            </a>
            @endhasPermission
        </div>
    </div>

    <!-- Alertas de sesión -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                    Información del Tipo de Activo
                </h3>

                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tipoActivo->id }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre del Tipo</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $tipoActivo->nombre }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Maneja Kilometraje</dt>
                        <dd class="mt-1">
                            @if($tipoActivo->tiene_kilometraje)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Sí, registra kilometraje
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    No registra kilometraje
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Activos Asignados</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $tipoActivo->vehiculos_count }} {{ $tipoActivo->vehiculos_count == 1 ? 'activo' : 'activos' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tipoActivo->created_at->format('d/m/Y H:i') }}</dd>
                    </div>

                    @if($tipoActivo->created_at != $tipoActivo->updated_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tipoActivo->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Lista de Activos Asignados -->
            @if($tipoActivo->vehiculos_count > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z" />
                    </svg>
                    Activos de este Tipo ({{ $tipoActivo->vehiculos_count }})
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placas/Serie</th>
                                @if($tipoActivo->tiene_kilometraje)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tipoActivo->vehiculos as $vehiculo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</div>
                                        <div class="text-sm text-gray-500">Año: {{ $vehiculo->anio }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $vehiculo->placas ?: 'N/A' }}
                                    </td>
                                    @if($tipoActivo->tiene_kilometraje)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($vehiculo->estatus === 'activo')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        @elseif($vehiculo->estatus === 'mantenimiento')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Mantenimiento
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ ucfirst($vehiculo->estatus) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('vehiculos.show', $vehiculo) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition duration-200" 
                                           title="Ver detalles del activo">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($tipoActivo->vehiculos_count > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('vehiculos.index', ['tipo_activo_id' => $tipoActivo->id]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Ver todos los activos de este tipo
                        </a>
                    </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Panel de Acciones -->
        <div class="space-y-6">
            <!-- Estadísticas -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                    Estadísticas
                </h3>

                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $tipoActivo->vehiculos_count }}</div>
                        <div class="text-sm text-gray-500">{{ $tipoActivo->vehiculos_count == 1 ? 'Activo Asignado' : 'Activos Asignados' }}</div>
                    </div>

                    @if($tipoActivo->tiene_kilometraje && $tipoActivo->vehiculos_count > 0)
                        @php
                            $activosConKm = $tipoActivo->vehiculos->where('kilometraje_actual', '>', 0);
                            $totalKm = $activosConKm->sum('kilometraje_actual');
                            $promedioKm = $activosConKm->count() > 0 ? $totalKm / $activosConKm->count() : 0;
                        @endphp
                        <div class="border-t pt-4">
                            <div class="text-center mb-2">
                                <div class="text-2xl font-bold text-green-600">{{ number_format($totalKm) }}</div>
                                <div class="text-sm text-gray-500">Kilómetros Totales</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-yellow-600">{{ number_format($promedioKm) }}</div>
                                <div class="text-sm text-gray-500">Promedio por Activo</div>
                            </div>
                        </div>
                    @endif
                </div>

                @if($tipoActivo->vehiculos_count > 0)
                    <div class="mt-4">
                        <a href="{{ route('vehiculos.index', ['tipo_activo_id' => $tipoActivo->id]) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ver Activos de este Tipo
                        </a>
                    </div>
                @endif
            </div>

            <!-- Acciones -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Acciones Disponibles
                </h3>

                <div class="space-y-3">
                    @hasPermission('editar_catalogos')
                    <a href="{{ route('tipos-activos.edit', $tipoActivo) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Editar Tipo de Activo
                    </a>
                    @endhasPermission

                    @hasPermission('eliminar_catalogos')
                    @if($tipoActivo->vehiculos_count == 0)
                    <form action="{{ route('tipos-activos.destroy', $tipoActivo) }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar el tipo de activo {{ $tipoActivo->nombre }}? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Eliminar Tipo de Activo
                        </button>
                    </form>
                    @else
                    <div class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-gray-100 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        No se puede eliminar (tiene activos asignados)
                    </div>
                    @endif
                    @endhasPermission
                </div>
            </div>
        </div>
    </div>
@endsection