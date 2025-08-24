@extends('pdf.layouts.base')

@section('title', 'Reporte de Alertas de Mantenimiento')
@section('report-title', 'Reporte de Alertas de Mantenimiento')
@section('report-subtitle', 'Análisis preventivo y alertas de mantenimiento pendiente por vehículo')

@section('content')
    <!-- Sección de Resumen Ejecutivo -->
    @if(isset($resumen) && count($resumen) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['total_alertas'] ?? 0 }}</span>
                        <span class="stat-label">Total Alertas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['vehiculos_afectados'] ?? 0 }}</span>
                        <span class="stat-label">Vehículos Afectados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number text-danger">{{ $resumen['por_urgencia']['critica'] ?? 0 }}</span>
                        <span class="stat-label">Críticas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number text-warning">{{ $resumen['por_urgencia']['alta'] ?? 0 }}</span>
                        <span class="stat-label">Altas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number text-info">{{ $resumen['por_urgencia']['normal'] ?? 0 }}</span>
                        <span class="stat-label">Normales</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($resumen['costo_estimado'] ?? 0, 2) }}</span>
                        <span class="stat-label">Costo Estimado</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Distribución por Tipo de Mantenimiento -->
    @if(isset($resumen['por_tipo']) && count($resumen['por_tipo']) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Distribución por Tipo de Mantenimiento</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['por_tipo']['motor'] ?? 0 }}</span>
                        <span class="stat-label">Motor</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['por_tipo']['transmision'] ?? 0 }}</span>
                        <span class="stat-label">Transmisión</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['por_tipo']['hidraulico'] ?? 0 }}</span>
                        <span class="stat-label">Hidráulico</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['por_tipo']['preventivo'] ?? 0 }}</span>
                        <span class="stat-label">Preventivo</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $resumen['por_tipo']['correctivo'] ?? 0 }}</span>
                        <span class="stat-label">Correctivo</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información del Reporte -->
    <div class="pdf-info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Fecha de Generación:</div>
                <div class="info-value">{{ $fechaGeneracion ?? now()->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total de Alertas:</div>
                <div class="info-value">{{ count($alertas) }} alertas registradas</div>
            </div>
            <div class="info-row">
                <div class="info-label">Criterio de Evaluación:</div>
                <div class="info-value">Basado en intervalos de kilometraje y días transcurridos</div>
            </div>
            @if(isset($resumen['periodo_evaluacion']))
                <div class="info-row">
                    <div class="info-label">Periodo Evaluado:</div>
                    <div class="info-value">{{ $resumen['periodo_evaluacion'] }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alertas Críticas (si existen) -->
    @php
        $alertasCriticas = collect($alertas)->where('nivel_urgencia', 'critica');
    @endphp
    @if($alertasCriticas->count() > 0)
        <div class="pdf-stats-section" style="border-color: #e74c3c; background-color: #fff5f5;">
            <h3 class="stats-title" style="color: #e74c3c;">⚠️ Alertas Críticas - Atención Inmediata</h3>
            <table class="pdf-table">
                <thead>
                    <tr style="background-color: #e74c3c;">
                        <th style="width: 25%;">Vehículo</th>
                        <th style="width: 15%;">Tipo</th>
                        <th style="width: 20%;">Descripción</th>
                        <th style="width: 15%;">Km Actual</th>
                        <th style="width: 15%;">Km Vencimiento</th>
                        <th style="width: 10%;">Días Vencido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alertasCriticas->take(5) as $alerta)
                        <tr>
                            <td class="text-bold">
                                {{ $alerta['vehiculo_info']['nombre_completo'] ?? 'N/A' }}
                                <div class="font-small text-muted">{{ $alerta['vehiculo_info']['placas'] ?? 'Sin placas' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-critico">
                                    {{ ucfirst($alerta['tipo_mantenimiento'] ?? 'N/A') }}
                                </span>
                            </td>
                            <td>{{ $alerta['descripcion'] ?? 'Mantenimiento vencido' }}</td>
                            <td class="text-center">{{ number_format($alerta['kilometraje_actual'] ?? 0) }} km</td>
                            <td class="text-center">{{ number_format($alerta['kilometraje_limite'] ?? 0) }} km</td>
                            <td class="text-center text-danger text-bold">{{ $alerta['dias_vencido'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Tabla Principal de Todas las Alertas -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 20%;">Vehículo</th>
                <th style="width: 12%;">Tipo Mantenimiento</th>
                <th style="width: 8%;">Urgencia</th>
                <th style="width: 10%;">Km Actual</th>
                <th style="width: 10%;">Km Límite</th>
                <th style="width: 8%;">Diferencia</th>
                <th style="width: 8%;">Días</th>
                <th style="width: 10%;">Costo Est.</th>
                <th style="width: 8%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alertas as $index => $alerta)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="text-bold">{{ $alerta['vehiculo_info']['nombre_completo'] ?? 'N/A' }}</div>
                        <div class="font-small text-muted">
                            {{ $alerta['vehiculo_info']['placas'] ?? 'Sin placas' }}
                            @if(isset($alerta['vehiculo_info']['anio']))
                                ({{ $alerta['vehiculo_info']['anio'] }})
                            @endif
                        </div>
                        @if(isset($alerta['vehiculo_info']['n_serie']))
                            <div class="font-small text-muted">Serie: {{ Str::limit($alerta['vehiculo_info']['n_serie'], 15) }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $tipoClass = match($alerta['tipo_mantenimiento'] ?? '') {
                                'motor' => 'status-danger',
                                'transmision' => 'status-warning',
                                'hidraulico' => 'status-info',
                                'preventivo' => 'status-normal',
                                'correctivo' => 'status-alta',
                                default => 'status-normal'
                            };
                        @endphp
                        <span class="status-badge {{ $tipoClass }}">
                            {{ ucfirst($alerta['tipo_mantenimiento'] ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php
                            $urgenciaClass = match($alerta['nivel_urgencia'] ?? '') {
                                'critica' => 'status-critico',
                                'alta' => 'status-alta',
                                'normal' => 'status-normal',
                                default => 'status-normal'
                            };
                        @endphp
                        <span class="status-badge {{ $urgenciaClass }}">
                            {{ ucfirst($alerta['nivel_urgencia'] ?? 'Normal') }}
                        </span>
                    </td>
                    <td class="text-center">
                        {{ number_format($alerta['kilometraje_actual'] ?? 0) }} km
                    </td>
                    <td class="text-center">
                        {{ number_format($alerta['kilometraje_limite'] ?? 0) }} km
                    </td>
                    <td class="text-center">
                        @php
                            $diferencia = ($alerta['kilometraje_actual'] ?? 0) - ($alerta['kilometraje_limite'] ?? 0);
                            $diferenciaClass = $diferencia > 0 ? 'text-danger' : 'text-success';
                        @endphp
                        <span class="text-bold {{ $diferenciaClass }}">
                            {{ $diferencia > 0 ? '+' : '' }}{{ number_format($diferencia) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if(isset($alerta['dias_vencido']) && $alerta['dias_vencido'] > 0)
                            <span class="text-danger text-bold">{{ $alerta['dias_vencido'] }}</span>
                        @elseif(isset($alerta['dias_restantes']))
                            <span class="text-success">{{ $alerta['dias_restantes'] }}</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if(isset($alerta['costo_estimado']))
                            ${{ number_format($alerta['costo_estimado'], 2) }}
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(isset($alerta['vehiculo_info']['estatus']))
                            @php
                                $vehiculoEstatus = match($alerta['vehiculo_info']['estatus']) {
                                    'disponible' => 'status-disponible',
                                    'asignado' => 'status-asignado',
                                    'mantenimiento' => 'status-mantenimiento',
                                    'fuera_servicio' => 'status-fuera-servicio',
                                    'baja' => 'status-baja',
                                    default => 'status-disponible'
                                };
                            @endphp
                            <span class="status-badge {{ $vehiculoEstatus }}">
                                {{ ucfirst(str_replace('_', ' ', $alerta['vehiculo_info']['estatus'])) }}
                            </span>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted p-15">
                        No se encontraron alertas de mantenimiento pendientes
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Recomendaciones -->
    @if(count($alertas) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Recomendaciones y Plan de Acción</h3>
            <div style="padding: 10px; line-height: 1.6;">
                <div style="margin-bottom: 10px;">
                    <strong>Prioridad Inmediata:</strong>
                    @if($alertasCriticas->count() > 0)
                        Atender {{ $alertasCriticas->count() }} alertas críticas que requieren mantenimiento inmediato.
                    @else
                        No hay alertas críticas pendientes.
                    @endif
                </div>
                
                @php
                    $alertasAltas = collect($alertas)->where('nivel_urgencia', 'alta');
                    $alertasNormales = collect($alertas)->where('nivel_urgencia', 'normal');
                @endphp
                
                @if($alertasAltas->count() > 0)
                    <div style="margin-bottom: 10px;">
                        <strong>Prioridad Alta:</strong>
                        Programar mantenimiento para {{ $alertasAltas->count() }} vehículos en las próximas 2 semanas.
                    </div>
                @endif
                
                @if($alertasNormales->count() > 0)
                    <div style="margin-bottom: 10px;">
                        <strong>Mantenimiento Programado:</strong>
                        Considerar {{ $alertasNormales->count() }} vehículos para mantenimiento preventivo en el próximo mes.
                    </div>
                @endif
                
                @if(isset($resumen['costo_estimado']) && $resumen['costo_estimado'] > 0)
                    <div style="margin-bottom: 10px;">
                        <strong>Presupuesto Requerido:</strong>
                        Se estima un costo total de ${{ number_format($resumen['costo_estimado'], 2) }} para completar todos los mantenimientos pendientes.
                    </div>
                @endif
                
                <div style="margin-bottom: 10px;">
                    <strong>Frecuencia de Revisión:</strong>
                    Se recomienda revisar este reporte semanalmente para mantener la flota en condiciones óptimas.
                </div>
            </div>
        </div>
    @endif
@endsection

@section('footer-info')
    Reporte de alertas generado {{ isset($fechaGeneracion) ? $fechaGeneracion : now()->format('d/m/Y H:i:s') }} - {{ count($alertas) }} alertas identificadas
@endsection
