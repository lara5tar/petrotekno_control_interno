@extends('pdf.layouts.base')

@section('title', 'Reporte de Vehículos Filtrados')
@section('report-title', 'Reporte de Vehículos Filtrados')
@section('report-subtitle', 'Listado de vehículos según criterios de filtrado aplicados')

@section('content')
    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total'] ?? 0 }}</span>
                        <span class="stat-label">Total Vehículos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['disponible'] ?? 0 }}</span>
                        <span class="stat-label">Disponibles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['asignado'] ?? 0 }}</span>
                        <span class="stat-label">Asignados</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['mantenimiento'] ?? 0 }}</span>
                        <span class="stat-label">Mantenimiento</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['fuera_servicio'] ?? 0 }}</span>
                        <span class="stat-label">Fuera Servicio</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estado']['baja'] ?? 0 }}</span>
                        <span class="stat-label">Dados de Baja</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Información de Filtros Aplicados -->
    @if(isset($filtros) && count(array_filter($filtros)) > 0)
        <div class="pdf-info-section">
            <h3 class="info-title">Filtros Aplicados</h3>
            <div class="info-grid">
                @if(isset($filtros['buscar']) && $filtros['buscar'])
                    <div class="info-row">
                        <div class="info-label">Búsqueda:</div>
                        <div class="info-value">{{ $filtros['buscar'] }}</div>
                    </div>
                @endif
                @if(isset($filtros['estado']) && $filtros['estado'])
                    <div class="info-row">
                        <div class="info-label">Estado Filtrado:</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $filtros['estado'])) }}</div>
                    </div>
                @endif
                @if(isset($filtros['anio']) && $filtros['anio'])
                    <div class="info-row">
                        <div class="info-label">Año Filtrado:</div>
                        <div class="info-value">{{ $filtros['anio'] }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Total de Registros:</div>
                    <div class="info-value">{{ count($vehiculos) }} vehículos</div>
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

    <!-- Tabla Principal de Vehículos -->
    <table class="pdf-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 12%;">Marca/Modelo</th>
                <th style="width: 10%;">Tipo</th>
                <th style="width: 6%;">Año</th>
                <th style="width: 9%;">Placas</th>
                <th style="width: 12%;">No. Serie</th>
                <th style="width: 13%;">Ubicación</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 13%;">Km Actual</th>
                <th style="width: 10%;">Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehiculos as $index => $vehiculo)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">
                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                    </td>
                    <td class="text-center">{{ $vehiculo->tipo_activo_nombre ?? 'N/A' }}</td>
                    <td class="text-center">{{ ($vehiculo->anio !== null && $vehiculo->anio !== '' && $vehiculo->anio > 0) ? $vehiculo->anio : 'N/A' }}</td>
                    <td class="text-center no-wrap">{{ $vehiculo->placas ?: 'N/A' }}</td>
                    <td class="font-small break-word">{{ $vehiculo->n_serie ?: 'N/A' }}</td>
                    <td class="text-center">
                        @if($vehiculo->estado || $vehiculo->municipio)
                            {{ $vehiculo->estado }}{{ $vehiculo->estado && $vehiculo->municipio ? ', ' : '' }}{{ $vehiculo->municipio }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-center">
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
                    </td>
                    <td class="text-center">
                        @if($vehiculo->kilometraje_actual)
                            <span class="text-bold">{{ number_format($vehiculo->kilometraje_actual) }} km</span>
                        @else
                            <span class="text-muted font-small">N/A</span>
                        @endif
                    </td>
                    <td class="text-center font-small">
                        {{ $vehiculo->created_at ? $vehiculo->created_at->format('d/m/Y') : 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted p-15">
                        No se encontraron vehículos con los criterios especificados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Resumen de Kilometrajes -->
    @if(count($vehiculos) > 0)
        <div class="pdf-stats-section mt-20">
            <h3 class="stats-title">Resumen de Kilometrajes</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    @php
                        $vehiculosConKm = $vehiculos->whereNotNull('kilometraje_actual');
                        $promedioKm = $vehiculosConKm->avg('kilometraje_actual');
                        $maxKm = $vehiculosConKm->max('kilometraje_actual');
                        $minKm = $vehiculosConKm->min('kilometraje_actual');
                    @endphp
                    <div class="stat-item">
                        <span class="stat-number">{{ $vehiculosConKm->count() }}</span>
                        <span class="stat-label">Con Kilometraje</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $promedioKm ? number_format($promedioKm, 0) : '0' }}</span>
                        <span class="stat-label">Promedio Km</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $maxKm ? number_format($maxKm, 0) : '0' }}</span>
                        <span class="stat-label">Mayor Km</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $minKm ? number_format($minKm, 0) : '0' }}</span>
                        <span class="stat-label">Menor Km</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notas adicionales -->
    <div class="pdf-notes-section mt-20">
        <h3 class="notes-title">Notas Importantes</h3>
        <ul class="notes-list">
            <li>Este reporte incluye únicamente los vehículos que cumplen con los criterios de filtrado aplicados.</li>
            <li>Los datos de kilometraje corresponden al último registro disponible en el sistema.</li>
            <li>Los campos sin información se muestran como "N/A" (No Aplica).</li>
            <li>El estado de cada vehículo refleja su situación actual en el sistema de control interno.</li>
            @if(isset($filtros['buscar']) && $filtros['buscar'])
                <li>La búsqueda aplicada incluye coincidencias en marca, modelo, placas y número de serie.</li>
            @endif
        </ul>
    </div>
@endsection