@extends('layouts.app')

@section('title', 'Vehículos')

@section('header', 'Gestión de Vehículos')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Vehículos']
    ]" />

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Mensaje de error --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Encabezado con botón de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Listado de Vehículos</h2>
        @hasPermission('crear_vehiculos')
        <a href="{{ route('vehiculos.create') }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Agregar Vehículo
        </a>
        @endhasPermission
    </div>
    
    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('vehiculos.index') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar por placa, modelo, marca, serie..." class="pl-10 p-2 border border-gray-300 rounded-md w-full">
                </div>
            </div>
            <div class="flex-1 md:flex-none md:w-48">
                <label for="estatus_id" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="estatus_id" name="estatus_id" class="p-2 border border-gray-300 rounded-md w-full">
                    <option value="">Todos los estados</option>
                    @foreach($estatus as $estado)
                        <option value="{{ $estado->id }}" {{ request('estatus_id') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre_estatus }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 md:flex-none md:w-48">
                <label for="marca" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <input type="text" id="marca" name="marca" value="{{ request('marca') }}" placeholder="Filtrar por marca" class="p-2 border border-gray-300 rounded-md w-full">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Filtrar
                </button>
                <a href="{{ route('vehiculos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Limpiar
                </a>
            </div>
        </form>
    </div>
    
    <!-- Tabla de vehículos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Modelo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vehiculos as $vehiculo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $vehiculo->placas }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehiculo->anio }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                // Asignar colores basándose en el nombre del estatus
                                $nombreEstatus = strtolower($vehiculo->estatus->nombre_estatus);
                                if (in_array($nombreEstatus, ['disponible', 'asignado', 'activo'])) {
                                    $colorClass = 'bg-green-100 text-green-800';
                                } elseif (in_array($nombreEstatus, ['mantenimiento', 'reparación', 'reparacion'])) {
                                    $colorClass = 'bg-yellow-100 text-yellow-800';
                                } else {
                                    // Para estados como: fuera_servicio, baja, accidentado, inactivo
                                    $colorClass = 'bg-red-100 text-red-800';
                                }
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                {{ $vehiculo->estatus->nombre_estatus }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @hasPermission('ver_vehiculos')
                                <a href="{{ route('vehiculos.show', $vehiculo->id) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endhasPermission
                                @hasPermission('editar_vehiculos')
                                <a href="{{ route('vehiculos.edit', $vehiculo->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar vehículo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                @endhasPermission
                                @hasPermission('eliminar_vehiculos')
                                <button data-vehiculo-id="{{ $vehiculo->id }}" data-vehiculo-placas="{{ $vehiculo->placas }}" class="btn-eliminar text-red-600 hover:text-red-900" title="Eliminar vehículo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                @endhasPermission
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron vehículos.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($vehiculos->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($vehiculos->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                            Anterior
                        </span>
                    @else
                        <a href="{{ $vehiculos->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                    @endif

                    @if($vehiculos->hasMorePages())
                        <a href="{{ $vehiculos->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Siguiente
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                            Siguiente
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $vehiculos->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $vehiculos->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $vehiculos->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $vehiculos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar event listeners a todos los botones de eliminar
        const botonesEliminar = document.querySelectorAll('.btn-eliminar');
        
        botonesEliminar.forEach(function(boton) {
            boton.addEventListener('click', function() {
                const vehiculoId = this.getAttribute('data-vehiculo-id');
                const vehiculoPlacas = this.getAttribute('data-vehiculo-placas');
                confirmarEliminacion(vehiculoId, vehiculoPlacas);
            });
        });
    });

    function confirmarEliminacion(id, placas) {
        if (confirm(`¿Estás seguro de que deseas eliminar el vehículo con placas ${placas}?`)) {
            // Simulación de eliminación (solo frontend)
            alert('Vehículo eliminado exitosamente (simulación)');
            // En una implementación real, aquí se haría la petición al servidor
        }
    }
</script>
@endpush