@extends('layouts.app')

@section('title', 'Detalles del Vehículo')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Vehículos', 'url' => route('vehiculos.index')],
    ['label' => 'Detalle del Vehículo']
]" />

<div class="h-[calc(100vh-120px)] flex flex-col gap-4">

    <!-- Contenido Principal en Grid 1/3 - 2/3 -->
    <div class="flex-1 grid grid-cols-3 gap-4">
        <!-- Panel Izquierdo - 1/3 del espacio -->
        <div class="flex flex-col gap-4">
            <!-- Datos Generales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                </div>
                <div class="p-4">
                    <!-- Datos del vehículo primero -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Marca</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->marca ?? 'Nissan' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Modelo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->modelo ?? 'NP300' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Año</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->anio ?? '2023' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Placas</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->placas ?? 'NL-ABC-1234' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Serie</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->n_serie ?? 'NS123456789' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Derecho Vehicular</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $vehiculo->derecho_vehicular ?? 'DV-2025-001234' }}
                                </div>
                                <button onclick="viewDocument('derecho_vehicular.pdf')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-2 rounded text-sm transition duration-200"
                                        title="Ver documento">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Póliza de Seguro</label>
                            <div class="flex items-center space-x-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                    {{ $vehiculo->poliza_seguro ?? 'PS-2025-567890' }}
                                </div>
                                <button onclick="viewDocument('poliza_seguro.pdf')" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-2 rounded text-sm transition duration-200"
                                        title="Ver documento">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Fotografía del Vehículo ahora al final -->
                    <div class="mt-6">
                        <div class="relative">
                            @if(!empty($vehiculo->imagen) && $vehiculo->imagen !== null && $vehiculo->imagen !== '')
                                <div class="bg-gray-100 rounded overflow-hidden mb-2" id="image-container">
                                    <img src="{{ $vehiculo->imagen }}" 
                                         alt="Vehículo {{ $vehiculo->marca ?? 'Nissan' }} {{ $vehiculo->modelo ?? 'NP300' }}" 
                                         class="w-full h-auto object-contain max-h-64"
                                         id="vehicle-image"
                                         onload="handleImageLoad()"
                                         onerror="handleImageError()">
                                    <div class="absolute bottom-1 left-1 bg-black bg-opacity-75 text-white px-1 py-0.5 rounded text-xs">
                                        {{ now()->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="flex justify-center space-x-2" id="image-actions">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                        <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Descargar
                                    </button>
                                </div>
                                <!-- Placeholder para error de imagen (inicialmente oculto) -->
                                <div class="bg-red-50 border-2 border-dashed border-red-300 rounded mb-2 h-64 flex items-center justify-center hidden" id="image-error-placeholder">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-red-600 font-medium">No se encontró la imagen</p>
                                        <p class="text-xs text-red-500">Error al cargar la imagen del vehículo</p>
                                    </div>
                                </div>
                            @else
                                <!-- Placeholder para cuando no hay imagen -->
                                <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded mb-2 h-64 flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500 font-medium">Sin imagen disponible</p>
                                        <p class="text-xs text-gray-400">No se ha asignado una imagen a este vehículo</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Panel Derecho - 2/3 del espacio -->
        <div class="col-span-2 flex flex-col gap-4">
            <!-- Kilometraje y Estado - Pegados a los lados -->
            <div class="flex justify-between">
                <!-- Último Kilometraje -->
                <div class="flex-shrink-0">
                    <div class="text-base font-medium text-gray-600 mb-1">Último Kilometraje</div>
                    <div class="bg-gray-800 text-white p-2 rounded text-center min-w-[160px]">
                        <div class="text-lg font-bold">{{ number_format($vehiculo->kilometraje_actual ?? 0) }} km</div>
                    </div>
                </div>

                <!-- Estatus Actual -->
                <div class="flex-shrink-0">
                    <div class="text-base font-medium text-gray-600 mb-1">Estatus Actual</div>
                    @php
                        $statusColors = [
                            'disponible' => 'bg-green-500',
                            'asignado' => 'bg-blue-500',
                            'en_obra' => 'bg-yellow-500',
                            'mantenimiento' => 'bg-orange-500',
                            'fuera_servicio' => 'bg-red-500'
                        ];
                        // Asegurar que obtenemos el valor string del estatus
                        $estatusValue = is_object($vehiculo->estatus) 
                            ? $vehiculo->estatus->value 
                            : $vehiculo->estatus;
                        $currentStatus = $estatusValue ?? 'disponible';
                        $colorClass = $statusColors[$currentStatus] ?? 'bg-gray-500';
                    @endphp
                    <div class="{{ $colorClass }} text-white p-2 rounded text-center min-w-[160px]">
                        <div class="text-lg font-medium">{{ ucfirst(str_replace('_', ' ', $currentStatus)) }}</div>
                    </div>
                </div>
            </div>

            <!-- Pestañas de Información - Diseño tipo carpetas -->
            <div class="bg-white flex-1 flex flex-col" x-data="{ activeTab: 'operacion' }">
                <div class="relative">
                    <nav class="flex space-x-1 pr-3 pt-3">
                        <button @click="activeTab = 'operacion'" 
                                :class="activeTab === 'operacion' ? 'bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10' : 'bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200'"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 ml-0">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Operación
                            </span>
                        </button>
                        <button @click="activeTab = 'documentos'" 
                                :class="activeTab === 'documentos' ? 'bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10' : 'bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200'"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Documentos
                            </span>
                        </button>
                        <button @click="activeTab = 'mantenimientos'" 
                                :class="activeTab === 'mantenimientos' ? 'bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10' : 'bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200'"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Mantenimientos
                            </span>
                        </button>
                    </nav>
                    <!-- Línea base que conecta con las pestañas -->
                    <div class="absolute bottom-0 left-0 right-0 h-px bg-gray-300 z-0"></div>
                </div>

                <!-- Contenido de pestañas con bordes tipo carpeta -->
                <div class="flex-1 overflow-hidden bg-gray-50 border-l border-r border-b border-gray-300 rounded-b-lg">
                <div class="flex-1 overflow-hidden">
                    <!-- Contenido de Operación -->
                    <div x-show="activeTab === 'operacion'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Sección: Obra Actual -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm" id="obra-actual-section">
                                @php
                                    // Obtener la asignación activa actual del vehículo
                                    $asignacionActiva = null;
                                    
                                    // Verificar si el vehículo tiene el método asignacionesObraActivas
                                    if (method_exists($vehiculo, 'asignacionesObraActivas')) {
                                        try {
                                            $asignacionActiva = $vehiculo->asignacionesObraActivas()->with(['obra.encargado'])->first();
                                        } catch (\Exception $e) {
                                            // Si hay error, simplemente continuar sin asignación
                                            $asignacionActiva = null;
                                        }
                                    }
                                @endphp
                                
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Obra Actual
                                    </h5>
                                    @hasPermission('crear_asignaciones')
                                    <button onclick="openCambiarObraModal()" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        @if($asignacionActiva && $asignacionActiva->obra)
                                            Cambiar Obra
                                        @else
                                            Asignar Obra
                                        @endif
                                    </button>
                                    @endhasPermission
                                </div>
                                
                                @if($asignacionActiva && $asignacionActiva->obra)
                                <div class="space-y-4">
                                    <!-- Nombre y Ubicación de la Obra -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Nombre de la Obra</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $asignacionActiva->obra->nombre_obra }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Ubicación de la Obra</label>
                                            @if($asignacionActiva->obra->ubicacion)
                                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ $asignacionActiva->obra->ubicacion }}
                                                </div>
                                            @else
                                                <div class="bg-gray-400 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    Ubicación no especificada
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Responsable y Avance de la Obra -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Responsable de la Obra</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $asignacionActiva->obra->encargado ? $asignacionActiva->obra->encargado->nombre_completo : 'Sin responsable asignado' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Avance de la Obra</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                                <div class="flex-1">
                                                    {{ $asignacionActiva->obra->avance ?? 0 }}%
                                                </div>
                                                <div class="w-16 bg-gray-700 rounded-full h-2 ml-2">
                                                    <div class="bg-green-400 h-2 rounded-full" style="width: {{ $asignacionActiva->obra->avance ?? 0 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Fechas y Kilometrajes -->
                                    <div class="grid grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Fecha de Inicio</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $asignacionActiva->obra->fecha_inicio ? \Carbon\Carbon::parse($asignacionActiva->obra->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Fecha de Fin</label>
                                            <div class="bg-{{ $asignacionActiva->obra->fecha_fin && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($asignacionActiva->obra->fecha_fin)) ? 'red' : 'gray' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $asignacionActiva->obra->fecha_fin ? \Carbon\Carbon::parse($asignacionActiva->obra->fecha_fin)->format('d/m/Y') : 'No definida' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Kilometraje Inicial</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $asignacionActiva->kilometraje_inicial ? number_format($asignacionActiva->kilometraje_inicial) . ' km' : 'No registrado' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Kilometraje Final</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                                {{ $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) . ' km' : 'No registrado' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <h5 class="text-lg font-medium text-gray-800 mb-2">Sin obra asignada</h5>
                                    <p class="text-sm text-gray-600 mb-4">Este vehículo no está asignado a ninguna obra actualmente.</p>
                                    <p class="text-xs text-gray-500 mb-4">Un vehículo puede estar asignado solo a una obra a la vez.</p>
                                    
                                    @hasPermission('crear_asignaciones')
                                    <div class="flex flex-col space-y-2">
                                        <button onclick="openCambiarObraModal()" 
                                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Asignar Obra
                                        </button>
                                        <a href="{{ route('obras.index') }}" 
                                           class="inline-block bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver Obras Disponibles
                                        </a>
                                        <p class="text-xs text-gray-500">Usa "Asignar Obra" para asignar directamente o "Ver Obras" para explorar opciones</p>
                                    </div>
                                    @else
                                    <p class="text-xs text-gray-500">Contacta al administrador para asignar este vehículo a una obra</p>
                                    @endhasPermission
                                </div>
                                @endif
                            </div>
                            
                            <!-- Sección: Operador Actual -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                        Operador Actual
                                    </h5>
                                    @hasPermission('editar_vehiculos')
                                    <button onclick="openCambiarOperadorModal()" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        @if(isset($vehiculo->operador) && $vehiculo->operador)
                                            Cambiar Operador
                                        @else
                                            Asignar Operador
                                        @endif
                                    </button>
                                    @endhasPermission
                                </div>
                                
                                @if(isset($vehiculo->operador) && $vehiculo->operador)
                                <div class="space-y-3">
                                    <!-- Nombre, NSS, RFC, INE en una fila -->
                                    <div class="grid grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Nombre Completo</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->nombre_completo }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">NSS</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->nss ?? 'No disponible' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">RFC</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->rfc ?? 'No disponible' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">INE</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->ine ?? 'No disponible' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Licencia de Manejo, Domicilio, Teléfono en una fila -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Licencia de Manejo</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->licencia_manejo ?? 'No disponible' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Domicilio</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->domicilio ?? 'No disponible' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Teléfono</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->telefono ?? 'No disponible' }}
                                            </div>
                                        </div>
                                    </div>
                                    

                                </div>
                                @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                    <svg class="w-8 h-8 text-yellow-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <h5 class="text-lg font-medium text-yellow-800 mb-2">No hay operador asignado</h5>
                                    <p class="text-sm text-yellow-600 mb-3">Este vehículo no tiene un operador asignado actualmente.</p>
                                    
                                    @hasPermission('editar_vehiculos')
                                    <button onclick="openCambiarOperadorModal()" 
                                       class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                                        Asignar Operador
                                    </button>
                                    @endhasPermission
                                </div>
                                @endif
                            </div>
                            
                            <!-- Sección: Registro de Kilometraje -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Registro de Kilometraje
                                    </h5>
                                    @hasPermission('crear_kilometrajes')
                                    <button onclick="openKilometrajeModal()" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Capturar Nuevo
                                    </button>
                                    @endhasPermission
                                </div>
                                
                                <!-- Tabla de Kilometrajes -->
                                <div class="overflow-x-auto">
                                    <table id="kilometraje-table" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Registró</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($vehiculo->kilometrajes()->with(['obra', 'usuarioCaptura.personal'])->orderBy('fecha_captura', 'desc')->limit(10)->get() as $kilometraje)
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ number_format($kilometraje->kilometraje) }} km
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $kilometraje->fecha_captura?->format('d/m/Y') ?? 'Sin fecha' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $kilometraje->obra?->nombre_obra ?? 'Sin obra asignada' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $kilometraje->usuarioCaptura?->nombre_completo ?? 'Usuario no disponible' }}
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                        </svg>
                                                        <p class="font-medium">No hay registros de kilometraje</p>
                                                        <p class="text-xs text-gray-400">Este vehículo aún no tiene registros de kilometraje</p>
                                                        @hasPermission('crear_kilometrajes')
                                                        <button onclick="openKilometrajeModal()" 
                                                           class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                                            Crear primer registro
                                                        </button>
                                                        @endhasPermission
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($vehiculo->kilometrajes()->count() > 10)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('kilometrajes.index') }}?vehiculo_id={{ $vehiculo->id }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        Ver todos los registros ({{ $vehiculo->kilometrajes()->count() }} total)
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Documentos -->
                    <div x-show="activeTab === 'documentos'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Documentos del Vehículo -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Documentos del Vehículo
                                    </h5>
                                </div>
                                
                                <!-- Documentos Obligatorios -->
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos Obligatorios</h6>
                                <ul class="divide-y divide-gray-200 mb-6">
                                    <!-- Tarjeta de Circulación -->
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Tarjeta de Circulación</span>
                                                <p class="text-xs text-gray-500">Vence: 15/01/2025</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewDocument('tarjeta')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="downloadDocument('tarjeta')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                                Descargar
                                            </button>
                                        </div>
                                    </li>
                                    
                                    <!-- Póliza de Seguro -->
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Póliza de Seguro</span>
                                                <p class="text-xs text-gray-500">Vence: 30/06/2024</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewDocument('poliza')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="downloadDocument('poliza')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Descargar
                                            </button>
                                        </div>
                                    </li>
                                    
                                    <!-- Verificación Vehicular -->
                                    <li class="py-2 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Verificación Vehicular</span>
                                                <p class="text-xs text-gray-500">Vence: 10/06/2024</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="viewDocument('verificacion')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver
                                            </button>
                                            <button onclick="downloadDocument('verificacion')" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Descargar
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Mantenimientos -->
                    <div x-show="activeTab === 'mantenimientos'" class="p-6 bg-gray-50">
                        <div class="space-y-6">
                            <!-- Mantenimientos del Vehículo -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Mantenimientos Recientes
                                    </h5>
                                    @hasPermission('crear_mantenimientos')
                                    <a href="{{ route('mantenimientos.create') }}?vehiculo_id={{ $vehiculo->id }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Registrar Mantenimiento
                                    </a>
                                    @endhasPermission
                                </div>
                                
                                <!-- Tabla de Mantenimientos -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Sistema</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Kilometraje</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($vehiculo->mantenimientos()->orderBy('fecha_inicio', 'desc')->limit(10)->get() as $mantenimiento)
                                            <tr>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $mantenimiento->fecha_inicio?->format('d/m/Y') ?? 'Sin fecha' }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ ucfirst(strtolower($mantenimiento->tipo_servicio)) }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ ucfirst($mantenimiento->sistema_vehiculo ?? 'General') }}
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($mantenimiento->kilometraje_servicio ?? 0) }} km
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    @if($mantenimiento->fecha_fin)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Completado
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            En Progreso
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    @hasPermission('ver_mantenimientos')
                                                    <a href="{{ route('mantenimientos.show', $mantenimiento) }}" 
                                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                                       title="Ver detalles">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    @endhasPermission
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <p class="font-medium">No hay mantenimientos registrados</p>
                                                        <p class="text-xs text-gray-400">Este vehículo no tiene historial de mantenimientos</p>
                                                        @hasPermission('crear_mantenimientos')
                                                        <a href="{{ route('mantenimientos.create') }}?vehiculo_id={{ $vehiculo->id }}" 
                                                           class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                                            Registrar primer mantenimiento
                                                        </a>
                                                        @endhasPermission
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($vehiculo->mantenimientos()->count() > 10)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('mantenimientos.index') }}?vehiculo_id={{ $vehiculo->id }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        Ver todos los mantenimientos ({{ $vehiculo->mantenimientos()->count() }} total)
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
<!-- Modal para agregar kilometraje -->
<div id="add-kilometraje-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-md z-10">
        <div class="px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Agregar Kilometraje</h3>
        </div>
        <div class="p-4">
            <form id="add-kilometraje-form">
                @csrf
                <div class="mb-4">
                    <label for="add-kilometraje" class="block text-sm font-medium text-gray-700">Kilometraje</label>
                    <input type="number" name="kilometraje" id="add-kilometraje" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="add-fecha_captura" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha_captura" id="add-fecha_captura" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('add-kilometraje-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Guardar Kilometraje
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para subir documentos -->
<div id="upload-document-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-black bg-opacity-50 absolute inset-0"></div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-md z-10">
        <div class="px-4 py-3 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Subir Documento</h3>
        </div>
        <div class="p-4">
            <form id="upload-document-form">
                @csrf
                <div class="mb-4">
                    <label for="documento" class="block text-sm font-medium text-gray-700">Seleccionar Documento</label>
                    <input type="file" name="documento" id="documento" accept=".pdf,.jpg,.jpeg,.png" required
                           class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                    <select name="tipo_documento" id="tipo_documento" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un tipo</option>
                        <option value="tarjeta_circulacion">Tarjeta de Circulación</option>
                        <option value="poliza_seguro">Póliza de Seguro</option>
                        <option value="verificacion_vehicular">Verificación Vehicular</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('upload-document-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Subir Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Capturar Kilometraje -->
<div id="kilometraje-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header del Modal -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Capturar Kilometraje</h3>
                <button onclick="closeKilometrajeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Formulario -->
            <form id="kilometraje-form" method="POST" action="{{ route('vehiculos.kilometrajes.store.vehiculo', $vehiculo) }}">
                @csrf
                
                <!-- Kilometraje Actual (solo referencia) -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-800">
                        <span class="font-semibold">Kilometraje Actual:</span> {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                    </p>
                </div>

                <!-- Campo de Kilometraje -->
                <div class="mb-4">
                    <label for="kilometraje" class="block text-sm font-medium text-gray-700 mb-2">
                        Nuevo Kilometraje <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="kilometraje" 
                           name="kilometraje" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Ingrese el kilometraje actual"
                           min="{{ ($vehiculo->kilometraje_actual ?? 0) + 1 }}"
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        Debe ser mayor a {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km
                    </p>
                </div>

                <!-- Fecha de Captura -->
                <div class="mb-4">
                    <label for="fecha_captura" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Captura <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="fecha_captura" 
                           name="fecha_captura" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           value="{{ date('Y-m-d') }}"
                           max="{{ date('Y-m-d') }}"
                           required>
                </div>

                <!-- Observaciones -->
                <div class="mb-6">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea id="observaciones" 
                              name="observaciones" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Observaciones adicionales (opcional)"></textarea>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeKilometrajeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Guardar Kilometraje
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Obra -->
<div id="cambiar-obra-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">
                @if($asignacionActiva && $asignacionActiva->obra)
                    Cambiar Obra Asignada
                @else
                    Asignar Obra al Vehículo
                @endif
            </h3>
            <button onclick="closeCambiarObraModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Información actual -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-2">Asignación Actual</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Obra:</span>
                    <span class="font-medium">{{ $asignacionActiva ? $asignacionActiva->nombre_obra : 'Sin obra asignada' }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Operador:</span>
                    <span class="font-medium">{{ $vehiculo->operador ? $vehiculo->operador->nombre_completo : 'Sin operador' }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Fecha de asignación:</span>
                    <span class="font-medium">{{ $asignacionActiva ? $asignacionActiva->fecha_asignacion?->format('d/m/Y') : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Kilometraje inicial:</span>
                    <span class="font-medium">{{ $asignacionActiva ? number_format($asignacionActiva->kilometraje_inicial ?? 0) . ' km' : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <form id="cambiar-obra-form" method="POST" action="{{ route('asignaciones-obra.cambiar-obra', $vehiculo) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nueva Obra -->
                <div>
                    <label for="obra_id" class="block text-sm font-medium text-gray-700 mb-1">Nueva Obra</label>
                    <select id="obra_id" name="obra_id" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar obra...</option>
                        @php
                            // Obtener obras disponibles (excluyendo solo la obra actual del vehículo)
                            $obraActualId = null;
                            if ($asignacionActiva && $asignacionActiva->obra) {
                                $obraActualId = $asignacionActiva->obra->id;
                            }
                            
                            $obrasDisponibles = \App\Models\Obra::where('estatus', 'planificada')
                                ->orWhere('estatus', 'en_progreso')
                                ->when($obraActualId, function($query, $obraActualId) {
                                    return $query->where('id', '!=', $obraActualId);
                                })
                                ->orderBy('nombre_obra')
                                ->get();
                        @endphp
                        @foreach($obrasDisponibles as $obra)
                            <option value="{{ $obra->id }}">{{ $obra->nombre_obra }} - {{ $obra->ubicacion ?? 'Sin ubicación' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kilometraje Inicial -->
                <div>
                    <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700 mb-1">Kilometraje Inicial</label>
                    <input type="number" id="kilometraje_inicial" name="kilometraje_inicial" 
                           value="{{ $vehiculo->kilometraje_actual ?? 0 }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Kilometraje actual: {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km</p>
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="observaciones_cambio" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea id="observaciones_cambio" name="observaciones" rows="3" 
                             placeholder="Motivo del cambio de obra..."
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeCambiarObraModal()" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    @if($asignacionActiva && $asignacionActiva->obra)
                        Cambiar Obra
                    @else
                        Asignar Obra
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Cambiar Operador -->
<div id="cambiar-operador-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-operador-title" class="text-lg font-semibold text-gray-900">
                @if(isset($vehiculo->operador) && $vehiculo->operador)
                    Cambiar Operador del Vehículo
                @else
                    Asignar Operador al Vehículo
                @endif
            </h3>
            <button onclick="closeCambiarOperadorModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            <p><strong>Vehículo:</strong> {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})</p>
            @if(isset($vehiculo->operador) && $vehiculo->operador)
                <p><strong>Operador Actual:</strong> {{ $vehiculo->operador->nombre_completo }}</p>
            @else
                <p><strong>Estado:</strong> Sin operador asignado</p>
            @endif
        </div>

        <form id="cambiar-operador-form" method="POST" action="{{ route('vehiculos.cambiar-operador', $vehiculo) }}">
            @csrf
            @method('PATCH')
            
            <!-- Selección de Nuevo Operador -->
            <div class="mb-4">
                <label for="operador_id" class="block text-sm font-medium text-gray-700 mb-1">
                    @if(isset($vehiculo->operador) && $vehiculo->operador)
                        Nuevo Operador
                    @else
                        Operador a Asignar
                    @endif
                </label>
                <select id="operador_id" name="operador_id" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccionar operador...</option>
                    @php
                        // Obtener operadores disponibles (excluyendo el operador actual del vehículo)
                        $operadorActualId = null;
                        if (isset($vehiculo->operador) && $vehiculo->operador) {
                            $operadorActualId = $vehiculo->operador->id;
                        }
                        
                        // Obtener ID de la categoría "Operador"
                        $categoriaOperador = \App\Models\CategoriaPersonal::where('nombre_categoria', 'Operador')->first();
                        
                        $operadoresDisponibles = \App\Models\Personal::where('categoria_id', $categoriaOperador?->id)
                            ->where('estatus', 'activo')
                            ->when($operadorActualId, function($query, $operadorActualId) {
                                return $query->where('id', '!=', $operadorActualId);
                            })
                            ->orderBy('nombre_completo')
                            ->get();
                    @endphp
                    @foreach($operadoresDisponibles as $operador)
                        <option value="{{ $operador->id }}">{{ $operador->nombre_completo }} - {{ $operador->nss ?? 'Sin NSS' }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    @if(isset($vehiculo->operador) && $vehiculo->operador)
                        Selecciona el nuevo operador que reemplazará al actual
                    @else
                        Selecciona el operador que será asignado a este vehículo
                    @endif
                </p>
            </div>

            <!-- Observaciones (opcional) -->
            <div class="mb-4">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones (opcional)</label>
                <textarea id="observaciones" name="observaciones" rows="3" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                         placeholder="Motivo del cambio u observaciones adicionales..."></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeCambiarOperadorModal()" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    @if(isset($vehiculo->operador) && $vehiculo->operador)
                        Cambiar Operador
                    @else
                        Asignar Operador
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Funciones para el modal de kilometraje
    function openKilometrajeModal() {
        document.getElementById('kilometraje-modal').classList.remove('hidden');
        // Enfocar el campo de kilometraje
        setTimeout(() => {
            document.getElementById('kilometraje').focus();
        }, 100);
    }

    function closeKilometrajeModal() {
        document.getElementById('kilometraje-modal').classList.add('hidden');
        // Limpiar el formulario
        document.getElementById('kilometraje-form').reset();
        // Restaurar fecha actual
        document.getElementById('fecha_captura').value = '{{ date("Y-m-d") }}';
    }

    // Función para mostrar el modal de agregar kilometraje
    function showAddKilometrajeModal() {
        document.getElementById('add-kilometraje-modal').classList.remove('hidden');
    }

    // Función para cerrar modales
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Función para liberar asignación de obra
    function liberarAsignacion(asignacionId) {
        if (confirm('¿Está seguro de que desea liberar este vehículo de la obra? Esta acción no se puede deshacer.')) {
            // Crear formulario para enviar petición
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/asignaciones-obra/${asignacionId}/liberar`;
            
            // Agregar token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Agregar método PATCH
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);
            
            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Manejar el envío del formulario de agregar kilometraje
    document.getElementById('add-kilometraje-form').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí puedes agregar la lógica para guardar el kilometraje, como una llamada AJAX
        // Por ahora, solo cerraremos el modal y mostraremos un mensaje
        closeModal('add-kilometraje-modal');
        alert('Kilometraje agregado exitosamente');
    });

    // Manejar el envío del formulario de subir documento
    document.getElementById('upload-document-form').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí puedes agregar la lógica para subir el documento, como una llamada AJAX
        // Por ahora, solo cerraremos el modal y mostraremos un mensaje
        closeModal('upload-document-modal');
        alert('Documento subido exitosamente');
    });

    // Funciones para manejar la carga de imágenes
    function handleImageLoad() {
        const container = document.getElementById('image-container');
        const errorPlaceholder = document.getElementById('image-error-placeholder');
        if (container) container.classList.remove('bg-gray-100', 'border-2', 'border-dashed', 'border-gray-300');
        if (errorPlaceholder) errorPlaceholder.classList.add('hidden');
    }

    function handleImageError() {
        const container = document.getElementById('image-container');
        const errorPlaceholder = document.getElementById('image-error-placeholder');
        if (container) container.classList.add('hidden');
        if (errorPlaceholder) errorPlaceholder.classList.remove('hidden');
    }

    // Eventos para el modal de kilometraje
    document.addEventListener('DOMContentLoaded', function() {
        // Cerrar modal al hacer clic fuera
        const modal = document.getElementById('kilometraje-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeKilometrajeModal();
                }
            });
        }

        // Validación en tiempo real del kilometraje
        const kilometrajeInput = document.getElementById('kilometraje');
        if (kilometrajeInput) {
            kilometrajeInput.addEventListener('input', function(e) {
                const valor = parseInt(e.target.value);
                const minimo = {{ $vehiculo->kilometraje_actual ?? 0 }};
                
                if (valor && valor <= minimo) {
                    e.target.setCustomValidity('El kilometraje debe ser mayor a ' + minimo.toLocaleString() + ' km');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }

        // Manejo del envío del formulario
        const form = document.getElementById('kilometraje-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const cancelBtn = this.querySelector('button[type="button"]');
                
                // Deshabilitar botones durante el envío
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Guardando...';
                }
                if (cancelBtn) {
                    cancelBtn.disabled = true;
                }
            });
        }

        // Mostrar mensajes de sesión (éxito/error)
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        // Si hay errores de validación, reabrir el modal
        @if($errors->any())
            openKilometrajeModal();
            
            // Mostrar errores específicos
            @foreach($errors->all() as $error)
                showNotification('{{ $error }}', 'error');
            @endforeach
        @endif
    });

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        // Agregar al DOM
        document.body.appendChild(notification);
        
        // Remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    // Funciones para el modal de cambiar obra
    function openCambiarObraModal() {
        document.getElementById('cambiar-obra-modal').classList.remove('hidden');
    }

    function closeCambiarObraModal() {
        document.getElementById('cambiar-obra-modal').classList.add('hidden');
        // Limpiar el formulario
        document.getElementById('cambiar-obra-form').reset();
    }

    // Manejar envío del formulario de cambiar obra
    document.getElementById('cambiar-obra-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        
        // Deshabilitar botón mientras se procesa
        submitButton.disabled = true;
        @if($asignacionActiva && $asignacionActiva->obra)
            submitButton.textContent = 'Cambiando...';
        @else
            submitButton.textContent = 'Asignando...';
        @endif
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Obra cambiada exitosamente', 'success');
                closeCambiarObraModal();
                // Recargar la página para mostrar los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.error || 'Error al cambiar la obra', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cambiar la obra', 'error');
        })
        .finally(() => {
            // Rehabilitar botón
            submitButton.disabled = false;
            @if($asignacionActiva && $asignacionActiva->obra)
                submitButton.textContent = 'Cambiar Obra';
            @else
                submitButton.textContent = 'Asignar Obra';
            @endif
        });
    });

    // Funciones para el modal de cambiar operador
    function openCambiarOperadorModal() {
        document.getElementById('cambiar-operador-modal').classList.remove('hidden');
    }

    function closeCambiarOperadorModal() {
        document.getElementById('cambiar-operador-modal').classList.add('hidden');
        // Limpiar el formulario
        document.getElementById('cambiar-operador-form').reset();
    }

    // Manejar envío del formulario de cambiar operador
    document.getElementById('cambiar-operador-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        
        // Deshabilitar botón mientras se procesa
        submitButton.disabled = true;
        @if(isset($vehiculo->operador) && $vehiculo->operador)
            submitButton.textContent = 'Cambiando...';
        @else
            submitButton.textContent = 'Asignando...';
        @endif
        
        // Debug: verificar que los datos se envían
        console.log('Datos del formulario:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        fetch(this.action, {
            method: 'POST', // Cambiar a POST y usar _method en FormData
            body: formData, // FormData ya incluye _token y _method
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
                // No incluir X-CSRF-TOKEN cuando usamos FormData con _token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeCambiarOperadorModal();
                
                // Redirigir después de un breve delay
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showNotification(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cambiar el operador', 'error');
        })
        .finally(() => {
            // Rehabilitar botón
            submitButton.disabled = false;
            @if(isset($vehiculo->operador) && $vehiculo->operador)
                submitButton.textContent = 'Cambiar Operador';
            @else
                submitButton.textContent = 'Asignar Operador';
            @endif
        });
    });
</script>
@endpush
