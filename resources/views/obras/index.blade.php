@extends('layouts.app')

@section('title', 'Obras')

@section('header', 'Gestión de Obras')

@section('content')
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
        ['label' => 'Obras']
    ]" />

    <!-- Encabezado con botón de agregar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Listado de Obras</h2>
        <div class="flex space-x-3">
            @hasPermission('crear_obras')
            <a href="{{ route('obras.create') }}" class="bg-petroyellow hover:bg-yellow-500 text-petrodark font-medium py-2 px-4 rounded-md flex items-center transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Agregar Obra
            </a>
            @endhasPermission
        </div>
    </div>

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

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Estadísticas -->
    @if(isset($estadisticas))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Obras</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $estadisticas['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Activas</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $estadisticas['activas'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">En Progreso</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $estadisticas['en_progreso'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-100 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Finalizadas</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $estadisticas['finalizadas'] }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros y búsqueda -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('obras.index') }}" id="filtrosForm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre de obra..." class="pl-10 p-2 border border-gray-300 rounded-md w-full">
                    </div>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estatus</label>
                    <select id="estado" name="estatus" class="p-2 border border-gray-300 rounded-md w-full">
                        <option value="">Todos los estatus</option>
                        <option value="completada" {{ request('estatus') == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="en_progreso" {{ request('estatus') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="pausada" {{ request('estatus') == 'pausada' ? 'selected' : '' }}>Pausada</option>
                        <option value="cancelada" {{ request('estatus') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="flex-1 md:flex-none md:w-48">
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="p-2 border border-gray-300 rounded-md w-full">
                </div>
                <div class="flex-1 md:flex-none">
                    <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                    <div class="flex gap-2">
                        @if(request()->hasAny(['search', 'estatus', 'fecha_inicio']))
                            <a href="{{ route('obras.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded transition duration-200">
                                Limpiar
                            </a>
                        @endif
                        
                        <!-- Botones de exportación -->
                        <button type="button" onclick="descargarReporte('excel')" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded flex items-center gap-1 transition duration-200" title="Descargar Excel">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel
                        </button>
                        
                        <button type="button" onclick="descargarReporte('pdf')" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded flex items-center gap-1 transition duration-200" title="Descargar PDF">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>    <!-- Tabla de obras -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($obras->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avance</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($obras as $obra)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ str_pad($obra->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $obra->nombre_obra }}</div>
                                        @if($obra->descripcion)
                                            <div class="text-sm text-gray-500">{{ Str::limit($obra->descripcion, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $obra->ubicacion ?: 'No especificada' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($obra->estatus)
                                        @case('completada')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Completada
                                            </span>
                                            @break
                                        @case('en_progreso')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                En Progreso
                                            </span>
                                            @break
                                        @case('pausada')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pausada
                                            </span>
                                            @break
                                        @case('cancelada')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Cancelada
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Sin definir
                                            </span>
                                    @endswitch
                                </td>
                                <!-- Columna de Responsable -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <!-- Responsable de la Obra -->
                                        @if($obra->encargado)
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-xs font-medium text-purple-700">{{ $obra->encargado->nombre_completo }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-xs text-gray-400">Sin responsable</span>
                                            </div>
                                        @endif
                                        
                                        <!-- Vehículo y Operador (si están asignados) -->
                                        @if($obra->vehiculo_id && $obra->operador_id)
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                <span class="text-xs font-medium">{{ $obra->vehiculo->placa ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="text-xs">{{ $obra->operador->nombre_completo ?? 'N/A' }}</span>
                                            </div>
                                            @if($obra->fecha_liberacion)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Liberada
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activa
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <!-- Columna de Fechas -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <div class="text-xs">
                                            <span class="font-medium">Inicio:</span> {{ $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : '-' }}
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-medium">Fin:</span> {{ $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            @php $avance = $obra->avance ?? 0; @endphp
                                            <div class="bg-petroyellow h-2 rounded-full progress-bar" data-progress="{{ $avance }}"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $obra->avance ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @hasPermission('ver_obras')
                                        <a href="{{ route('obras.show', $obra) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        @endhasPermission
                                        @hasPermission('actualizar_obras')
                                        <a href="{{ route('obras.edit', $obra) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        @endhasPermission
                                        @hasPermission('eliminar_obras')
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900 eliminar-obra-btn" 
                                                title="Eliminar obra"
                                                data-obra-id="{{ $obra->id }}"
                                                data-obra-nombre="{{ $obra->nombre_obra }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        @endhasPermission
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Estado vacío dentro de la tabla -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avance</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay obras</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if(request()->hasAny(['search', 'estatus', 'fecha_inicio']))
                                        No se encontraron obras con los criterios especificados.
                                    @else
                                        Aún no hay obras registradas en el sistema.
                                    @endif
                                </p>
                                @if(request()->hasAny(['search', 'estatus', 'fecha_inicio']))
                                    <div class="mt-4">
                                        <a href="{{ route('obras.index') }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200">
                                            Limpiar filtros
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    <!-- Paginación -->
    @if(method_exists($obras, 'hasPages') && $obras->hasPages())
        <div class="mt-6">
            {{ $obras->withQueryString()->links() }}
        </div>
    @endif

    <!-- Modal de confirmación de eliminación -->
    <x-delete-confirmation-modal 
        id="modal-eliminar-obra"
        entity="la obra"
        entityIdField="obra-id"
        entityDisplayField="obra-nombre"
        additionalText="Esta acción eliminará la obra y todos sus registros asociados permanentemente."
    />
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de filtros
    const searchInput = document.getElementById('search');
    const estadoSelect = document.getElementById('estado');
    const fechaInput = document.getElementById('fecha_inicio');
    const form = document.getElementById('filtrosForm');
    
    // Verificar que todos los elementos existen
    if (!searchInput || !estadoSelect || !fechaInput || !form) {
        console.error('Algunos elementos del formulario no se encontraron:', {
            searchInput: !!searchInput,
            estadoSelect: !!estadoSelect,
            fechaInput: !!fechaInput,
            form: !!form
        });
        return;
    }
    
    // Variable para prevenir envíos múltiples
    let isSubmitting = false;
    
    // Función para enviar el formulario automáticamente
    function autoSubmit() {
        if (isSubmitting) return;
        isSubmitting = true;
        
        // Mostrar indicador de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtrando...';
        }
        
        form.submit();
    }
    
    // Event listeners para filtros automáticos
    if (estadoSelect) {
        estadoSelect.addEventListener('change', autoSubmit);
    }
    
    if (fechaInput) {
        fechaInput.addEventListener('change', autoSubmit);
    }
    
    // Event listener para búsqueda con delay
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                autoSubmit();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    }
    
    // Prevenir envío múltiple del formulario
    form.addEventListener('submit', function() {
        if (isSubmitting) return;
        isSubmitting = true;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Filtrando...';
        }
    });
    
    // Initialize progress bars
    document.querySelectorAll('.progress-bar').forEach(function(progressBar) {
        const progress = progressBar.getAttribute('data-progress');
        progressBar.style.width = progress + '%';
    });

    // Inicializar modal de eliminación para obras
    if (typeof window.initDeleteModal === 'function') {
        window.initDeleteModal({
            modalId: 'modal-eliminar-obra',
            entityIdField: 'obra-id',
            entityDisplayField: 'obra-nombre',
            deleteButtonSelector: '.eliminar-obra-btn',
            baseUrl: '/obras'
        });
        console.log('✅ Modal de eliminación inicializado para: modal-eliminar-obra');
    } else {
        console.error('❌ Función initDeleteModal no encontrada');
    }
});

function descargarReporte(tipo) {
    // Mostrar indicador de carga
    const tipoReporte = tipo === 'pdf' ? 'PDF' : 'Excel';
    
    // Obtener los parámetros de filtro actuales del formulario
    const filtrosForm = document.getElementById('filtrosForm');
    const formData = new FormData(filtrosForm);
    
    // Crear URL con parámetros
    let url;
    if (tipo === 'pdf') {
        url = '{{ route("obras.descargar-pdf") }}';
    } else {
        url = '{{ route("obras.descargar-excel") }}';
    }
    
    // Construir query string con los filtros actuales
    const params = new URLSearchParams();
    
    // Obtener valores específicos de los filtros
    const buscar = document.querySelector('input[name="search"]')?.value?.trim() || '';
    const estatus = document.querySelector('select[name="estatus"]')?.value || '';
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]')?.value || '';
    const soloActivas = document.querySelector('input[name="solo_activas"]')?.checked || false;
    
    // Agregar parámetros solo si tienen valor real (no vacío)
    if (buscar && buscar !== '') params.append('buscar', buscar);
    if (estatus && estatus !== '') params.append('estatus', estatus);
    if (fechaInicio && fechaInicio !== '') params.append('fecha_inicio', fechaInicio);
    if (soloActivas) params.append('solo_activas', 'true');
    
    // Crear URL completa
    const urlConParametros = url + '?' + params.toString();
    
    // Usar window.open para descargar sin afectar la página actual
    const ventanaDescarga = window.open(urlConParametros, '_blank');
    
    // Cerrar la ventana después de un breve momento (la descarga ya habrá iniciado)
    if (ventanaDescarga) {
        setTimeout(() => {
            ventanaDescarga.close();
        }, 1000);
    }
}
</script>
@endpush
