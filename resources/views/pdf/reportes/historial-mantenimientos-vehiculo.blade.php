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
            <h3 class="stats-title">Resumen de Mantenimientos</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_mantenimientos'] ?? 0 }}</span>
                        <span class="stat-label">Total Mantenimientos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_tipo']['motor'] ?? 0 }}</span>
                        <span class="stat-label">Motor</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_tipo']['transmision'] ?? 0 }}</span>
                        <span class="stat-label">Transmisión</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_tipo']['hidraulico'] ?? 0 }}</span>
                        <span class="stat-label">Hidráulico</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_tipo']['otros'] ?? 0 }}</span>
                        <span class="stat-label">Otros</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($estadisticas['costo_total'] ?? 0, 2) }}</span>
                        <span class="stat-label">Costo Total</span>
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
                <th style="width: 6%;">#</th>
                <th style="width: 12%;">Fecha</th>
                <th style="width: 12%;">Tipo</th>
                <th style="width: 25%;">Descripción</th>
                <th style="width: 10%;">Kilometraje</th>
                <th style="width: 12%;">Costo</th>
                <th style="width: 15%;">Proveedor/Taller</th>
                <th style="width: 8%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mantenimientos as $index => $mantenimiento)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ $mantenimiento->fecha_mantenimiento ? \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="text-center">
                        @php
                            $tipoClass = match($mantenimiento->tipo_mantenimiento) {
                                'motor' => 'status-danger',
                                'transmision' => 'status-warning',
                                'hidraulico' => 'status-info',
                                'preventivo' => 'status-normal',
                                'correctivo' => 'status-alta',
                                default => 'status-normal'
                            };
                        @endphp
                        <span class="status-badge {{ $tipoClass }}">
                            {{ ucfirst($mantenimiento->tipo_mantenimiento ?? 'N/A') }}
                        </span>
                    </td>
                    <td>
                        <div class="text-bold">{{ $mantenimiento->descripcion ?? 'Sin descripción' }}</div>
                        @if($mantenimiento->observaciones)
                            <div class="font-small text-muted">{{ Str::limit($mantenimiento->observaciones, 100) }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($mantenimiento->kilometraje)
                            <span class="text-bold">{{ number_format($mantenimiento->kilometraje) }} km</span>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($mantenimiento->costo)
                            <span class="text-bold">${{ number_format($mantenimiento->costo, 2) }}</span>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($mantenimiento->proveedor)
                            <div class="text-bold">{{ $mantenimiento->proveedor }}</div>
                        @elseif($mantenimiento->taller)
                            <div class="text-bold">{{ $mantenimiento->taller }}</div>
                        @else
                            <span class="text-muted font-small">No especificado</span>
                        @endif
                        @if($mantenimiento->numero_factura)
                            <div class="font-small text-muted">Fact: {{ $mantenimiento->numero_factura }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @php
                            $estadoClass = match($mantenimiento->estado) {
                                'completado' => 'status-finalizado',
                                'en_proceso' => 'status-pendiente',
                                'programado' => 'status-normal',
                                'cancelado' => 'status-baja',
                                default => 'status-normal'
                            };
                        @endphp
                        <span class="status-badge {{ $estadoClass }}">
                            {{ ucfirst(str_replace('_', ' ', $mantenimiento->estado ?? 'N/A')) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted p-15">
                        No se encontraron mantenimientos para este vehículo
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Análisis de Costos por Tipo -->
    @if(count($mantenimientos) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Análisis de Costos por Tipo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    @php
                        $mantenimientosConCosto = $mantenimientos->whereNotNull('costo');
                        $costoTotal = $mantenimientosConCosto->sum('costo');
                        $costoPromedio = $mantenimientosConCosto->count() > 0 ? $costoTotal / $mantenimientosConCosto->count() : 0;
                        $costoMayor = $mantenimientosConCosto->max('costo');
                        $costoMenor = $mantenimientosConCosto->min('costo');
                        
                        $costoPorTipo = $mantenimientosConCosto->groupBy('tipo_mantenimiento')->map(function($grupo) {
                            return $grupo->sum('costo');
                        });
                    @endphp
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($costoTotal, 2) }}</span>
                        <span class="stat-label">Costo Total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($costoPromedio, 2) }}</span>
                        <span class="stat-label">Costo Promedio</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($costoMayor, 2) }}</span>
                        <span class="stat-label">Costo Mayor</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($costoMenor, 2) }}</span>
                        <span class="stat-label">Costo Menor</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $mantenimientosConCosto->count() }}</span>
                        <span class="stat-label">Con Costo Registrado</span>
                    </div>
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
