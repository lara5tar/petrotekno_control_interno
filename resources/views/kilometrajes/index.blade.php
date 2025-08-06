@extends('layouts.app')

@section('title', 'Kilometrajes')

@section('header', 'Control de Kilometrajes')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Kilometrajes']
    ]" />

    <!-- Encabezado con botones de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Control de Kilometrajes</h2>
        @can('crear_kilometrajes')
        <div class="flex gap-2">
            <a href="{{ route('kilometrajes.create') }}" 
               class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nuevo Registro
            </a>
            @can('ver_kilometrajes')
            <a href="{{ route('kilometrajes.alertas') }}" 
               class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Alertas
            </a>
            @endcan
        </div>
        @endcan
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

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('kilometrajes.index') }}" id="filtrosForm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <label for="buscar" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="buscar" 
                               name="buscar" 
                               value="{{ request('buscar') }}"
                               placeholder="Buscar por observaciones, marca, modelo..." 
                               class="pl-10 p-2 border border-gray-300 rounded-md w-full">
                    </div>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="vehiculo_id" class="block text-sm font-medium text-gray-700 mb-1">Vehículo</label>
                    <select id="vehiculo_id" 
                            name="vehiculo_id"
                            class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todos los vehículos</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                {{ $vehiculo->placas }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="obra_id" class="block text-sm font-medium text-gray-700 mb-1">Obra</label>
                    <select id="obra_id" 
                            name="obra_id"
                            class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todas las obras</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                {{ $obra->nombre_obra }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-4 mt-4">
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" 
                           id="fecha_inicio" 
                           name="fecha_inicio" 
                           value="{{ request('fecha_inicio') }}"
                           class="p-2 border border-gray-300 rounded-md w-full">
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" 
                           id="fecha_fin" 
                           name="fecha_fin" 
                           value="{{ request('fecha_fin') }}"
                           class="p-2 border border-gray-300 rounded-md w-full">
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded transition duration-200">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['buscar', 'vehiculo_id', 'obra_id', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('kilometrajes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de kilometrajes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($kilometrajes->count() > 0)
                        @foreach($kilometrajes as $kilometraje)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $kilometraje->vehiculo->placas }}</div>
                                            <div class="text-sm text-gray-500">{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ number_format($kilometraje->kilometraje) }} km
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $kilometraje->fecha_captura->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $kilometraje->usuarioCaptura->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($kilometraje->obra)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ Str::limit($kilometraje->obra->nombre_obra, 20) }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Sin obra</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($kilometraje->observaciones)
                                        <div class="max-w-xs truncate">{{ $kilometraje->observaciones }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @can('ver_kilometrajes')
                                        <a href="{{ route('kilometrajes.show', $kilometraje) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        @endcan
                                        @can('editar_kilometrajes')
                                        <a href="{{ route('kilometrajes.edit', $kilometraje) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        @endcan
                                        @can('eliminar_kilometrajes')
                                        <form action="{{ route('kilometrajes.destroy', $kilometraje) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este registro de kilometraje? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Estado vacío dentro de la tabla -->
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay registros de kilometraje</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if(request()->hasAny(['buscar', 'vehiculo_id', 'obra_id', 'fecha_inicio', 'fecha_fin']))
                                            No se encontraron registros con los criterios especificados.
                                        @else
                                            Aún no hay registros de kilometraje en el sistema.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['buscar', 'vehiculo_id', 'obra_id', 'fecha_inicio', 'fecha_fin']))
                                        <div class="mt-4">
                                            <a href="{{ route('kilometrajes.index') }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200">
                                                Limpiar filtros
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(method_exists($kilometrajes, 'hasPages') && $kilometrajes->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Siguiente
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ $kilometrajes->firstItem() }}</span> a <span class="font-medium">{{ $kilometrajes->lastItem() }}</span> de <span class="font-medium">{{ $kilometrajes->total() }}</span> resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{ $kilometrajes->appends(request()->query())->links() }}
                            </nav>
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
    // Elementos del formulario
    const buscarInput = document.getElementById('buscar');
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const obraSelect = document.getElementById('obra_id');
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');
    const form = document.getElementById('filtrosForm');
    
    // Función para enviar el formulario automáticamente
    function autoSubmit() {
        form.submit();
    }
    
    // Event listeners para filtros automáticos
    vehiculoSelect.addEventListener('change', autoSubmit);
    obraSelect.addEventListener('change', autoSubmit);
    fechaInicioInput.addEventListener('change', autoSubmit);
    fechaFinInput.addEventListener('change', autoSubmit);
    
    // Event listener para búsqueda con delay
    let searchTimeout;
    buscarInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            autoSubmit();
        }, 500); // Esperar 500ms después de que el usuario deje de escribir
    });
    
    // Prevenir envío múltiple del formulario
    form.addEventListener('submit', function() {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtrando...';
            setTimeout(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Filtrar';
            }, 2000);
        }
    });
});
</script>
@endpush
