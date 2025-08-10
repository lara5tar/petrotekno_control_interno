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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo - Datos Generales -->
        <div class="space-y-6">
            <!-- Datos Generales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Datos Generales</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nombre de la Obra</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->nombre_obra }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">ID de Obra</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ str_pad($obra->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Estado</label>
                            <div class="bg-{{ $obra->estatus === 'en_progreso' ? 'green' : ($obra->estatus === 'completada' ? 'blue' : 'orange') }}-600 text-white px-3 py-2 rounded text-sm font-medium flex items-center">
                                <span class="w-2 h-2 bg-{{ $obra->estatus === 'en_progreso' ? 'green' : ($obra->estatus === 'completada' ? 'blue' : 'orange') }}-300 rounded-full mr-2"></span>
                                {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1 italic">
                                {{ $obra->estatus_descripcion }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Avance (%)</label>
                            <div class="space-y-2">
                                <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                    {{ $obra->avance ?? 0 }}%
                                </div>
                                <!-- Barra de progreso -->
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-{{ $obra->avance >= 100 ? 'green' : ($obra->avance >= 75 ? 'blue' : ($obra->avance >= 50 ? 'yellow' : 'orange')) }}-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ min(100, $obra->avance ?? 0) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha Inicio</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_inicio ? \Carbon\Carbon::parse($obra->fecha_inicio)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha Fin</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_fin ? \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') : 'No definida' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Asignación de Recursos -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Asignación de Recursos</h3>
                </div>
                <div class="p-4 space-y-4">
                    <!-- Vehículo Asignado -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Vehículo Asignado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->vehiculo ? $obra->vehiculo->marca . ' ' . $obra->vehiculo->modelo . ' - ' . $obra->vehiculo->placas : 'Sin asignar' }}
                            </div>
                        </div>
                    </div>

                    @if($obra->vehiculo)
                    <!-- Detalles del Vehículo -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Año</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->vehiculo->anio ?? 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Serie</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->vehiculo->n_serie ?? 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">KM Actual</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->vehiculo->kilometraje_actual ? number_format($obra->vehiculo->kilometraje_actual) . ' km' : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Personal Asignado -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Operador</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->operador ? $obra->operador->nombre_completo : 'Sin asignar' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Encargado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->encargado ? $obra->encargado->nombre_completo : 'Sin asignar' }}
                            </div>
                            @if($obra->encargado && !$obra->encargado->personal)
                            <div class="text-xs text-gray-500 mt-1">
                                Usuario del sistema: {{ $obra->encargado->email }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Fecha de Liberación -->
                    @if($obra->fecha_liberacion)
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Liberación</label>
                            <div class="bg-red-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ \Carbon\Carbon::parse($obra->fecha_liberacion)->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Recursos liberados | {{ \Carbon\Carbon::parse($obra->fecha_liberacion)->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Kilometrajes -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Control de Kilometrajes</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje Inicial</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->kilometraje_inicial ? number_format($obra->kilometraje_inicial) . ' km' : 'No registrado' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje Final</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->kilometraje_final ? number_format($obra->kilometraje_final) . ' km' : 'No registrado' }}
                            </div>
                        </div>
                    </div>

                    @if($obra->kilometraje_inicial && $obra->kilometraje_final && $obra->kilometraje_final > $obra->kilometraje_inicial)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kilometraje Recorrido</label>
                            <div class="bg-blue-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ number_format($obra->kilometraje_final - $obra->kilometraje_inicial) }} km
                            </div>
                        </div>
                        @if($obra->combustible_suministrado && $obra->combustible_suministrado > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Consumo Promedio</label>
                            <div class="bg-green-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ number_format(($obra->kilometraje_final - $obra->kilometraje_inicial) / $obra->combustible_suministrado, 2) }} km/L
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Información Adicional -->
        <div class="space-y-6">
            <!-- Observaciones -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Observaciones</h3>
                </div>
                <div class="p-4">
                    <div class="bg-gray-100 px-3 py-3 rounded text-sm border border-gray-300">
                        {{ $obra->observaciones ?: 'No hay observaciones registradas para esta obra.' }}
                    </div>
                </div>
            </div>

            <!-- Documentos Principales -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Documentos Principales</h3>
                </div>
                <div class="p-4">
                    <!-- Lista de documentos -->
                    <div class="space-y-3 mb-4">
                        <!-- Contrato -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-{{ $obra->tieneContrato() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Contrato</span>
                                    <p class="text-xs text-gray-500">
                                        @if($obra->tieneContrato())
                                            Documento principal disponible
                                        @else
                                            No disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($obra->tieneContrato())
                                <a href="{{ $obra->getUrlContrato() }}" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver
                                </a>
                            @else
                                <span class="text-xs text-red-500 font-medium">Faltante</span>
                            @endif
                        </div>

                        <!-- Fianza -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-{{ $obra->tieneFianza() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Fianza</span>
                                    <p class="text-xs text-gray-500">
                                        @if($obra->tieneFianza())
                                            Garantía del proyecto disponible
                                        @else
                                            No disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($obra->tieneFianza())
                                <a href="{{ $obra->getUrlFianza() }}" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver
                                </a>
                            @else
                                <span class="text-xs text-red-500 font-medium">Faltante</span>
                            @endif
                        </div>

                        <!-- Acta Entrega-Recepción -->
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-{{ $obra->tieneActaEntregaRecepcion() ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Acta Entrega-Recepción</span>
                                    <p class="text-xs text-gray-500">
                                        @if($obra->tieneActaEntregaRecepcion())
                                            Finalización del proyecto disponible
                                        @else
                                            No disponible
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($obra->tieneActaEntregaRecepcion())
                                <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs flex items-center transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver
                                </a>
                            @else
                                <span class="text-xs text-red-500 font-medium">Faltante</span>
                            @endif
                        </div>
                    </div>


                </div>
            </div>

            <!-- Estadísticas y Progreso -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Estadísticas del Proyecto</h3>
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

                    @if($obra->esta_atrasada)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-red-800 font-medium">¡Obra Atrasada!</span>
                        </div>
                        <p class="text-red-700 text-sm mt-1">La fecha de finalización prevista ya ha pasado.</p>
                    </div>
                    @endif
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
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
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
                class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded text-sm transition-colors duration-200 flex items-center space-x-2 shadow-lg"
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
