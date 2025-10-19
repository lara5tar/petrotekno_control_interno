@props([
    'filtros' => [],
    'rutaLimpiar' => null,
    'titulo' => 'Filtros Aplicados'
])

@php
    $filtrosActivos = collect($filtros)->filter(function($valor, $clave) {
        return !empty($valor) && $valor !== null;
    });
@endphp

@if($filtrosActivos->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-gray-100 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">{{ $titulo }}</h3>
                        <p class="text-xs text-gray-500">{{ $filtrosActivos->count() }} {{ $filtrosActivos->count() === 1 ? 'filtro activo' : 'filtros activos' }} en este reporte</p>
                    </div>
                </div>
                @if($rutaLimpiar)
                    <a href="{{ $rutaLimpiar }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar filtros
                    </a>
                @endif
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($filtrosActivos as $tipo => $valor)
                    @switch($tipo)
                        @case('estatus')
                        @case('estado')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @switch($valor)
                                    @case('disponible')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('asignado')
                                        bg-blue-100 text-blue-800
                                        @break
                                    @case('en_mantenimiento')
                                        bg-yellow-100 text-yellow-800
                                        @break
                                    @case('fuera_de_servicio')
                                        bg-orange-100 text-orange-800
                                        @break
                                    @case('baja')
                                        bg-red-100 text-red-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Estado: 
                                @switch($valor)
                                    @case('disponible')
                                        Disponible
                                        @break
                                    @case('asignado')
                                        Asignado
                                        @break
                                    @case('en_mantenimiento')
                                        En Mantenimiento
                                        @break
                                    @case('fuera_de_servicio')
                                        Fuera de Servicio
                                        @break
                                    @case('baja')
                                        Dado de Baja
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $valor)) }}
                                @endswitch
                            </span>
                            @break

                        @case('marca')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Marca: {{ $valor }}
                            </span>
                            @break

                        @case('anio')
                        @case('año')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Año: {{ $valor }}
                            </span>
                            @break

                        @case('fecha_inicio')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Desde: {{ \Carbon\Carbon::parse($valor)->format('d/m/Y') }}
                            </span>
                            @break

                        @case('fecha_fin')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Hasta: {{ \Carbon\Carbon::parse($valor)->format('d/m/Y') }}
                            </span>
                            @break

                        @case('obra')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-cyan-100 text-cyan-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Obra: {{ $valor }}
                            </span>
                            @break

                        @case('departamento')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Departamento: {{ $valor }}
                            </span>
                            @break

                        @default
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ ucfirst(str_replace('_', ' ', $tipo)) }}: {{ $valor }}
                            </span>
                    @endswitch
                @endforeach
            </div>
            @if($filtrosActivos->count() > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Los datos mostrados están filtrados según los criterios seleccionados arriba.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endif
