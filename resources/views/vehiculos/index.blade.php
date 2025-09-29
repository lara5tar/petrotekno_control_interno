@extends('layouts.app')

@section('title', 'Activos')

@section('header', 'Gestión de Activos')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Activos']
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
        <h2 class="text-2xl font-bold text-gray-800">Listado de Activos</h2>

        @php
            $user = auth()->user();
            $canCreateVehicles = $user && $user->hasPermission('crear_vehiculos');
        @endphp

        @if($canCreateVehicles)
        <a href="{{ route('vehiculos.create') }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Agregar Activo
        </a>
        @elseif(auth()->check() && auth()->user()->email === 'admin@petrotekno.com')
        <!-- Botón de emergencia para admin -->
        <a href="{{ route('vehiculos.create') }}" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            ADMIN: Agregar Activo
        </a>
        @endif

    </div>
    
    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('vehiculos.index') }}" id="filtrosForm" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
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
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="estado" name="estado" class="p-2 border border-gray-300 rounded-md w-full">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estadoKey => $estadoNombre)
                        <option value="{{ $estadoKey }}" {{ request('estado') == $estadoKey ? 'selected' : '' }}>
                            {{ $estadoNombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md transition duration-200">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'estado']))
                    <a href="{{ route('vehiculos.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>
        
        <!-- Botones de descarga de reportes -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $vehiculos->total() }}</span> vehículos encontrados
                    @if(request()->hasAny(['search', 'estado']))
                        <span class="text-gray-500">(filtrados)</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <!-- Botón de descarga PDF -->
                    <button onclick="descargarReporte('pdf')" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar PDF
                    </button>
                    
                    <!-- Botón de descarga Excel -->
                    <button onclick="descargarReporte('excel')" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabla de activos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Activo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Serie</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vehiculos as $vehiculo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehiculo->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ !empty($vehiculo->marca) ? $vehiculo->marca : 'Sin marca' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ !empty($vehiculo->modelo) ? $vehiculo->modelo : 'Sin modelo' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ !empty($vehiculo->anio) ? $vehiculo->anio : 'Sin año' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehiculo->tipoActivo ? $vehiculo->tipoActivo->nombre : 'No asignado' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ !empty($vehiculo->placas) ? $vehiculo->placas : 'Sin placas' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ !empty($vehiculo->n_serie) ? $vehiculo->n_serie : 'Sin serie' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                // Asignar colores basándose en el estado del activo
                                $estadoActivo = $vehiculo->estatus;
                                if ($estadoActivo === 'disponible') {
                                    $colorClass = 'bg-green-100 text-green-800';
                                } elseif ($estadoActivo === 'asignado') {
                                    $colorClass = 'bg-blue-100 text-blue-800';
                                } elseif ($estadoActivo === 'en_mantenimiento') {
                                    $colorClass = 'bg-yellow-100 text-yellow-800';
                                } elseif ($estadoActivo === 'fuera_de_servicio') {
                                    $colorClass = 'bg-orange-100 text-orange-800';
                                } elseif ($estadoActivo === 'baja') {
                                    $colorClass = 'bg-red-100 text-red-800';
                                } else {
                                    // Valor por defecto para estados desconocidos
                                    $colorClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                {{ $vehiculo->estatus->nombre() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @hasPermission('ver_vehiculos')
                                <a href="{{ route('vehiculos.show', ['vehiculo' => $vehiculo]) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endhasPermission
                                @hasPermission('editar_vehiculos')
                                <a href="{{ route('vehiculos.edit', ['vehiculo' => $vehiculo]) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar activo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                @endhasPermission
                                
                                @hasPermission('eliminar_vehiculos')
                                <button data-activo-id="{{ $vehiculo->id }}" data-activo-placas="{{ !empty($vehiculo->placas) ? $vehiculo->placas : 'Sin placas' }}" class="btn-eliminar text-red-600 hover:text-red-900" title="Eliminar activo">
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
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron activos.
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
        // Elementos del formulario de filtros
        const searchInput = document.getElementById('search');
        const estadoSelect = document.getElementById('estado');
        const marcaInput = document.getElementById('marca');
        const form = document.getElementById('filtrosForm');
        
        // Función para enviar el formulario automáticamente
        function autoSubmit() {
            form.submit();
        }
        
        // Event listeners para filtros automáticos
        estadoSelect.addEventListener('change', autoSubmit);
        
        // Event listener para búsqueda con delay
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                autoSubmit();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
        
        // Event listener para marca con delay
        let marcaTimeout;
        marcaInput.addEventListener('input', function() {
            clearTimeout(marcaTimeout);
            marcaTimeout = setTimeout(function() {
                autoSubmit();
            }, 500);
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

        // Agregar event listeners a todos los botones de eliminar
        const botonesEliminar = document.querySelectorAll('.btn-eliminar');
        
        botonesEliminar.forEach(function(boton) {
            boton.addEventListener('click', function() {
                const activoId = this.getAttribute('data-activo-id');
                const activoPlacas = this.getAttribute('data-activo-placas');
                confirmarEliminacion(activoId, activoPlacas);
            });
        });
    });

    // Función para descargar reportes manteniendo los filtros aplicados
    function descargarReporte(tipo) {
        // Obtener los parámetros de filtro actuales
        const urlParams = new URLSearchParams(window.location.search);
        const filtros = {};
        
        // Capturar filtros de la URL
        if (urlParams.get('search')) {
            filtros.search = urlParams.get('search');
        }
        if (urlParams.get('estado')) {
            filtros.estado = urlParams.get('estado');
        }
        
        // Construir la URL de descarga
        let url;
        if (tipo === 'pdf') {
            url = '{{ route("vehiculos.descargar-reporte-pdf") }}';
        } else if (tipo === 'excel') {
            url = '{{ route("vehiculos.descargar-reporte-excel") }}';
        }
        
        // Agregar parámetros de filtro a la URL
        const params = new URLSearchParams(filtros);
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        // Mostrar indicador de carga
        const boton = event.target.closest('button');
        const textoOriginal = boton.innerHTML;
        boton.disabled = true;
        boton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Generando...
        `;
        
        // Crear enlace temporal para descarga
        const link = document.createElement('a');
        link.href = url;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Restaurar botón después de un breve delay
        setTimeout(() => {
            boton.disabled = false;
            boton.innerHTML = textoOriginal;
        }, 2000);
    }
    function confirmarEliminacion(id, placas) {
        if (confirm(`¿Estás seguro de que deseas eliminar el activo con placas ${placas}?`)) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/vehiculos/${id}`;
            
            // Agregar CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);
            
            // Agregar método DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Agregar al DOM y enviar
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush