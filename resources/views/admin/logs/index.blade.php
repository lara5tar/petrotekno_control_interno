@extends('layouts.app')

@section('title', 'Logs del Sistema')

@section('header', 'Logs del Sistema')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Configuración', 'url' => route('admin.configuracion.index')],
        ['label' => 'Logs del Sistema']
    ]" />

    <!-- Encabezado con botón de volver -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Logs del Sistema</h2>
        <a href="{{ route('admin.configuracion.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver a Configuración
        </a>
    </div>

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('admin.logs.index') }}" id="filtrosForm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Filtro por Usuario -->
                <div class="flex-1">
                    <label for="usuario_id" class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <select name="usuario_id" id="usuario_id" class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->personal ? $usuario->personal->nombre : $usuario->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Acción -->
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="accion" class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                    <select name="accion" id="accion" class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todas las acciones</option>
                        @foreach($acciones as $accion)
                            <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $accion)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Tabla -->
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="tabla_afectada" class="block text-sm font-medium text-gray-700 mb-1">Tabla</label>
                    <select name="tabla_afectada" id="tabla_afectada" class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todas las tablas</option>
                        @foreach($tablas as $tabla)
                            <option value="{{ $tabla }}" {{ request('tabla_afectada') == $tabla ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $tabla)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Fecha Desde -->
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" 
                           class="p-2 border border-gray-300 rounded-md w-full">
                </div>

                <!-- Filtro por Fecha Hasta -->
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                           class="p-2 border border-gray-300 rounded-md w-full">
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex gap-2 mt-4">
                <button type="submit" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200">
                    Filtrar
                </button>
                @if(request()->hasAny(['usuario_id', 'accion', 'tabla_afectada', 'fecha_desde', 'fecha_hasta']))
                    <a href="{{ route('admin.logs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de logs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tabla</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $log->usuario && $log->usuario->personal ? $log->usuario->personal->nombre : ($log->usuario->email ?? 'Sistema') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $log->usuario ? $log->usuario->email : 'admin@petrotekno.com' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $accionColor = match($log->accion) {
                                    'crear_vehiculo', 'crear_personal', 'crear_obra' => 'bg-green-100 text-green-800',
                                    'actualizar_vehiculo', 'actualizar_personal', 'actualizar_obra', 'actualizar_mantenimiento' => 'bg-blue-100 text-blue-800',
                                    'eliminar_vehiculo', 'eliminar_personal', 'eliminar_obra' => 'bg-red-100 text-red-800',
                                    'login' => 'bg-purple-100 text-purple-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $accionColor }}">
                                {{ ucfirst(str_replace('_', ' ', $log->accion)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->tabla_afectada }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $log->registro_id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate">{{ $log->detalles }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay registros de logs</h3>
                            <p class="text-gray-600">No se encontraron registros que coincidan con los filtros aplicados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($logs->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($logs->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                                Anterior
                            </span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Anterior
                            </a>
                        @endif

                        @if($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Siguiente
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                                Siguiente
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando 
                                <span class="font-medium">{{ $logs->firstItem() }}</span>
                                a 
                                <span class="font-medium">{{ $logs->lastItem() }}</span>
                                de 
                                <span class="font-medium">{{ $logs->total() }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection