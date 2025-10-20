@extends('pdf.layouts.base')

@section('title', 'Reporte de Mantenimientos Filtrados')
@section('report-title', 'Reporte de Mantenimientos Filtrados')
@section('report-subtitle', 'Listado de mantenimientos según criterios de filtrado aplicados')

@section('content')
    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total'] ?? 0 }}</span>
                        <span class="stat-label">Total Mantenimientos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($estadisticas['costo_total'] ?? 0, 2) }}</span>
                        <span class="stat-label">Costo Total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($estadisticas['costo_promedio'] ?? 0, 2) }}</span>
                        <span class="stat-label">Costo Promedio</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['completados'] ?? 0 }}</span>
                        <span class="stat-label">Completados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['en_proceso'] ?? 0 }}</span>
                        <span class="stat-label">En Proceso</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas por Tipo y Sistema -->
        <div class="pdf-stats-section">
            <h3 class="stats-title">Distribución por Tipo y Sistema</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_tipo_servicio']['PREVENTIVO'] ?? 0 }}</span>
                        <span class="stat-label">Preventivos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_tipo_servicio']['CORRECTIVO'] ?? 0 }}</span>
                        <span class="stat-label">Correctivos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_sistema']['motor'] ?? 0 }}</span>
                        <span class="stat-label">Motor</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_sistema']['transmision'] ?? 0 }}</span>
                        <span class="stat-label">Transmisión</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_sistema']['hidraulico'] ?? 0 }}</span>
                        <span class="stat-label">Hidráulico</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_sistema']['general'] ?? 0 }}</span>
                        <span class="stat-label">General</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros Aplicados -->
    @if(isset($filtrosAplicados) && count(array_filter($filtrosAplicados)) > 0)
        <div class="pdf-info-section">
            <h3 class="info-title">Filtros Aplicados</h3>
            <div class="info-grid">
                @if(isset($filtrosAplicados['buscar']) && $filtrosAplicados['buscar'])
                    <div class="info-row">
                        <div class="info-label">Búsqueda:</div>
                        <div class="info-value">{{ $filtrosAplicados['buscar'] }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['vehiculo_id']) && $filtrosAplicados['vehiculo_id'])
                    <div class="info-row">
                        <div class="info-label">Vehículo ID:</div>
                        <div class="info-value">{{ $filtrosAplicados['vehiculo_id'] }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['tipo_servicio']) && $filtrosAplicados['tipo_servicio'])
                    <div class="info-row">
                        <div class="info-label">Tipo de Servicio:</div>
                        <div class="info-value">{{ strtoupper($filtrosAplicados['tipo_servicio']) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['sistema_vehiculo']) && $filtrosAplicados['sistema_vehiculo'])
                    <div class="info-row">
                        <div class="info-label">Sistema:</div>
                        <div class="info-value">{{ strtoupper($filtrosAplicados['sistema_vehiculo']) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['fecha_inicio_desde']) && $filtrosAplicados['fecha_inicio_desde'])
                    <div class="info-row">
                        <div class="info-label">Fecha Desde:</div>
                        <div class="info-value">{{ date('d/m/Y', strtotime($filtrosAplicados['fecha_inicio_desde'])) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['fecha_inicio_hasta']) && $filtrosAplicados['fecha_inicio_hasta'])
                    <div class="info-row">
                        <div class="info-label">Fecha Hasta:</div>
                        <div class="info-value">{{ date('d/m/Y', strtotime($filtrosAplicados['fecha_inicio_hasta'])) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['kilometraje_min']) && $filtrosAplicados['kilometraje_min'])
                    <div class="info-row">
                        <div class="info-label">Kilometraje Mínimo:</div>
                        <div class="info-value">{{ number_format($filtrosAplicados['kilometraje_min']) }} km</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['kilometraje_max']) && $filtrosAplicados['kilometraje_max'])
                    <div class="info-row">
                        <div class="info-label">Kilometraje Máximo:</div>
                        <div class="info-value">{{ number_format($filtrosAplicados['kilometraje_max']) }} km</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['costo_min']) && $filtrosAplicados['costo_min'])
                    <div class="info-row">
                        <div class="info-label">Costo Mínimo:</div>
                        <div class="info-value">${{ number_format($filtrosAplicados['costo_min'], 2) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['costo_max']) && $filtrosAplicados['costo_max'])
                    <div class="info-row">
                        <div class="info-label">Costo Máximo:</div>
                        <div class="info-value">${{ number_format($filtrosAplicados['costo_max'], 2) }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Total de Registros:</div>
                    <div class="info-value">{{ count($mantenimientos) }} mantenimientos</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Generación:</div>
                    <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Espacio para separación entre secciones -->
    <div style="margin-bottom: 20px;"></div>

    <!-- Tabla Principal de Mantenimientos -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width: 6%">ID</th>
                <th style="width: 8%;">Fecha</th>
                <th style="width: 15%;">Vehículo</th>
                <th style="width: 8%;">Placas</th>
                <th style="width: 10%;">Tipo</th>
                <th style="width: 8%;">Sistema</th>
                <th style="width: 25%;">Descripción</th>
                <th style="width: 8%;">Kilometraje</th>
                <th style="width: 7%;">Costo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mantenimientos as $index => $mantenimiento)
                <tr>
                    <td class="text-center">{{ str_pad($mantenimiento->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-center font-small">
                        {{ $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A' }}
                        @if($mantenimiento->fecha_fin)
                            <br><span class="text-muted">{{ $mantenimiento->fecha_fin->format('d/m/Y') }}</span>
                        @else
                            <br><span class="text-muted font-small">En proceso</span>
                        @endif
                    </td>
                    <td class="font-small">
                        @if($mantenimiento->vehiculo)
                            <strong>{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</strong>
                            @if($mantenimiento->vehiculo->anio)
                                <br><span class="text-muted">({{ $mantenimiento->vehiculo->anio }})</span>
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-center font-small">
                        {{ $mantenimiento->vehiculo->placas ?? 'N/A' }}
                    </td>
                    <td class="text-center">
                        @php
                            $tipoClass = $mantenimiento->tipo_servicio === 'PREVENTIVO' ? 'status-normal' : 'status-alta';
                        @endphp
                        <span class="status-badge {{ $tipoClass }}">
                            {{ $mantenimiento->tipo_servicio ? strtoupper($mantenimiento->tipo_servicio) : 'N/A' }}
                        </span>
                    </td>
                    <td class="text-center font-small">
                        @php
                            $sistemaFormatted = match($mantenimiento->sistema_vehiculo) {
                                'motor' => 'MOTOR',
                                'transmision' => 'TRANSMISIÓN',
                                'hidraulico' => 'HIDRÁULICO',
                                'general' => 'GENERAL',
                                default => strtoupper($mantenimiento->sistema_vehiculo ?? 'N/A')
                            };
                        @endphp
                        {{ $sistemaFormatted }}
                    </td>
                    <td class="font-small">
                        {{ Str::limit($mantenimiento->descripcion ?? 'Sin descripción', 80) }}
                    </td>
                    <td class="text-center font-small">
                        @if($mantenimiento->kilometraje_servicio)
                            {{ number_format($mantenimiento->kilometraje_servicio) }}
                            <br><span class="text-muted font-small">km</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-right font-small">
                        @if($mantenimiento->costo)
                            <strong>${{ number_format($mantenimiento->costo, 0) }}</strong>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted p-15">
                        No se encontraron mantenimientos con los criterios especificados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen de Costos por Sistema -->
    @if(count($mantenimientos) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Análisis de Costos por Sistema de Vehículo</h3>
            @php
                $sistemasCosto = $mantenimientos->whereNotNull('costo')
                    ->groupBy('sistema_vehiculo')
                    ->map(function($grupo, $sistema) {
                        return [
                            'sistema' => match($sistema) {
                                'motor' => 'MOTOR',
                                'transmision' => 'TRANSMISIÓN',
                                'hidraulico' => 'HIDRÁULICO',
                                'general' => 'GENERAL',
                                default => strtoupper($sistema)
                            },
                            'total_servicios' => $grupo->count(),
                            'costo_total' => $grupo->sum('costo'),
                            'costo_promedio' => $grupo->avg('costo')
                        ];
                    })
                    ->sortByDesc('costo_total');
            @endphp

            <div class="stats-grid">
                <div class="stats-row">
                    @foreach($sistemasCosto as $sistema)
                        <div class="stat-item">
                            <span class="stat-number">${{ number_format($sistema['costo_total'], 0) }}</span>
                            <span class="stat-label">{{ $sistema['sistema'] }}</span>
                            <span class="font-small text-muted">{{ $sistema['total_servicios'] }} servicios</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection

@section('footer-info')
    Reporte de mantenimientos filtrados - {{ count($mantenimientos) }} registros
@endsection