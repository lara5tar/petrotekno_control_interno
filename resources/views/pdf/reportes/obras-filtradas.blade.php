@extends('pdf.layouts.base')

@section('title', 'Reporte de Obras Filtradas')
@section('report-title', 'Reporte de Obras Filtradas')
@section('report-subtitle', 'Listado de obras según criterios de filtrado aplicados')

@section('content')
    <!-- Sección de Estadísticas -->
    @if(isset($estadisticas) && count($estadisticas) > 0)
        <div class="pdf-stats-section">
            <h3 class="stats-title">Resumen Ejecutivo</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['total'] ?? 0 }}</span>
                        <span class="stat-label">Total Obras</span>
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
                        <span class="stat-number">{{ $estadisticas['por_estatus']['completada'] ?? 0 }}</span>
                        <span class="stat-label">Completadas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estatus']['en_progreso'] ?? 0 }}</span>
                        <span class="stat-label">En Progreso</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas por Estatus -->
        <div class="pdf-stats-section">
            <h3 class="stats-title">Distribución por Estatus</h3>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estatus']['activa'] ?? 0 }}</span>
                        <span class="stat-label">Activas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estatus']['en_progreso'] ?? 0 }}</span>
                        <span class="stat-label">En Progreso</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estatus']['completada'] ?? 0 }}</span>
                        <span class="stat-label">Completadas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $estadisticas['por_estatus']['suspendida'] ?? 0 }}</span>
                        <span class="stat-label">Suspendidas</span>
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
                @if(isset($filtrosAplicados['estatus']) && $filtrosAplicados['estatus'])
                    <div class="info-row">
                        <div class="info-label">Estatus:</div>
                        <div class="info-value">{{ ucfirst($filtrosAplicados['estatus']) }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['fecha_inicio']) && $filtrosAplicados['fecha_inicio'])
                    <div class="info-row">
                        <div class="info-label">Fecha de Inicio:</div>
                        <div class="info-value">{{ $filtrosAplicados['fecha_inicio'] }}</div>
                    </div>
                @endif
                @if(isset($filtrosAplicados['solo_activas']) && $filtrosAplicados['solo_activas'])
                    <div class="info-row">
                        <div class="info-label">Solo Activas:</div>
                        <div class="info-value">{{ $filtrosAplicados['solo_activas'] === 'true' ? 'Sí' : 'No' }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Información adicional -->
    <div class="pdf-info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Total de Registros:</div>
                <div class="info-value">{{ isset($obras) ? $obras->count() : 0 }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Generación:</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generado por:</div>
                <div class="info-value">{{ Auth::user()->nombre ?? 'Sistema' }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Obras -->
    @if(isset($obras) && $obras->count() > 0)
        <div class="pdf-table-section">
            <h3 class="table-title">Listado de Obras</h3>
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th style="width: 5%">id</th>
                        <th style="width: 25%">Nombre de la Obra</th>
                        <th style="width: 10%">Fecha Inicio</th>
                        <th style="width: 10%">Fecha Fin</th>
                        <th style="width: 12%">Estatus</th>
                        <th style="width: 15%">Encargado</th>
                        <th style="width: 18%">Ubicación</th>
                        <th style="width: 5%">Avance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($obras as $obra)
                        <tr>
                            <td class="pdf-cell-center">{{ $obra->id }}</td>
                            <td class="pdf-cell">{{ $obra->nombre_obra ?? 'N/A' }}</td>
                            <td class="pdf-cell-center">
                                {{ $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="pdf-cell-center">
                                {{ $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : 'En progreso' }}
                            </td>
                            <td class="pdf-cell-center">
                                @switch($obra->estatus)
                                    @case('activa')
                                        <span class="status-badge status-activa">Activa</span>
                                        @break
                                    @case('en_progreso')
                                        <span class="status-badge status-en-progreso">En Progreso</span>
                                        @break
                                    @case('completada')
                                        <span class="status-badge status-completada">Completada</span>
                                        @break
                                    @case('suspendida')
                                        <span class="status-badge status-suspendida">Suspendida</span>
                                        @break
                                    @case('planificada')
                                        <span class="status-badge status-planificada">Planificada</span>
                                        @break
                                    @default
                                        {{ ucfirst($obra->estatus ?? 'N/A') }}
                                @endswitch
                            </td>
                            <td class="pdf-cell">
                                @if($obra->encargado)
                                    {{ $obra->encargado->nombre_completo ?? 
                                        trim(($obra->encargado->nombre ?? '') . ' ' . ($obra->encargado->apellidos ?? '')) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="pdf-cell">{{ $obra->ubicacion ?? 'N/A' }}</td>
                            <td class="pdf-cell-center">
                                {{ $obra->avance ? $obra->avance . '%' : 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="pdf-no-data">
            <p>No se encontraron obras que coincidan con los criterios de filtrado aplicados.</p>
        </div>
    @endif

    <!-- Información adicional en el pie -->
    <div class="pdf-footer-info">
        <p><strong>Nota:</strong> Este reporte muestra información general de obras sin detalles de vehículos o operadores. Para informes más extensos, considere aplicar filtros adicionales o utilice el formato Excel.</p>
        <p><strong>Sistema:</strong> Solupatch Control Interno - Módulo de Obras</p>
    </div>
@endsection

@section('additional-styles')
<style>
    .pdf-cell small {
        font-size: 0.8em;
        color: #666;
    }
</style>
@endsection