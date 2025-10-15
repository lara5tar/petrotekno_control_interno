@extends('layouts.app')

@section('title', 'Detalles del Activo')

@section('content')
<style>
/* Estilos para modales - asegurar que estén ocultos por defecto */
[id$="-modal"] {
    display: none !important;
}

[id$="-modal"]:not(.hidden) {
    display: flex !important;
}

/* Estilos para optimizar modales cuando están abiertos */
.modal-dialog-auto {
    height: auto !important;
    min-height: auto !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
}

.modal-content-compact {
    padding: 24px !important;
    margin: 20px !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.modal-backdrop {
    overflow-y: auto !important;
    padding: 40px !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Espaciado mejorado para formularios en modales */
.modal-form-group {
    margin-bottom: 20px !important;
}

.modal-form-group label {
    margin-bottom: 8px !important;
    display: block !important;
    font-weight: 500 !important;
}

.modal-form-group input,
.modal-form-group select,
.modal-form-group textarea {
    padding: 12px !important;
    border-radius: 8px !important;
    border: 1px solid #d1d5db !important;
    margin-bottom: 4px !important;
}

.modal-form-group textarea {
    min-height: 80px !important;
    resize: vertical !important;
}

/* Espaciado para botones en modales */
.modal-buttons {
    margin-top: 24px !important;
    padding-top: 20px !important;
    border-top: 1px solid #e5e7eb !important;
    gap: 12px !important;
}

/* Títulos de modales con mejor espaciado */
.modal-title {
    margin-bottom: 20px !important;
    padding-bottom: 12px !important;
    border-bottom: 1px solid #e5e7eb !important;
}
</style>

<!-- Breadcrumb -->
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Activos', 'url' => route('vehiculos.index')],
    ['label' => 'Detalle del Activo']
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
                    <!-- Datos del activo primero -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tipo de Activo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $vehiculo->tipoActivo->nombre ?? 'No especificado' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Marca</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ !empty($vehiculo->marca) ? $vehiculo->marca : 'Sin marca' }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Se eliminó el campo de póliza duplicado -->
                    
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Modelo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ !empty($vehiculo->modelo) ? $vehiculo->modelo : 'Sin modelo' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Año</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ !empty($vehiculo->anio) ? $vehiculo->anio : 'Sin año' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Placas</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ !empty($vehiculo->placas) ? $vehiculo->placas : 'Sin placas' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Serie</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ !empty($vehiculo->n_serie) ? $vehiculo->n_serie : 'Sin número de serie' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                @if($vehiculo->estatus)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($vehiculo->estatus->value === 'asignado')
                                            bg-blue-100 text-blue-800
                                        @elseif($vehiculo->estatus->value === 'disponible')
                                            bg-green-100 text-green-800
                                        @elseif($vehiculo->estatus->value === 'en_mantenimiento')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($vehiculo->estatus->value === 'fuera_de_servicio')
                                            bg-red-100 text-red-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ $vehiculo->estatus->nombre() }}
                                    </span>
                                @else
                                    Sin estado
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Municipio</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ !empty($vehiculo->municipio) ? $vehiculo->municipio : 'Sin municipio' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Derecho Vehicular</label>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                        @if($vehiculo->derecho_url)
                                    <span class="text-green-200">✓ Año {{ \Carbon\Carbon::parse($vehiculo->created_at)->format('Y') }}</span>
                                @else
                                    Sin documento
                                @endif
                            </div>
                            @if($vehiculo->derecho_url)
                                <button onclick="viewDocument('{{ $vehiculo->derecho_url }}')" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-2 rounded text-sm transition duration-200"
                                                title="Ver documento">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                @if($vehiculo->derecho_vencimiento)
                            <div class="text-xs text-gray-500">
                                Vence: {{ $vehiculo->derecho_vencimiento->format('d/m/Y') }}
                                @if($vehiculo->derecho_vencimiento < now())
                                    <span class="text-red-600 font-semibold">(VENCIDO)</span>
                                @elseif($vehiculo->derecho_vencimiento < now()->addDays(30))
                                    <span class="text-yellow-600 font-semibold">(PRÓXIMO A VENCER)</span>
                                @endif
                            </div>
                        @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Póliza de Seguro</label>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium flex-1">
                                        @if($vehiculo->numero_poliza)
                                    <span class="text-green-200">{{ $vehiculo->numero_poliza }}</span>
                                @else
                                    Sin número de póliza
                                @endif
                            </div>
                            @if($vehiculo->poliza_url)
                                <button onclick="viewDocument('{{ $vehiculo->poliza_url }}')" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-2 rounded text-sm transition duration-200"
                                                title="Ver documento">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                @if($vehiculo->poliza_vencimiento)
                            <div class="text-xs text-gray-500">
                                Vence: {{ $vehiculo->poliza_vencimiento->format('d/m/Y') }}
                                @if($vehiculo->poliza_vencimiento < now())
                                    <span class="text-red-600 font-semibold">(VENCIDO)</span>
                                @elseif($vehiculo->poliza_vencimiento < now()->addDays(30))
                                    <span class="text-yellow-600 font-semibold">(PRÓXIMO A VENCER)</span>
                                @endif
                            </div>
                        @endif
                            </div>
                        </div>
                    </div>

                    <!-- Se eliminó Tipo de Maquinaria de aquí y se movió al inicio -->

                    <!-- Fotografía del Activo ahora al final -->
                    <div class="mt-6">
                        <div class="relative">
                            @if(!empty($vehiculo->url_imagen) && $vehiculo->url_imagen !== null && $vehiculo->url_imagen !== '')
                            <div class="bg-gray-100 rounded overflow-hidden mb-2" id="image-container">
                                <img src="{{ $vehiculo->url_imagen }}" 
                                     alt="Activo {{ !empty($vehiculo->marca) ? $vehiculo->marca : 'Sin marca' }} {{ !empty($vehiculo->modelo) ? $vehiculo->modelo : 'Sin modelo' }}" 
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
                                        <p class="text-xs text-red-500">Error al cargar la imagen del activo</p>
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
                                        <p class="text-xs text-gray-400">No se ha asignado una imagen a este activo</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Observaciones</label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            @if(!empty($vehiculo->observaciones))
                                <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $vehiculo->observaciones }}</div>
                            @else
                                <div class="text-sm text-gray-400 italic">Sin observaciones</div>
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
                <!-- Último Kilometraje - Solo se muestra si el tipo de activo tiene kilometraje -->
                @if($vehiculo->tipoActivo && $vehiculo->tipoActivo->tiene_kilometraje)
                <div class="flex-shrink-0">
                    <div class="text-base font-medium text-gray-600 mb-1">Último Kilometraje</div>
                    <div class="bg-gray-800 text-white p-2 rounded text-center min-w-[160px]">
                        <div class="text-lg font-bold">{{ number_format($vehiculo->kilometraje_actual ?? 0) }} km</div>
                    </div>
                </div>
                @endif

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
            <div class="bg-white flex-1 flex flex-col" id="tabs-container">
                <div class="relative">
                    <nav class="flex space-x-1 pr-3 pt-3">
                        <button onclick="changeTab('operacion')" 
                                id="tab-operacion"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 ml-0 bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Operación
                            </span>
                        </button>
                        <button onclick="changeTab('documentos')" 
                                id="tab-documentos"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Documentos
                            </span>
                        </button>
                        <button onclick="changeTab('mantenimientos')" 
                                id="tab-mantenimientos"
                                class="relative px-4 py-2 text-sm font-medium rounded-t-lg transition-all duration-200 border-b-0 bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200">
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
                    <div id="content-operacion" class="p-6 bg-gray-50 tab-content" style="display: block;">
                        <div class="space-y-6">
                            <!-- Sección: Obra Actual -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm" id="obra-actual-section">
                                @php
                                    // Obtener la asignación activa actual del activo
                                    $asignacionActiva = null;
                                    
                                    // Verificar si el activo tiene el método asignacionesObraActivas
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
                                            <div class="flex justify-between items-center mb-1">
                                                <label class="block text-sm text-gray-600">Responsable de la Obra</label>
                                                @hasPermission('editar_obras')
                                                <button id="btn-responsable-obra" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                    {{ $asignacionActiva->obra->encargado ? 'Cambiar' : 'Asignar' }}
                                                </button>
                                                @endhasPermission
                                            </div>
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
                                    <div class="grid {{ $vehiculo->tipoActivo && $vehiculo->tipoActivo->tiene_kilometraje ? 'grid-cols-4' : 'grid-cols-2' }} gap-3">
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
                                        @if($vehiculo->tipoActivo && $vehiculo->tipoActivo->tiene_kilometraje)
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
                                        @endif
                                    </div>
                                    
                                    <!-- Botón Ver Obra -->
                                    <div class="mt-4">
                                        <a href="{{ route('obras.show', $asignacionActiva->obra->id) }}" 
                                           class="inline-block bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md text-sm transition-colors duration-200">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver Obra
                                        </a>
                                    </div>
                                </div>
                                @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <h5 class="text-lg font-medium text-gray-800 mb-2">Sin obra asignada</h5>
                                    <p class="text-sm text-gray-600 mb-4">Este activo no está asignado a ninguna obra actualmente.</p>
                <p class="text-xs text-gray-500 mb-4">Un activo puede estar asignado solo a una obra a la vez.</p>
                                    
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
                                    <p class="text-xs text-gray-500">Contacta al administrador para asignar este activo a una obra</p>
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
                                    
                                    <!-- Licencia de Manejo, Domicilio en una fila -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600">Licencia de Manejo</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->no_licencia ?? 'No disponible' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600">Domicilio</label>
                                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm">
                                                {{ $vehiculo->operador->direccion ?? 'No disponible' }}
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
                                    <p class="text-sm text-yellow-600 mb-3">Este activo no tiene un operador asignado actualmente.</p>
                                    
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
                            @if($vehiculo->tipoActivo && $vehiculo->tipoActivo->tiene_kilometraje)
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
                                                <th scope="col" class="px-3 py-2 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Combustible</th>
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
                                                    {{ $kilometraje->cantidad_combustible ? number_format($kilometraje->cantidad_combustible, 2) . ' L' : 'N/A' }}
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
                                                <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                        </svg>
                                                        <p class="font-medium">No hay registros de kilometraje</p>
                                                        <p class="text-xs text-gray-400">Este activo aún no tiene registros de kilometraje</p>
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
                            @endif
                        </div>
                    </div>

                    <!-- Contenido de Documentos -->
                    <div id="content-documentos" class="p-6 bg-gray-50 tab-content" style="display: none;">
                        <div class="space-y-6">
                            <!-- Documentos del Activo -->
                            <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h5 class="text-base font-semibold text-gray-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Documentos del Activo
                                    </h5>
                                </div>
                                
                                <!-- Documentos del Activo -->
                                <h6 class="text-sm font-medium text-gray-700 mb-2">Documentos del Activo</h6>
                                <ul class="divide-y divide-gray-200 mb-6">
                                    
                                    <!-- Póliza de Seguro -->
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Póliza de Seguro</span>
                                                @if($vehiculo->poliza_vencimiento)
                                                    <p class="text-xs text-gray-500">
                                                        Vence: {{ $vehiculo->poliza_vencimiento->format('d/m/Y') }}
                                                        @if($vehiculo->poliza_vencimiento < now())
                                                            <span class="text-red-600 font-medium">(Vencida)</span>
                                                        @elseif($vehiculo->poliza_vencimiento <= now()->addDays(30))
                                                            <span class="text-yellow-600 font-medium">(Por vencer)</span>
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="text-xs text-gray-400">Sin fecha de vencimiento</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($vehiculo->poliza_url)
                                                <a href="{{ $vehiculo->poliza_url }}" target="_blank" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ $vehiculo->poliza_url }}" download 
                                                   class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No disponible</span>
                                            @endif
                                        </div>
                                    </li>
                                    
                                    <!-- Derecho Vehicular -->
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Derecho Vehicular</span>
                                                @if($vehiculo->derecho_vencimiento)
                                                    <p class="text-xs text-gray-500">
                                                        Vence: {{ $vehiculo->derecho_vencimiento->format('d/m/Y') }}
                                                        @if($vehiculo->derecho_vencimiento < now())
                                                            <span class="text-red-600 font-medium">(Vencido)</span>
                                                        @elseif($vehiculo->derecho_vencimiento <= now()->addDays(30))
                                                            <span class="text-yellow-600 font-medium">(Por vencer)</span>
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="text-xs text-gray-400">Sin fecha de vencimiento</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($vehiculo->derecho_url)
                                                <a href="{{ $vehiculo->derecho_url }}" target="_blank" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ $vehiculo->derecho_url }}" download 
                                                   class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No disponible</span>
                                            @endif
                                        </div>
                                    </li>
                                    
                                    <!-- Factura del Activo -->
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Factura/Pedimento</span>
                                                <p class="text-xs text-gray-500">Documento de compra del activo</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($vehiculo->factura_url)
                                                <a href="{{ $vehiculo->factura_url }}" target="_blank" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ $vehiculo->factura_url }}" download 
                                                   class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">No disponible</span>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Mantenimientos -->
                    <div id="content-mantenimientos" class="p-6 bg-gray-50 tab-content" style="display: none;">
                        <div class="space-y-6">
                            <!-- Mantenimientos del Activo -->
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
                                        <button onclick="openMantenimientoModal()" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded-md transition-colors duration-200 flex items-center text-xs">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Registrar Mantenimiento
                                        </button>
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
                                                    {{ strtoupper($mantenimiento->sistema_vehiculo ?? 'GENERAL') }}
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
                                                        <p class="text-xs text-gray-400">Este activo no tiene historial de mantenimientos</p>
                                                        @hasPermission('crear_mantenimientos')
                                                        <button onclick="openMantenimientoModal()" 
                                                                class="mt-2 text-blue-600 hover:text-blue-800 text-sm underline">
                                                            Registrar primer mantenimiento
                                                        </button>
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
    <div class="relative top-20 mx-auto p-4 border shadow-lg rounded-md bg-white max-w-md">
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

                <!-- Cantidad de Combustible -->
                <div class="mb-4">
                    <label for="cantidad_combustible" class="block text-sm font-medium text-gray-700 mb-2">
                        Cantidad de Combustible (Litros) - Opcional
                    </label>
                    <input type="number" 
                           id="cantidad_combustible" 
                           name="cantidad_combustible" 
                           min="0" 
                           max="9999.99" 
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Ej: 50.5">
                </div>

                <!-- Observaciones -->
                <div class="mb-4">
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
                <div class="flex justify-end space-x-3 mt-2">
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
<div id="cambiar-obra-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 modal-backdrop">
    <div class="relative top-10 mx-auto border w-96 max-w-md shadow-lg rounded-md bg-white modal-dialog-auto modal-content-compact">
        <div class="flex justify-between items-center modal-title">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-900">
                @if($asignacionActiva && $asignacionActiva->obra)
                    Cambiar Obra Asignada
                @else
                    Asignar Obra al Activo
                @endif
            </h3>
            <button onclick="closeCambiarObraModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Información actual -->
        <div class="modal-form-group p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-3">Asignación Actual</h4>
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
                <div class="modal-form-group">
                    <label for="obra_id" class="block text-sm font-medium text-gray-700">Nueva Obra</label>
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
                <div class="modal-form-group">
                    <label for="kilometraje_inicial" class="block text-sm font-medium text-gray-700">Kilometraje Inicial</label>
                    <input type="number" id="kilometraje_inicial" name="kilometraje_inicial" 
                           value="{{ $vehiculo->kilometraje_actual ?? 0 }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Kilometraje actual: {{ number_format($vehiculo->kilometraje_actual ?? 0) }} km</p>
                </div>

                <!-- Observaciones -->
                <div class="modal-form-group col-span-2">
                    <label for="observaciones_cambio" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea id="observaciones_cambio" name="observaciones" rows="3" 
                             placeholder="Motivo del cambio de obra..."
                             class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical"></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 modal-buttons">
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
</div>

