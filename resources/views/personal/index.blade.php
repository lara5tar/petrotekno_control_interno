@extends('layouts.app')

@section('title', 'Personal')

@section('header', 'Gestión de Personal')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Personal']
    ]" />

    <!-- Encabezado con botones de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Listado de Personal</h2>
        <div class="flex space-x-3">
            @hasPermission('crear_personal')
            <a href="{{ route('personal.create') }}" 
               class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Agregar Personal
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

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('personal.index') }}" id="filtrosForm">
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
                               placeholder="Buscar por nombre, categoría, RFC, NSS..." 
                               class="pl-10 pr-10 p-2 border border-gray-300 rounded-lg w-full h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               autocomplete="off">
                        
                        <!-- Loading indicator -->
                        <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none hidden">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        
                        <!-- Clear button -->
                        <button type="button" 
                                id="clear-search" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200 hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estado" 
                            name="estatus"
                            class="p-2 border border-gray-300 rounded-md w-full h-10">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select id="categoria" 
                            name="categoria_id"
                            class="p-2 border border-gray-300 rounded-md w-full h-10">
                        <option value="">Todos</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request()->hasAny(['buscar', 'categoria_id', 'estatus']))
                <div class="flex flex-col">
                    <!-- Label invisible para alineación -->
                    <label class="block text-sm font-medium text-gray-700 mb-1 invisible">Acciones</label>
                    <a href="{{ route('personal.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200 h-10 flex items-center justify-center">
                        Limpiar
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de personal -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table id="personal-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Empleado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($personal->count() > 0)
                        @foreach($personal as $persona)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $persona->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $persona->nombre_completo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $persona->categoria->nombre_categoria ?? 'Sin categoría' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($persona->estatus === 'activo')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                    @elseif($persona->estatus === 'inactivo')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($persona->estatus) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $persona->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @hasPermission('ver_personal')
                                        <a href="{{ route('personal.show', $persona->id) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        @endhasPermission
                                        @hasPermission('editar_personal')
                                        <a href="{{ route('personal.edit', $persona->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        @endhasPermission
                                        @hasPermission('eliminar_personal')
                                        <form action="{{ route('personal.destroy', $persona->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a {{ $persona->nombre_completo }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endhasPermission
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <!-- Estado vacío dentro de la tabla -->
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay personal</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if(request()->hasAny(['buscar', 'categoria_id', 'estatus']))
                                            No se encontró personal con los criterios especificados.
                                        @else
                                            Aún no hay personal registrado en el sistema.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['buscar', 'categoria_id', 'estatus']))
                                        <div class="mt-4">
                                            <a href="{{ route('personal.index') }}" 
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
        @if(method_exists($personal, 'hasPages') && $personal->hasPages())
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
                                Mostrando <span class="font-medium">{{ $personal->firstItem() }}</span> a <span class="font-medium">{{ $personal->lastItem() }}</span> de <span class="font-medium">{{ $personal->total() }}</span> resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{ $personal->appends(request()->query())->links() }}
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
    console.log('🚀 Personal page loaded - SIMPLIFIED VERSION');
    
    // VERSIÓN ULTRA SIMPLE - SOLO FILTROS BÁSICOS
    const estadoSelect = document.getElementById('estado');
    const categoriaSelect = document.getElementById('categoria');
    const filtrosForm = document.getElementById('filtrosForm');
    const searchInput = document.getElementById('buscar');
    
    console.log('Elements found:', {
        estadoSelect: !!estadoSelect,
        categoriaSelect: !!categoriaSelect,
        filtrosForm: !!filtrosForm,
        searchInput: !!searchInput
    });
    
    // Función para limpiar campos vacíos antes de enviar
    function limpiarCamposVacios() {
        // Si el campo de búsqueda está vacío, removerlo del formulario temporalmente
        if (searchInput && searchInput.value.trim() === '') {
            searchInput.removeAttribute('name');
        } else if (searchInput) {
            searchInput.setAttribute('name', 'buscar');
        }
        
        // Si estado está en "Todos", removerlo
        if (estadoSelect && estadoSelect.value === '') {
            estadoSelect.removeAttribute('name');
        } else if (estadoSelect) {
            estadoSelect.setAttribute('name', 'estatus');
        }
        
        // Si categoría está en "Todos", removerlo
        if (categoriaSelect && categoriaSelect.value === '') {
            categoriaSelect.removeAttribute('name');
        } else if (categoriaSelect) {
            categoriaSelect.setAttribute('name', 'categoria_id');
        }
    }
    
    // Función simple: limpiar y submit del formulario
    function aplicarFiltros() {
        console.log('📤 Submitting form with filters...');
        console.log('Estado:', estadoSelect ? estadoSelect.value : 'N/A');
        console.log('Categoría:', categoriaSelect ? categoriaSelect.value : 'N/A');
        
        if (filtrosForm) {
            limpiarCamposVacios();
            filtrosForm.submit();
        }
    }
    
    // Event listener para estado
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            console.log('📊 Estado changed to:', this.value);
            aplicarFiltros();
        });
    }
    
    // Event listener para categoría
    if (categoriaSelect) {
        categoriaSelect.addEventListener('change', function() {
            console.log('👥 Categoría changed to:', this.value);
            aplicarFiltros();
        });
    }
    
    // Búsqueda básica (solo input, sin AJAX)
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            const value = this.value;
            console.log('🔍 Search input:', value);
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Simple debounce - submit form after 1 second of no typing
            searchTimeout = setTimeout(function() {
                if (value.length >= 2 || value.length === 0) {
                    console.log('📤 Submitting search form...');
                    limpiarCamposVacios();
                    filtrosForm.submit();
                }
            }, 1000);
        });
        
        // Submit on Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                console.log('⏎ Enter pressed, submitting form...');
                limpiarCamposVacios();
                filtrosForm.submit();
            }
        });
    }
    
    // Event listener para el botón Filtrar
    // REMOVIDO: Ya no necesitamos botón filtrar - filtrado automático
    
    console.log('✅ Simple filters initialized');
});
</script>
@endpush
