@extends('pdf.layouts.base')

@section('title', 'Historial de Obras por Vehículo')
@section('report-title', 'Historial de Obras por Vehículo')
@section('report-subtitle', 'Reporte individual del historial completo de asignaciones por activo específico')

@section('content')
    <!-- Información del Vehículo -->
    @if(isset($vehiculo))
        <div class="pdf-info-section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Vehículo:</div>
                    <div class="info-value text-bold">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Placas:</div>
                    <div class="info-value">{{ $vehiculo->placas ?: 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. Serie:</div>
                    <div class="info-value">{{ $vehiculo->n_serie ?: 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ubicación:</div>
                    <div class="info-value">
                        @if($vehiculo->estado || $vehiculo->municipio)
                            {{ $vehiculo->estado ?: 'Sin estado' }}
                            @if($vehiculo->municipio)
                                - {{ $vehiculo->municipio }}
                            @endif
                        @else
                            Sin ubicación
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado Actual:</div>
                    <div class="info-value">
                        @php
                            $statusValue = $vehiculo->estatus->value ?? $vehiculo->estatus;
                            $statusClass = match($statusValue) {
                                'disponible' => 'status-disponible',
                                'asignado' => 'status-asignado',
                                'mantenimiento' => 'status-mantenimiento',
                                'fuera_servicio' => 'status-fuera-servicio',
                                'baja' => 'status-baja',
                                default => 'status-disponible'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $statusValue)) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kilometraje Actual:</div>
                    <div class="info-value">{{ $vehiculo->kilometraje_actual ? number_format($vehiculo->kilometraje_actual) . ' km' : 'Sin registro' }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen de Asignaciones</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_asignaciones'] ?? 0 }}</span>
                        <span class="stat-label">Total Asignaciones</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['obras_activas'] ?? 0 }}</span>
                        <span class="stat-label">Obras Activas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['obras_finalizadas'] ?? 0 }}</span>
                        <span class="stat-label">Obras Finalizadas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_kilometros'] ?? 0 }}</span>
                        <span class="stat-label">Total Kilómetros</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['promedio_dias'] ?? 0 }}</span>
                        <span class="stat-label">Promedio Días</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros -->
    @if(isset($filtros) && count($filtros) > 0)
        <div class="pdf-info-section">
            <div class="info-grid">
                @if(isset($filtros['vehiculo_id']) && $filtros['vehiculo_id'])
                    <div class="info-row">
                        <div class="info-label">Vehículo ID:</div>
                        <div class="info-value">{{ $filtros['vehiculo_id'] }}</div>
                    </div>
                @endif
                @if(isset($filtros['fecha_inicio']) && $filtros['fecha_inicio'])
                    <div class="info-row">
                        <div class="info-label">Fecha Desde:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($filtros['fecha_inicio'])->format('d/m/Y') }}</div>
                    </div>
                @endif
                @if(isset($filtros['fecha_fin']) && $filtros['fecha_fin'])
                    <div class="info-row">
                        <div class="info-label">Fecha Hasta:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y') }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Total de Registros:</div>
                    <div class="info-value">{{ count($asignaciones) }} asignaciones</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla Principal de Asignaciones -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 25%;">Obra</th>
                <th style="width: 15%;">Operador</th>
                <th style="width: 12%;">Fecha Asignación</th>
                <th style="width: 12%;">Fecha Finalización</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 10%;">Km Inicial</th>
                <th style="width: 10%;">Km Final</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asignaciones as $index => $asignacion)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">
                        <div>{{ $asignacion->obra->nombre ?? 'N/A' }}</div>
                        @if($asignacion->obra && $asignacion->obra->cliente)
                            <div class="font-small text-muted">Cliente: {{ $asignacion->obra->cliente }}</div>
                        @endif
                        @if($asignacion->obra && $asignacion->obra->ubicacion)
                            <div class="font-small text-muted">{{ $asignacion->obra->ubicacion }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="text-bold">{{ $asignacion->operador->nombre_completo ?? 'N/A' }}</div>
                        @if($asignacion->operador && $asignacion->operador->numero_licencia)
                            <div class="font-small text-muted">Lic: {{ $asignacion->operador->numero_licencia }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $asignacion->fecha_asignacion ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="text-center">
                        @if($asignacion->fecha_finalizacion)
                            {{ \Carbon\Carbon::parse($asignacion->fecha_finalizacion)->format('d/m/Y') }}
                        @else
                            <span class="text-muted font-small">En curso</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $estadoClass = match($asignacion->estado) {
                                'activo' => 'status-activo',
                                'finalizado' => 'status-finalizado',
                                'pendiente' => 'status-pendiente',
                                default => 'status-pendiente'
                            };
                        @endphp
                        <span class="status-badge {{ $estadoClass }}">
                            {{ ucfirst($asignacion->estado) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($asignacion->kilometraje_inicial)
                            {{ number_format($asignacion->kilometraje_inicial) }} km
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($asignacion->kilometraje_final)
                            <span class="text-bold">{{ number_format($asignacion->kilometraje_final) }} km</span>
                            @if($asignacion->kilometraje_inicial)
                                @php
                                    $kmRecorridos = $asignacion->kilometraje_final - $asignacion->kilometraje_inicial;
                                @endphp
                                <div class="font-small text-info">+{{ number_format($kmRecorridos) }} km</div>
                            @endif
                        @elseif($asignacion->estado === 'activo')
                            <span class="text-muted font-small">En curso</span>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted p-15">
                        No se encontraron asignaciones para este vehículo
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen de Kilometrajes por Obra -->
    @if(count($asignaciones) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Análisis de Kilometraje</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    @php
                        $asignacionesConKm = $asignaciones->whereNotNull('kilometraje_inicial')->whereNotNull('kilometraje_final');
                        $totalKmRecorridos = $asignacionesConKm->sum(function($a) {
                            return $a->kilometraje_final - $a->kilometraje_inicial;
                        });
                        $promedioKmPorObra = $asignacionesConKm->count() > 0 ? $totalKmRecorridos / $asignacionesConKm->count() : 0;
                        $obraConMasKm = $asignacionesConKm->sortByDesc(function($a) {
                            return $a->kilometraje_final - $a->kilometraje_inicial;
                        })->first();
                    @endphp
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($totalKmRecorridos) }}</span>
                        <span class="stat-label">Total Km Recorridos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($promedioKmPorObra, 0) }}</span>
                        <span class="stat-label">Promedio Km/Obra</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $asignacionesConKm->count() }}</span>
                        <span class="stat-label">Obras con Km</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $asignaciones->where('estado', 'activo')->count() }}</span>
                        <span class="stat-label">Asignaciones Activas</span>
                    </div>
                    @if($obraConMasKm)
                        <div class="stat-item">
                            <span class="stat-number">{{ number_format($obraConMasKm->kilometraje_final - $obraConMasKm->kilometraje_inicial) }}</span>
                            <span class="stat-label">Mayor Km en Obra</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

@section('footer-info')
    Historial de obras para vehículo {{ isset($vehiculo) ? $vehiculo->marca . ' ' . $vehiculo->modelo : 'N/A' }} - {{ count($asignaciones) }} asignaciones
@endsection
