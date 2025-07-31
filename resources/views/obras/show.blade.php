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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Vehículo Asignado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->vehiculo ? $obra->vehiculo->marca . ' ' . $obra->vehiculo->modelo . ' - ' . $obra->vehiculo->placas : 'Sin asignar' }}
                            </div>
                            @if($obra->vehiculo)
                            <div class="text-xs text-gray-500 mt-1">
                                Año: {{ $obra->vehiculo->anio ?? 'N/A' }} | 
                                Serie: {{ $obra->vehiculo->n_serie ?? 'N/A' }} |
                                KM Actual: {{ $obra->vehiculo->kilometraje_actual ? number_format($obra->vehiculo->kilometraje_actual) . ' km' : 'N/A' }}
                            </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha Asignación</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->fecha_asignacion ? \Carbon\Carbon::parse($obra->fecha_asignacion)->format('d/m/Y') : 'No definida' }}
                            </div>
                            @if($obra->fecha_asignacion)
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($obra->fecha_asignacion)->format('H:i') }} hrs |
                                {{ \Carbon\Carbon::parse($obra->fecha_asignacion)->diffForHumans() }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Personal Asignado -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Operador</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->operador ? $obra->operador->nombre_completo : 'Sin asignar' }}
                            </div>
                            @if($obra->operador && $obra->operador->categoria)
                            <div class="text-xs text-gray-500 mt-1">
                                Categoría: {{ $obra->operador->categoria->nombre_categoria ?? 'N/A' }}
                            </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Encargado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->encargado ? $obra->encargado->email : 'Sin asignar' }}
                            </div>
                            @if($obra->encargado && $obra->encargado->personal)
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $obra->encargado->personal->nombre_completo ?? 'Usuario del sistema' }}
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
            <!-- Control de Combustible -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Control de Combustible</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Combustible Inicial</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->combustible_inicial ? $obra->combustible_inicial . ' L' : 'No registrado' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Combustible Final</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->combustible_final ? $obra->combustible_final . ' L' : 'No registrado' }}
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Combustible Suministrado</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->combustible_suministrado ? $obra->combustible_suministrado . ' L' : 'No registrado' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Costo Combustible</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ $obra->costo_combustible ? '$' . number_format($obra->costo_combustible, 2) : 'No registrado' }}
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Combustible -->
                    @if($obra->historial_combustible && count($obra->historial_combustible) > 0)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Historial de Combustible</label>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-40 overflow-y-auto">
                            @foreach($obra->historial_combustible as $registro)
                            <div class="bg-white border border-gray-200 rounded p-2 mb-2 last:mb-0">
                                <div class="flex justify-between items-start text-xs">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-700">
                                            {{ $registro['fecha'] ?? 'Fecha no especificada' }}
                                        </span>
                                        <div class="text-gray-600 mt-1">
                                            @if(isset($registro['cantidad']))
                                                Cantidad: {{ $registro['cantidad'] }} L
                                            @endif
                                            @if(isset($registro['costo']))
                                                | Costo: ${{ number_format($registro['costo'], 2) }}
                                            @endif
                                            @if(isset($registro['estacion']))
                                                | Estación: {{ $registro['estacion'] }}
                                            @endif
                                        </div>
                                        @if(isset($registro['observaciones']))
                                        <div class="text-gray-500 mt-1 italic">
                                            {{ $registro['observaciones'] }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

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
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="text-primary mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Documentos Principales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Contrato -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-contract me-1"></i>
                                Contrato
                            </label>
                            @if($obra->tieneContrato())
                                <div class="alert alert-info mb-0" role="alert">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            <span class="small">Archivo disponible</span>
                                        </div>
                                        <a href="{{ $obra->getUrlContrato() }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>Ver archivo
                                        </a>
                                    </div>
                                    <small class="text-muted">Subido: {{ $obra->fecha_subida_contrato?->format('d/m/Y H:i') }}</small>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0 text-center" role="alert">
                                    <i class="fas fa-file-pdf text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="small text-muted mb-0">No hay contrato subido</p>
                                </div>
                            @endif
                        </div>

                        <!-- Fianza -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-invoice-dollar me-1"></i>
                                Fianza
                            </label>
                            @if($obra->tieneFianza())
                                <div class="alert alert-success mb-0" role="alert">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            <span class="small">Archivo disponible</span>
                                        </div>
                                        <a href="{{ $obra->getUrlFianza() }}" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-eye me-1"></i>Ver archivo
                                        </a>
                                    </div>
                                    <small class="text-muted">Subido: {{ $obra->fecha_subida_fianza?->format('d/m/Y H:i') }}</small>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0 text-center" role="alert">
                                    <i class="fas fa-file-pdf text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="small text-muted mb-0">No hay fianza subida</p>
                                </div>
                            @endif
                        </div>

                        <!-- Acta Entrega-Recepción -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-signature me-1"></i>
                                Acta Entrega-Recepción
                            </label>
                            @if($obra->tieneActaEntregaRecepcion())
                                <div class="alert alert-warning mb-0" role="alert">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            <span class="small">Archivo disponible</span>
                                        </div>
                                        <a href="{{ $obra->getUrlActaEntregaRecepcion() }}" target="_blank" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-eye me-1"></i>Ver archivo
                                        </a>
                                    </div>
                                    <small class="text-muted">Subido: {{ $obra->fecha_subida_acta?->format('d/m/Y H:i') }}</small>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0 text-center" role="alert">
                                    <i class="fas fa-file-pdf text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="small text-muted mb-0">No hay acta subida</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas de documentos -->
                    <div class="mt-4 p-3 bg-light border rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-primary mb-1">Progreso de Documentación</h6>
                                <p class="small text-muted mb-2">{{ $obra->getPorcentajeDocumentosCompletados() }}% de documentos principales completados</p>
                            </div>
                            <div class="d-flex gap-2">
                                @if($obra->tieneContrato())
                                    <span class="badge bg-info text-dark">Contrato ✓</span>
                                @endif
                                @if($obra->tieneFianza())
                                    <span class="badge bg-success">Fianza ✓</span>
                                @endif
                                @if($obra->tieneActaEntregaRecepcion())
                                    <span class="badge bg-warning text-dark">Acta ✓</span>
                                @endif
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-gradient-primary" role="progressbar" 
                                 style="width: {{ $obra->getPorcentajeDocumentosCompletados() }}%" 
                                 aria-valuenow="{{ $obra->getPorcentajeDocumentosCompletados() }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
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

            <!-- Fechas de Control -->
            <div class="bg-white border border-gray-300 rounded-lg">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-300">
                    <h3 class="font-semibold text-gray-800">Fechas de Control</h3>
                </div>
                <div class="p-4 space-y-4">
                    @if($obra->fecha_liberacion)
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Liberación</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ \Carbon\Carbon::parse($obra->fecha_liberacion)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Fecha de Creación</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ \Carbon\Carbon::parse($obra->created_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Última Actualización</label>
                            <div class="bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium">
                                {{ \Carbon\Carbon::parse($obra->updated_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    @if($obra->fecha_eliminacion)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h4a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-red-800 font-medium">Obra Eliminada</span>
                        </div>
                        <div class="mt-1">
                            <label class="block text-sm font-medium text-red-600">Fecha de Eliminación</label>
                            <div class="bg-red-600 text-white px-3 py-2 rounded text-sm font-medium mt-1">
                                {{ \Carbon\Carbon::parse($obra->fecha_eliminacion)->format('d/m/Y H:i') }}
                            </div>
                        </div>
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