<!-- Modal para Cambiar Operador -->
<div id="cambiar-operador-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 modal-backdrop">
    <div class="relative top-10 mx-auto border w-96 max-w-md shadow-lg rounded-md bg-white modal-dialog-auto modal-content-compact">
        <div class="flex justify-between items-center modal-title">
            <h3 id="modal-operador-title" class="text-lg font-semibold text-gray-900">
                @if(isset($vehiculo->operador) && $vehiculo->operador)
                Cambiar Operador del Activo
                @else
                    Asignar Personal como Operador
                @endif
            </h3>
            <button onclick="closeCambiarOperadorModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="modal-form-group p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
            <p><strong>Activo:</strong> {{ !empty($vehiculo->marca) ? $vehiculo->marca : 'Sin marca' }} {{ !empty($vehiculo->modelo) ? $vehiculo->modelo : 'Sin modelo' }} ({{ !empty($vehiculo->placas) ? $vehiculo->placas : 'Sin placas' }})</p>
            @if(isset($vehiculo->operador) && $vehiculo->operador)
                <p class="mt-2"><strong>Operador Actual:</strong> {{ $vehiculo->operador->nombre_completo }}</p>
            @else
                <p class="mt-2"><strong>Estado:</strong> Sin operador asignado</p>
            @endif
        </div>

        <form id="cambiar-operador-form" method="POST" action="{{ route('vehiculos.cambiar-operador', $vehiculo) }}">
            @csrf
            @method('PATCH')
            
            <!-- Selección de Personal como Operador -->
            <div class="modal-form-group">
                <label for="operador_id" class="block text-sm font-medium text-gray-700">
                    @if(isset($vehiculo->operador) && $vehiculo->operador)
                        Nuevo Personal como Operador
                    @else
                        Personal a Asignar como Operador
                    @endif
                </label>
                <select id="operador_id" name="operador_id" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleccionar personal...</option>
                    @php
                        // Obtener operadores disponibles (excluyendo el operador actual del vehículo)
                        $operadorActualId = null;
                        if (isset($vehiculo->operador) && $vehiculo->operador) {
                            $operadorActualId = $vehiculo->operador->id;
                        }
                        
                        // Obtener todo el personal activo (no solo operadores)
                        $personalDisponible = \App\Models\Personal::where('estatus', 'activo')
                            ->when($operadorActualId, function($query, $operadorActualId) {
                                return $query->where('id', '!=', $operadorActualId);
                            })
                            ->with('categoria') // Incluir la categoría para mostrarla
                            ->orderBy('nombre_completo')
                            ->get();
                    @endphp
                    @foreach($personalDisponible as $persona)
                        <option value="{{ $persona->id }}">
                            {{ $persona->nombre_completo }}
                            @if($persona->categoria)
                                ({{ $persona->categoria->nombre_categoria }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    @if(isset($vehiculo->operador) && $vehiculo->operador)
                        Selecciona la nueva persona que operará este activo
                    @else
                        Selecciona la persona que será asignada como operador de este activo
                    @endif
                </p>
            </div>

            <!-- Observaciones (opcional) -->
            <div class="modal-form-group">
                <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones (opcional)</label>
                <textarea id="observaciones" name="observaciones" rows="3" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                         placeholder="Motivo del cambio u observaciones adicionales..."></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 modal-buttons">
                <button type="button" onclick="closeCambiarOperadorModal()" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    @if(isset($vehiculo->operador) && $vehiculo->operador)
                        Cambiar Operador
                    @else
                        Asignar como Operador
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Registrar Mantenimiento -->
<div id="registrar-mantenimiento-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[60]">
    <div class="relative top-10 mx-auto border w-96 max-w-md shadow-lg rounded-md bg-white modal-dialog-auto modal-content-compact">
        <div class="flex justify-between items-center modal-title">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Registrar Nuevo Mantenimiento
            </h3>
            <button onclick="closeMantenimientoModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-4 text-sm text-gray-600">
            <p><strong>Activo:</strong> {{ !empty($vehiculo->marca) ? $vehiculo->marca : 'Sin marca' }} {{ !empty($vehiculo->modelo) ? $vehiculo->modelo : 'Sin modelo' }} ({{ !empty($vehiculo->placas) ? $vehiculo->placas : 'Sin placas' }})</p>
        </div>

        <form id="registrar-mantenimiento-form" method="POST" action="{{ route('mantenimientos.store') }}">
            @csrf
            <input type="hidden" name="vehiculo_id" value="{{ $vehiculo->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tipo de Servicio -->
                <div>
                    <label for="tipo_servicio" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Servicio *</label>
                    <select id="tipo_servicio" name="tipo_servicio" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar tipo</option>
                        <option value="PREVENTIVO">Preventivo</option>
                        <option value="CORRECTIVO">Correctivo</option>
                    </select>
                </div>

                <!-- Sistema del Activo -->
                <div>
                    <label for="sistema_vehiculo" class="block text-sm font-medium text-gray-700 mb-2">Sistema del Activo *</label>
                    <select id="sistema_vehiculo" name="sistema_vehiculo" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar sistema</option>
                        <option value="motor">Motor</option>
                        <option value="transmision">Transmisión</option>
                        <option value="hidraulico">Hidráulico</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <!-- Proveedor -->
                <div>
                    <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-2">Proveedor *</label>
                    <input type="text" id="proveedor" name="proveedor" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nombre del taller o proveedor">
                </div>

                <!-- Kilometraje -->
                <div>
                    <label for="kilometraje_servicio" class="block text-sm font-medium text-gray-700 mb-2">Kilometraje del Servicio *</label>
                    <input type="number" id="kilometraje_servicio" name="kilometraje_servicio" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="ej: 15000">
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio *</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Dejar vacío si el mantenimiento está en progreso</p>
                </div>

                <!-- Costo -->
                <div class="md:col-span-2">
                    <label for="costo" class="block text-sm font-medium text-gray-700 mb-2">Costo (opcional)</label>
                    <input type="number" step="0.01" id="costo" name="costo" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="ej: 250.00">
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe el mantenimiento realizado o por realizar..."></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeMantenimientoModal()" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                    Registrar Mantenimiento
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Asignar/Cambiar Responsable de Obra -->
<div id="responsable-obra-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[100]" style="display: none;">
    <div class="relative top-10 mx-auto p-3 border w-96 max-w-md shadow-lg rounded-md bg-white" style="height: auto; min-height: auto;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                @if($asignacionActiva && $asignacionActiva->obra && $asignacionActiva->obra->encargado)
                    Cambiar Responsable de Obra
                @else
                    Asignar Responsable de Obra
                @endif
            </h3>
            <button onclick="closeResponsableObraModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if($asignacionActiva && $asignacionActiva->obra)
            <!-- Información actual -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">Información Actual</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Obra:</span>
                        <span class="font-medium">{{ $asignacionActiva->obra->nombre_obra }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Responsable actual:</span>
                        <span class="font-medium">
                            {{ $asignacionActiva->obra->encargado ? $asignacionActiva->obra->encargado->nombre_completo : 'Sin responsable asignado' }}
                        </span>
                    </div>
                </div>
            </div>

            <form id="responsable-obra-form" method="POST" action="{{ route('obras.cambiar-encargado', $asignacionActiva->obra) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label for="personal_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar Responsable *
                    </label>
                    <select id="personal_id" name="personal_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar responsable...</option>
                        @php
                            $personalDisponible = \App\Models\Personal::where('estatus', 'activo')
                                ->orderBy('nombre_completo')
                                ->get();
                        @endphp
                        @foreach($personalDisponible as $persona)
                            <option value="{{ $persona->id }}" 
                                    {{ ($asignacionActiva->obra->encargado && $asignacionActiva->obra->encargado->id == $persona->id) ? 'selected' : '' }}>
                                {{ $persona->nombre_completo }} - {{ $persona->puesto ?? 'Sin puesto' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="observaciones_responsable" class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones (opcional)
                    </label>
                    <textarea id="observaciones_responsable" name="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Observaciones sobre el cambio de responsable..."></textarea>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="closeResponsableObraModal()" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        @if($asignacionActiva->obra->encargado)
                            Cambiar Responsable
                        @else
                            Asignar Responsable
                        @endif
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <h4 class="text-lg font-medium text-gray-900 mb-2">Sin obra asignada</h4>
                <p class="text-gray-600 mb-4">Este activo no está asignado a ninguna obra actualmente.</p>
            <p class="text-sm text-gray-500">Primero debe asignar el activo a una obra para poder designar un responsable.</p>
            </div>
        @endif
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    <!-- Botón Editar -->
    <a href="{{ route('vehiculos.edit', $vehiculo->id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
       title="Editar Activo">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>

    <!-- Botón Eliminar -->
    <button type="button" 
            onclick="openDeleteModal('{{ route('vehiculos.destroy', $vehiculo->id) }}', '{{ $vehiculo->marca ?? 'Sin marca' }} {{ $vehiculo->modelo ?? 'Sin modelo' }}')"
            class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
            title="Eliminar Activo">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
        </svg>
        <span>Eliminar</span>
    </button>
</div>

@endsection

<!-- Modal de confirmación para eliminar vehículo -->
<x-delete-confirmation-modal 
    id="delete-confirmation-modal"
    entity="el vehículo"
    entityIdField="vehiculo-id"
    entityDisplayField="vehiculo-info"
    routeName="vehiculos"
    additionalText="Esta acción no se puede deshacer y eliminará toda la información asociada al vehículo."
    :useTraditionalSubmit="true"
/>

@push('scripts')
<script>
    // ===== FUNCIONES GLOBALES DE MODALES =====
    
    // Función para cerrar todos los modales
    function closeAllModals() {
        const modalIds = [
            'cambiar-operador-modal',
            'cambiar-obra-modal',
            'kilometraje-modal',
            'add-kilometraje-modal',
            'upload-document-modal',
            'registrar-mantenimiento-modal',
            'responsable-obra-modal',
            'delete-confirmation-modal'
        ];
        
        modalIds.forEach(function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        });
    }

    // ===== FUNCIONES DEL MODAL DE ELIMINACIÓN =====
    function openDeleteModal(deleteUrl, itemName) {
        closeAllModals();
        const modal = document.getElementById('delete-confirmation-modal');
        const entityIdSpan = document.getElementById('entity-id');
        const entityDisplaySpan = document.getElementById('entity-display');
        const form = document.getElementById('delete-confirmation-modal-form');
        
        if (modal && entityIdSpan && entityDisplaySpan && form) {
            // Extraer ID del URL (último segmento)
            const vehiculoId = deleteUrl.split('/').pop();
            
            entityIdSpan.textContent = `#${vehiculoId}`;
            entityDisplaySpan.textContent = itemName ? ` - ${itemName}` : '';
            form.setAttribute('action', deleteUrl);
            modal.classList.remove('hidden');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-confirmation-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Funciones para el modal de cambiar operador
    function openCambiarOperadorModal() {
        closeAllModals();
        const modal = document.getElementById('cambiar-operador-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            console.error('Modal cambiar-operador-modal no encontrado');
        }
    }

    function closeCambiarOperadorModal() {
        const modal = document.getElementById('cambiar-operador-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        // Limpiar el formulario
        const form = document.getElementById('cambiar-operador-form');
        if (form) form.reset();
    }

    // Funciones para el modal de cambiar obra
    function openCambiarObraModal() {
        closeAllModals();
        const modal = document.getElementById('cambiar-obra-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        } else {
            console.error('Modal cambiar-obra-modal no encontrado');
        }
    }

    function closeCambiarObraModal() {
        const modal = document.getElementById('cambiar-obra-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        // Limpiar el formulario
        const form = document.getElementById('cambiar-obra-form');
        if (form) form.reset();
    }

    // Funciones para el modal de mantenimiento
    function openMantenimientoModal() {
        closeAllModals();
        const modal = document.getElementById('registrar-mantenimiento-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }

    function closeMantenimientoModal() {
        const modal = document.getElementById('registrar-mantenimiento-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    // ===== EVENTOS GLOBALES =====
    
    // Manejar tecla Escape para cerrar modales
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
    
    // Cerrar modales al hacer clic fuera de ellos
    document.addEventListener('click', function(e) {
        const modales = [
            'cambiar-operador-modal',
            'cambiar-obra-modal', 
            'registrar-mantenimiento-modal'
        ];
        
        modales.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && e.target === modal) {
                closeAllModals();
            }
        });
    });

    // ===== FIN EVENTOS GLOBALES =====

    // ===== FIN FUNCIONES GLOBALES =====

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
    });
</script>

<!-- Blade directives for session messages -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showNotification(@json(session('success')), 'success');
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showNotification(@json(session('error')), 'error');
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        openKilometrajeModal();
        
        // Mostrar errores específicos
        @foreach($errors->all() as $error)
            showNotification(@json($error), 'error');
        @endforeach
    });
</script>
@endif

<script>

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        let className = 'fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ';
        if (type === 'success') {
            className += 'bg-green-500 text-white';
        } else if (type === 'error') {
            className += 'bg-red-500 text-white';
        } else {
            className += 'bg-blue-500 text-white';
        }
        notification.className = className;
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
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeCambiarOperadorModal();
                
                // Redirigir después de un breve delay
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showNotification(data.error || 'Error al cambiar el operador', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cambiar el operador: ' + error.message, 'error');
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

    // Manejar envío del formulario de registrar mantenimiento
    document.getElementById('registrar-mantenimiento-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        
        // Deshabilitar botón mientras se procesa
        submitButton.disabled = true;
        submitButton.textContent = 'Registrando...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Mantenimiento registrado exitosamente', 'success');
                closeMantenimientoModal();
                
                // Redirigir después de un breve delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.error || 'Error al registrar el mantenimiento', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al registrar el mantenimiento', 'error');
        })
        .finally(() => {
            // Rehabilitar botón
            submitButton.disabled = false;
            submitButton.textContent = 'Registrar Mantenimiento';
        });
    });

    // Event listener para cerrar modal al hacer clic en el fondo
    document.getElementById('registrar-mantenimiento-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMantenimientoModal();
        }
    });

    // Funciones para el modal del responsable de obra
    function openResponsableObraModal() {
        console.log('👤 [INICIO] openResponsableObraModal llamada');
        
        // Cerrar todos los modales primero
        closeAllModals();
        
        // Obtener el modal específico
        const modal = document.getElementById('responsable-obra-modal');
        if (!modal) {
            console.error('❌ Modal responsable-obra-modal no encontrado');
            alert('Error: Modal de responsable de obra no encontrado');
            return;
        }
        
        console.log('✅ [OK] Modal encontrado:', modal);
        
        // Asegurar que el modal esté visible
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        
        // Verificar si el modal está realmente visible
        console.log('📊 [VERIFICACION] Estado del modal:');
        console.log('   - Clases:', modal.className);
        console.log('   - Hidden:', modal.classList.contains('hidden'));
        console.log('   - Display:', getComputedStyle(modal).display);
        
        console.log('🏁 [FIN] openResponsableObraModal completada');
    }

    function closeResponsableObraModal() {
        console.log('🔒 [INICIO] closeResponsableObraModal llamada');
        
        const modal = document.getElementById('responsable-obra-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            
            // Limpiar el formulario
            const form = document.getElementById('responsable-obra-form');
            if (form) {
                form.reset();
            }
        }
        
        console.log('🏁 [FIN] closeResponsableObraModal completada');
    }

    // Manejar envío del formulario de cambiar responsable
    const responsableForm = document.getElementById('responsable-obra-form');
    if (responsableForm) {
        responsableForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Deshabilitar botón mientras se procesa
            submitButton.disabled = true;
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Actualizando...';
            
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
                    showNotification(data.message || 'Responsable asignado exitosamente', 'success');
                    closeResponsableObraModal();
                    
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.error || 'Error al asignar el responsable', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al asignar el responsable', 'error');
            })
            .finally(() => {
                // Rehabilitar botón
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            });
        });
    }

    // Event listener para cerrar modal al hacer clic en el fondo
    const responsableModal = document.getElementById('responsable-obra-modal');
    if (responsableModal) {
        responsableModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeResponsableObraModal();
            }
        });
    }

    // Función para manejar el cambio de pestañas
    function changeTab(tabName) {
        // Ocultar todos los contenidos de pestañas
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.style.display = 'none';
        });

        // Remover clase activa de todos los botones
        const tabButtons = document.querySelectorAll('[id^="tab-"]');
        tabButtons.forEach(button => {
            button.className = button.className.replace(
                'bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10',
                'bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200'
            );
        });

        // Mostrar el contenido de la pestaña activa
        const activeContent = document.getElementById('content-' + tabName);
        if (activeContent) {
            activeContent.style.display = 'block';
        }

        // Agregar clase activa al botón clickeado
        const activeButton = document.getElementById('tab-' + tabName);
        if (activeButton) {
            activeButton.className = activeButton.className.replace(
                'bg-gray-100 border-gray-300 border-t border-l border-r text-gray-600 hover:bg-gray-200',
                'bg-gray-50 border-gray-300 border-t border-l border-r text-gray-800 shadow-sm z-10'
            );
        }
    }

    // Función para mostrar modal de imagen
    function showImageModal(imageUrl, activoDescription) {
        // Crear modal dinámicamente si no existe
        let modal = document.getElementById('image-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'image-modal';
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75';
            modal.innerHTML = `
                <div class="relative max-w-4xl max-h-full p-4">
                    <button onclick="closeImageModal()" class="absolute -top-4 -right-4 bg-white rounded-full p-2 text-gray-600 hover:text-gray-800 z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg shadow-lg" />
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-3 rounded-b-lg">
                        <p id="modal-description" class="text-center text-sm font-medium"></p>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Cerrar modal al hacer clic en el fondo
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal();
                }
            });
        }
        
        // Actualizar contenido del modal
        document.getElementById('modal-image').src = imageUrl;
        document.getElementById('modal-image').alt = activoDescription;
        document.getElementById('modal-description').textContent = activoDescription;
        
        // Mostrar modal
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // Función para cerrar modal de imagen
    function closeImageModal() {
        const modal = document.getElementById('image-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Inicializar la primera pestaña como activa al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        changeTab('operacion');
        
        // Asegurarse de que los event listeners para los modales estén configurados correctamente
        const btnResponsableObra = document.getElementById('btn-responsable-obra');
        if (btnResponsableObra) {
            console.log('Encontrado botón de responsable de obra, configurando evento');
            btnResponsableObra.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Clic en botón de responsable obra');
                openResponsableObraModal();
            });
        } else {
            console.warn('No se encontró el botón de responsable de obra');
        }
        
        // Asegurarse de que el modal existe
        const modalResponsable = document.getElementById('responsable-obra-modal');
        if (modalResponsable) {
            console.log('Modal de responsable encontrado');
        } else {
            console.warn('Modal de responsable NO encontrado');
        }
        
        // CORRECCIÓN: Asegurar que todos los modales estén ocultos al cargar la página
        const modalIds = [
            'cambiar-operador-modal',
            'cambiar-obra-modal', 
            'registrar-mantenimiento-modal',
            'responsable-obra-modal',
            'kilometraje-modal',
            'delete-confirmation-modal'
        ];
        
        modalIds.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                console.log(`Modal ${modalId} forzado a oculto`);
            }
        });
        
        // Configurar event listeners para el modal de eliminación
        const btnCancelarDelete = document.getElementById('delete-confirmation-modal-btn-cancelar');
        if (btnCancelarDelete) {
            btnCancelarDelete.addEventListener('click', closeDeleteModal);
        }
        
        // Event listener para cerrar modal al hacer clic fuera
        const deleteModal = document.getElementById('delete-confirmation-modal');
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    closeDeleteModal();
                }
            });
        }
        
        // Asegurar que el body no tenga overflow hidden
        document.body.style.overflow = 'auto';
        
        console.log('Inicialización de modales completada - todos ocultos');
    });
</script>
@endpush