@extends('pdf.layouts.base')

@section('title', 'Historial de Obras por Operador')
@section('report-title', 'Historial de Obras por Operador')
@section('report-subtitle', 'Reporte individual del historial completo de asignaciones por operador específico')

@section('content')
    <!-- Información del Operador -->
    @if(isset($operador))
        <div class="pdf-info-section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Operador:</div>
                    <div class="info-value text-bold">{{ $operador->nombre_completo ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">No. Licencia:</div>
                    <div class="info-value">{{ $operador->numero_licencia ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">NSS:</div>
                    <div class="info-value">{{ $operador->nss ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        @php
                            $statusClass = $operador->estatus === 'activo' ? 'status-activo' : 'status-baja';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($operador->estatus ?? 'N/A') }}
                        </span>
                    </div>
                </div>
                @if($operador->telefono)
                    <div class="info-row">
                        <div class="info-label">Teléfono:</div>
                        <div class="info-value">{{ $operador->telefono }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen de Actividad</h3>
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
                        <span class="stat-number">{{ $estadisticas['vehiculos_distintos'] ?? 0 }}</span>
                        <span class="stat-label">Activos Operados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total_kilometros'] ?? 0 }}</span>
                        <span class="stat-label">Total Kilómetros</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['promedio_dias'] ?? 0 }}</span>
                        <span class="stat-label">Promedio Días/Obra</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros -->
    @if(isset($filtros) && count($filtros) > 0)
        <div class="pdf-info-section">
            <div class="info-grid">
                @if(isset($filtros['operador_id']) && $filtros['operador_id'])
                    <div class="info-row">
                        <div class="info-label">Operador ID:</div>
                        <div class="info-value">{{ $filtros['operador_id'] }}</div>
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
                <th style="width: 5%;">#</th>
                <th style="width: 18%;">Obra</th>
                <th style="width: 16%;">Activo</th>
                <th style="width: 10%;">Ubicación</th>
                <th style="width: 9%;">Fecha Asignación</th>
                <th style="width: 9%;">Fecha Finalización</th>
                <th style="width: 8%;">Estado</th>
                <th style="width: 8%;">Km Inicial</th>
                <th style="width: 8%;">Km Final</th>
                <th style="width: 9%;">Días Trabajados</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asignaciones as $index => $asignacion)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">
                        <div>{{ $asignacion->obra->nombre ?? 'N/A' }}</div>
                        @if($asignacion->obra && $asignacion->obra->cliente)
                            <div class="font-small text-muted">{{ $asignacion->obra->cliente }}</div>
                        @endif
                        @if($asignacion->obra && $asignacion->obra->ubicacion)
                            <div class="font-small text-muted">{{ Str::limit($asignacion->obra->ubicacion, 30) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($asignacion->vehiculo)
                            <div class="text-bold">{{ $asignacion->vehiculo->marca }} {{ $asignacion->vehiculo->modelo }}</div>
                            <div class="font-small text-muted">{{ $asignacion->vehiculo->anio }} - {{ $asignacion->vehiculo->placas ?: 'Sin placas' }}</div>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $asignacion->vehiculo->ubicacion ?? 'Sin ubicación' }}
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
                            {{ number_format($asignacion->kilometraje_inicial) }}
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($asignacion->kilometraje_final)
                            <span class="text-bold">{{ number_format($asignacion->kilometraje_final) }}</span>
                            @if($asignacion->kilometraje_inicial)
                                @php
                                    $kmRecorridos = $asignacion->kilometraje_final - $asignacion->kilometraje_inicial;
                                @endphp
                                <div class="font-small text-info">+{{ number_format($kmRecorridos) }}</div>
                            @endif
                        @elseif($asignacion->estado === 'activo')
                            <span class="text-muted font-small">En curso</span>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($asignacion->fecha_asignacion)
                            @php
                                $fechaFin = $asignacion->fecha_finalizacion ? \Carbon\Carbon::parse($asignacion->fecha_finalizacion) : now();
                                $fechaInicio = \Carbon\Carbon::parse($asignacion->fecha_asignacion);
                                $diasTrabajados = $fechaInicio->diffInDays($fechaFin);
                            @endphp
                            <span class="text-bold">{{ $diasTrabajados }}</span>
                            <div class="font-small text-muted">días</div>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted p-15">
                        No se encontraron asignaciones para este operador
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Análisis de Productividad -->
    @if(count($asignaciones) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Análisis de Productividad</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    @php
                        $asignacionesConKm = $asignaciones->whereNotNull('kilometraje_inicial')->whereNotNull('kilometraje_final');
                        $totalKmRecorridos = $asignacionesConKm->sum(function($a) {
                            return $a->kilometraje_final - $a->kilometraje_inicial;
                        });
                        $promedioKmPorObra = $asignacionesConKm->count() > 0 ? $totalKmRecorridos / $asignacionesConKm->count() : 0;
                        
                        $asignacionesConFechas = $asignaciones->whereNotNull('fecha_asignacion');
                        $totalDiasTrabajados = $asignacionesConFechas->sum(function($a) {
                            $fechaFin = $a->fecha_finalizacion ? \Carbon\Carbon::parse($a->fecha_finalizacion) : now();
                            $fechaInicio = \Carbon\Carbon::parse($a->fecha_asignacion);
                            return $fechaInicio->diffInDays($fechaFin);
                        });
                        $promedioDiasPorObra = $asignacionesConFechas->count() > 0 ? $totalDiasTrabajados / $asignacionesConFechas->count() : 0;
                        
                        $vehiculosUnicos = $asignaciones->pluck('vehiculo_id')->unique()->count();
                        $obrasUnicas = $asignaciones->pluck('obra_id')->unique()->count();
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
                        <span class="stat-number">{{ $totalDiasTrabajados }}</span>
                        <span class="stat-label">Total Días Trabajados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ number_format($promedioDiasPorObra, 1) }}</span>
                        <span class="stat-label">Promedio Días/Obra</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $vehiculosUnicos }}</span>
                        <span class="stat-label">Activos Diferentes</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $obrasUnicas }}</span>
                        <span class="stat-label">Obras Diferentes</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Resumen de Activos Operados -->
    @if(count($asignaciones) > 0)
        @php
            $vehiculosOperados = $asignaciones->groupBy('vehiculo_id')->map(function($asignacionesVehiculo) {
                $vehiculo = $asignacionesVehiculo->first()->vehiculo;
                return [
                    'vehiculo' => $vehiculo,
                    'total_asignaciones' => $asignacionesVehiculo->count(),
                    'km_totales' => $asignacionesVehiculo->whereNotNull('kilometraje_inicial')->whereNotNull('kilometraje_final')->sum(function($a) {
                        return $a->kilometraje_final - $a->kilometraje_inicial;
                    })
                ];
            })->sortByDesc('total_asignaciones');
        @endphp
        
        @if($vehiculosOperados->count() > 0)
            <div class="pdf-stats-section mt-20">
                <h3 class="stats-title">Activos Más Operados</h3>
                <table class="pdf-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Activo</th>
                            <th style="width: 20%;">Asignaciones</th>
                            <th style="width: 20%;">Km Recorridos</th>
                            <th style="width: 20%;">Promedio Km/Asignación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculosOperados->take(5) as $data)
                            <tr>
                                <td>
                                    @if($data['vehiculo'])
                                        <div class="text-bold">{{ $data['vehiculo']->marca }} {{ $data['vehiculo']->modelo }}</div>
                                        <div class="font-small text-muted">{{ $data['vehiculo']->anio }} - {{ $data['vehiculo']->placas ?: 'Sin placas' }}</div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center text-bold">{{ $data['total_asignaciones'] }}</td>
                                <td class="text-center">{{ number_format($data['km_totales']) }} km</td>
                                <td class="text-center">
                                    {{ $data['total_asignaciones'] > 0 ? number_format($data['km_totales'] / $data['total_asignaciones'], 0) : '0' }} km
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
@endsection

@section('footer-info')
    Historial de operador {{ isset($operador) ? $operador->nombre_completo : 'N/A' }} - {{ count($asignaciones) }} asignaciones registradas
@endsection
