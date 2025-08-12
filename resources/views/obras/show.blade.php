@extends('layouts.app')

@section('title', 'Detalles de la Obra')

@section('header', 'Detalles de la Obra')

@section('content')
{{-- Breadcrumb --}}
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => true],
    ['label' => 'Obras', 'url' => route('obras.index')],
    ['label' => 'Detalle de la Obra']
]" />

<!-- Contenido Principal -->
<div class="p-6">
    <!-- Header con información principal -->
    <div class="bg-white border border-gray-300 rounded-lg mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $obra->nombre_obra }}</h2>
                    <p class="text-blue-100 text-sm">ID: {{ str_pad($obra->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-{{ $obra->estatus === 'en_progreso' ? 'green' : ($obra->estatus === 'completada' ? 'blue' : 'orange') }}-500 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                        <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
                        {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                    </div>
                    @if($obra->esta_atrasada)
                    <div class="bg-red-500 text-white px-3 py-1 rounded text-xs mt-2 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Atrasada
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Barra de progreso prominente -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Progreso del Proyecto</span>
                <span class="text-lg font-bold text-gray-800">{{ $obra->avance ?? 0 }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-{{ $obra->avance >= 100 ? 'green' : ($obra->avance >= 75 ? 'blue' : ($obra->avance >= 50 ? 'yellow' : 'orange')) }}-500 h-3 rounded-full transition-all duration-500 shadow-sm" 
                     style="width: {{ min(100, $obra->avance ?? 0) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Secciones organizadas en dos columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- COLUMNA IZQUIERDA: Información del Proyecto -->
        <div class="space-y-6">
            
            <!-- Información Básica del Proyecto -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Información del Proyecto
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Inicio</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_inicio ? \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Finalización</label>
                            <div class="bg-{{ $obra->fecha_fin && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($obra->fecha_fin)) ? 'red' : 'blue' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_fin ? \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas y Cronograma -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        Cronograma y Estadísticas
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Días Transcurridos</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->dias_transcurridos }} días
                            </div>
                        </div>
                        @if($obra->dias_restantes !== null)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Días Restantes</label>
                            <div class="bg-{{ $obra->dias_restantes > 0 ? 'green' : 'red' }}-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->dias_restantes }} días
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($obra->duracion_total)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Duración Total</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->duracion_total }} días
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Progreso de Tiempo</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->porcentaje_tiempo_transcurrido }}%
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Observaciones -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Observaciones y Notas
                    </h3>
                </div>
                <div class="p-4">
                    <div class="bg-yellow-50 border border-yellow-200 px-4 py-3 rounded text-sm">
                        {{ $obra->observaciones ?: 'No hay observaciones registradas para esta obra.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Recursos y Documentación -->
        <div class="space-y-6">
            
            <!-- Asignación de Recursos -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Recursos Asignados
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Personal Asignado -->
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Personal</h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Encargado</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $obra->encargado ? $obra->encargado->nombre_completo : 'Sin asignar' }}
                                    </div>
                                    @if($obra->encargado && !$obra->encargado->personal)
                                    <div class="text-xs text-blue-600 mt-1">
                                        {{ $obra->encargado->email }}
                                    </div>
                                    @endif
                                </div>
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Operador</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $obra->operador ? $obra->operador->nombre_completo : 'Sin asignar' }}
                                    </div>
                                </div>
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Vehículo Asignado -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Vehículo</h4>
                        @if($obra->vehiculo)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-medium text-gray-800">
                                        {{ $obra->vehiculo->marca }} {{ $obra->vehiculo->modelo }}
                                    </div>
                                    <div class="text-sm text-gray-600">{{ $obra->vehiculo->placas }}</div>
                                </div>
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <span class="text-gray-500">Año:</span>
                                    <div class="font-medium">{{ $obra->vehiculo->anio ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Serie:</span>
                                    <div class="font-medium">{{ Str::limit($obra->vehiculo->n_serie ?? 'N/A', 8) }}</div>
                                </div>
                                <div>
                                    <span class="text-gray-500">KM:</span>
                                    <div class="font-medium">{{ $obra->vehiculo->kilometraje_actual ? number_format($obra->vehiculo->kilometraje_actual) : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div class="text-sm text-red-800">Sin vehículo asignado</div>
                        </div>
                        @endif
                    </div>

                    <!-- Fecha de Liberación -->
                    @if($obra->fecha_liberacion)
                    <div class="border-t border-gray-200 pt-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-red-800">Recursos Liberados</div>
                                    <div class="text-xs text-red-600">
                                        {{ \Carbon\Carbon::parse($obra->fecha_liberacion)->format('d/m/Y H:i') }}
                                        ({{ \Carbon\Carbon::parse($obra->fecha_liberacion)->diffForHumans() }})
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documentos del Proyecto -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v2m0 0h14" />
                        </svg>
                        Documentación
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <!-- Contrato -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border {{ $obra->tieneContrato() ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-{{ $obra->tieneContrato() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Contrato</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $obra->tieneContrato() ? 'Disponible' : 'No disponible' }}
                                    </div>
                                </div>
                            </div>
                            @if($obra->tieneContrato())
                                <a href="{{ $obra->getUrlContrato() }}" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                    Ver
                                </a>
                            @else
                                <span class="text-xs text-red-600 font-medium">Faltante</span>
                            @endif
                        </div>

                        <!-- Fianza -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border {{ $obra->tieneFianza() ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-{{ $obra->tieneFianza() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Fianza</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $obra->tieneFianza() ? 'Disponible' : 'No disponible' }}
                                    </div>
                                </div>
                            </div>
                            @if($obra->tieneFianza())
                                <a href="{{ $obra->getUrlFianza() }}" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                    Ver
                                </a>
                            @else
                                <span class="text-xs text-red-600 font-medium">Faltante</span>
                            @endif
                        </div>

                        <!-- Acta Entrega-Recepción -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border {{ $obra->tieneActaEntregaRecepcion() ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-{{ $obra->tieneActaEntregaRecepcion() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Acta Entrega-Recepción</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $obra->tieneActaEntregaRecepcion() ? 'Disponible' : 'No disponible' }}
                                    </div>
                                </div>
                            </div>
                            @if($obra->tieneActaEntregaRecepcion())
                                <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200">
                                    Ver
                                </a>
                            @else
                                <span class="text-xs text-red-600 font-medium">Faltante</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de Acción Flotantes -->
<div class="fixed bottom-6 right-6 flex space-x-3 z-50">
    @hasPermission('actualizar_obras')
    <!-- Botón Editar -->
    <a href="{{ route('obras.edit', $obra->id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
       title="Editar Obra">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
        <span>Editar</span>
    </a>
    @endhasPermission

    @hasPermission('eliminar_obras')
    <!-- Botón Eliminar -->
    <form action="{{ route('obras.destroy', $obra->id) }}" 
          method="POST" 
          class="inline" 
          onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta obra? Esta acción no se puede deshacer.')">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                title="Eliminar Obra">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2.5 4a.5.5 0 01.5-.5h4a.5.5 0 01.5.5v2a.5.5 0 01-.5.5H7a.5.5 0 01-.5-.5V9z" clip-rule="evenodd" />
            </svg>
            <span>Eliminar</span>
        </button>
    </form>
    @endhasPermission
</div>

@endsection
