@extends('pdf.layouts.base')

@section('title', 'Historial de Mantenimientos por Vehículo')
@section('report-title', 'Historial de Mantenimientos por Vehículo')
@section('report-subtitle', 'Reporte completo del historial de mantenimientos por activo específico')

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
                    <div class="info-label">No. Serie:</div>
                    <div class="info-value">{{ $vehiculo->n_serie ?: 'N/A' }}</div>
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
                <div class="info-row">
                    <div class="info-label">Intervalos de Mantenimiento:</div>
                    <div class="info-value">
                        <div>Motor: {{ $vehiculo->intervalo_km_motor ? number_format($vehiculo->intervalo_km_motor) . ' km' : 'No definido' }}</div>
                        <div>Transmisión: {{ $vehiculo->intervalo_km_transmision ? number_format($vehiculo->intervalo_km_transmision) . ' km' : 'No definido' }}</div>
                        <div>Hidráulico: {{ $vehiculo->intervalo_km_hidraulico ? number_format($vehiculo->intervalo_km_hidraulico) . ' km' : 'No definido' }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_mantenimientos'] ?? 0 }}</span>
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
                @if(isset($filtros['tipo_mantenimiento']) && $filtros['tipo_mantenimiento'])
                    <div class="info-row">
                        <div class="info-label">Tipo Filtrado:</div>
                        <div class="info-value">{{ ucfirst($filtros['tipo_mantenimiento']) }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Total de Registros:</div>
                    <div class="info-value">{{ count($mantenimientos) }} mantenimientos</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabla Principal de Mantenimientos -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width: 4%">#</th>
                <th style="width: 8%;">Fecha</th>
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
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center font-small">
                        {{ $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A' }}
                        @if($mantenimiento->fecha_fin)
                            <br><span class="text-muted">{{ $mantenimiento->fecha_fin->format('d/m/Y') }}</span>
                        @else
                            <br><span class="text-muted font-small">En proceso</span>
                        @endif
                    </td>
                    <td class="text-center font-small">
                        @php
                            $tipoFormatted = match($mantenimiento->tipo_mantenimiento) {
                                'PREVENTIVO' => 'Preventivo',
                                'CORRECTIVO' => 'Correctivo',
                                default => ucfirst($mantenimiento->tipo_mantenimiento ?? 'N/A')
                            };
                        @endphp
                        {{ $tipoFormatted }}
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
                            ${{ number_format($mantenimiento->costo, 2) }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-15">
                        No se encontraron mantenimientos para este vehículo
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Análisis de Costos por Sistema -->
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
                            <span class="stat-number">${{ number_format($sistema['costo_total'], 2) }}</span>
                            <span class="stat-label">{{ $sistema['sistema'] }}</span>
                            <span class="stat-sublabel">{{ $sistema['total_servicios'] }} servicios</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Análisis de Costos por Tipo de Mantenimiento -->
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Análisis de Costos por Tipo de Mantenimiento</h3>
            @php
                $tiposCosto = $mantenimientos->whereNotNull('costo')
                    ->groupBy('tipo_mantenimiento')
                    ->map(function($grupo, $tipo) {
                        return [
                            'tipo' => match($tipo) {
                                'PREVENTIVO' => 'Preventivo',
                                'CORRECTIVO' => 'Correctivo',
                                default => ucfirst($tipo)
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
                    @foreach($tiposCosto as $tipo)
                        <div class="stat-item">
                            <span class="stat-number">${{ number_format($tipo['costo_total'], 2) }}</span>
                            <span class="stat-label">{{ $tipo['tipo'] }}</span>
                            <span class="stat-sublabel">{{ $tipo['total_servicios'] }} servicios</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Próximos Mantenimientos Recomendados -->
    @if(isset($vehiculo) && $vehiculo->kilometraje_actual)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Próximos Mantenimientos Recomendados</h3>
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Tipo de Mantenimiento</th>
                        <th style="width: 20%;">Intervalo (km)</th>
                        <th style="width: 20%;">Último Mantenimiento</th>
                        <th style="width: 20%;">Próximo Mantenimiento</th>
                        <th style="width: 15%;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ultimoMotor = $mantenimientos->where('tipo_mantenimiento', 'motor')->sortByDesc('kilometraje')->first();
                        $ultimaTransmision = $mantenimientos->where('tipo_mantenimiento', 'transmision')->sortByDesc('kilometraje')->first();
                        $ultimoHidraulico = $mantenimientos->where('tipo_mantenimiento', 'hidraulico')->sortByDesc('kilometraje')->first();
                        
                        $proximoMotor = $ultimoMotor && $vehiculo->intervalo_km_motor 
                            ? $ultimoMotor->kilometraje + $vehiculo->intervalo_km_motor 
                            : ($vehiculo->intervalo_km_motor ?? 0);
                        $proximaTransmision = $ultimaTransmision && $vehiculo->intervalo_km_transmision 
                            ? $ultimaTransmision->kilometraje + $vehiculo->intervalo_km_transmision 
                            : ($vehiculo->intervalo_km_transmision ?? 0);
                        $proximoHidraulico = $ultimoHidraulico && $vehiculo->intervalo_km_hidraulico 
                            ? $ultimoHidraulico->kilometraje + $vehiculo->intervalo_km_hidraulico 
                            : ($vehiculo->intervalo_km_hidraulico ?? 0);
                    @endphp
                    
                    @if($vehiculo->intervalo_km_motor)
                        <tr>
                            <td class="text-bold">Mantenimiento de Motor</td>
                            <td class="text-center">{{ number_format($vehiculo->intervalo_km_motor) }} km</td>
                            <td class="text-center">
                                @if($ultimoMotor)
                                    {{ number_format($ultimoMotor->kilometraje) }} km
                                    <div class="font-small text-muted">{{ \Carbon\Carbon::parse($ultimoMotor->fecha_mantenimiento)->format('d/m/Y') }}</div>
                                @else
                                    <span class="text-muted">Sin registro</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ number_format($proximoMotor) }} km
                            </td>
                            <td class="text-center">
                                @php
                                    $diferencia = $proximoMotor - $vehiculo->kilometraje_actual;
                                    $estadoMantenimiento = $diferencia <= 0 ? 'Vencido' : ($diferencia <= 1000 ? 'Próximo' : 'Al día');
                                    $classEstado = $diferencia <= 0 ? 'status-critico' : ($diferencia <= 1000 ? 'status-alta' : 'status-normal');
                                @endphp
                                <span class="status-badge {{ $classEstado }}">{{ $estadoMantenimiento }}</span>
                            </td>
                        </tr>
                    @endif
                    
                    @if($vehiculo->intervalo_km_transmision)
                        <tr>
                            <td class="text-bold">Mantenimiento de Transmisión</td>
                            <td class="text-center">{{ number_format($vehiculo->intervalo_km_transmision) }} km</td>
                            <td class="text-center">
                                @if($ultimaTransmision)
                                    {{ number_format($ultimaTransmision->kilometraje) }} km
                                    <div class="font-small text-muted">{{ \Carbon\Carbon::parse($ultimaTransmision->fecha_mantenimiento)->format('d/m/Y') }}</div>
                                @else
                                    <span class="text-muted">Sin registro</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ number_format($proximaTransmision) }} km
                            </td>
                            <td class="text-center">
                                @php
                                    $diferencia = $proximaTransmision - $vehiculo->kilometraje_actual;
                                    $estadoMantenimiento = $diferencia <= 0 ? 'Vencido' : ($diferencia <= 1000 ? 'Próximo' : 'Al día');
                                    $classEstado = $diferencia <= 0 ? 'status-critico' : ($diferencia <= 1000 ? 'status-alta' : 'status-normal');
                                @endphp
                                <span class="status-badge {{ $classEstado }}">{{ $estadoMantenimiento }}</span>
                            </td>
                        </tr>
                    @endif
                    
                    @if($vehiculo->intervalo_km_hidraulico)
                        <tr>
                            <td class="text-bold">Mantenimiento Hidráulico</td>
                            <td class="text-center">{{ number_format($vehiculo->intervalo_km_hidraulico) }} km</td>
                            <td class="text-center">
                                @if($ultimoHidraulico)
                                    {{ number_format($ultimoHidraulico->kilometraje) }} km
                                    <div class="font-small text-muted">{{ \Carbon\Carbon::parse($ultimoHidraulico->fecha_mantenimiento)->format('d/m/Y') }}</div>
                                @else
                                    <span class="text-muted">Sin registro</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ number_format($proximoHidraulico) }} km
                            </td>
                            <td class="text-center">
                                @php
                                    $diferencia = $proximoHidraulico - $vehiculo->kilometraje_actual;
                                    $estadoMantenimiento = $diferencia <= 0 ? 'Vencido' : ($diferencia <= 1000 ? 'Próximo' : 'Al día');
                                    $classEstado = $diferencia <= 0 ? 'status-critico' : ($diferencia <= 1000 ? 'status-alta' : 'status-normal');
                                @endphp
                                <span class="status-badge {{ $classEstado }}">{{ $estadoMantenimiento }}</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
@endsection

@section('footer-info')
    Historial de mantenimientos para vehículo {{ isset($vehiculo) ? $vehiculo->marca . ' ' . $vehiculo->modelo : 'N/A' }} - {{ count($mantenimientos) }} registros
@endsection
