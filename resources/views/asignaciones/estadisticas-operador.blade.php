@extends('layouts.app')

@section('title', 'Estadísticas del Operador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📊 Estadísticas de Productividad</h3>
                    <p class="card-text text-muted">
                        Análisis de desempeño para: <strong>{{ $estadisticas['operador']['nombre_completo'] }}</strong>
                    </p>
                </div>

                <div class="card-body">
                    <!-- Información del período -->
                    <div class="alert alert-info">
                        <strong>Período analizado:</strong> 
                        {{ \Carbon\Carbon::parse($estadisticas['periodo']['fecha_inicio'])->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($estadisticas['periodo']['fecha_fin'])->format('d/m/Y') }}
                    </div>

                    <!-- Resumen ejecutivo -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $estadisticas['resumen']['total_asignaciones'] }}</h4>
                                    <small>Total Asignaciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $estadisticas['resumen']['asignaciones_completadas'] }}</h4>
                                    <small>Completadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body text-center">
                                    <h4>{{ $estadisticas['resumen']['asignaciones_activas'] }}</h4>
                                    <small>Activas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $estadisticas['resumen']['tasa_completitud'] }}%</h4>
                                    <small>Tasa de Completitud</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas de rendimiento -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Kilometraje Total</h5>
                                    <h3 class="text-primary">{{ number_format($estadisticas['resumen']['kilometraje_total_recorrido']) }} km</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Promedio por Asignación</h5>
                                    <h3 class="text-success">{{ number_format($estadisticas['resumen']['promedio_kilometraje_asignacion']) }} km</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Duración Promedio</h5>
                                    <h3 class="text-warning">{{ $estadisticas['resumen']['duracion_promedio_dias'] }} días</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehículos más utilizados -->
                    @if(!empty($estadisticas['vehiculos_mas_utilizados']))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>🚗 Vehículos Más Utilizados</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Vehículo</th>
                                                <th>Asignaciones</th>
                                                <th>Kilometraje Total</th>
                                                <th>Promedio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estadisticas['vehiculos_mas_utilizados'] as $vehiculo)
                                                <tr>
                                                    <td>{{ $vehiculo['vehiculo'] }}</td>
                                                    <td>{{ $vehiculo['total_asignaciones'] }}</td>
                                                    <td>{{ number_format($vehiculo['kilometraje_total']) }} km</td>
                                                    <td>{{ number_format($vehiculo['kilometraje_total'] / max($vehiculo['total_asignaciones'], 1)) }} km</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Estadísticas por mes -->
                    @if(!empty($estadisticas['estadisticas_por_mes']))
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>📈 Rendimiento Mensual</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mes</th>
                                                <th>Total Asignaciones</th>
                                                <th>Completadas</th>
                                                <th>Tasa Completitud</th>
                                                <th>Kilometraje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estadisticas['estadisticas_por_mes'] as $mes => $datos)
                                                <tr>
                                                    <td>
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $mes)->format('M Y') }}
                                                    </td>
                                                    <td>{{ $datos['total'] }}</td>
                                                    <td>{{ $datos['completadas'] }}</td>
                                                    <td>
                                                        @if($datos['total'] > 0)
                                                            <span class="badge badge-{{ ($datos['completadas'] / $datos['total']) * 100 >= 80 ? 'success' : (($datos['completadas'] / $datos['total']) * 100 >= 60 ? 'warning' : 'danger') }}">
                                                                {{ round(($datos['completadas'] / $datos['total']) * 100, 1) }}%
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($datos['kilometraje']) }} km</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Personal
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <a href="{{ route('asignaciones.index', ['personal_id' => $estadisticas['operador']['id']]) }}" 
                                   class="btn btn-info">
                                    <i class="fas fa-list"></i> Ver Asignaciones
                                </a>
                                <button type="button" class="btn btn-primary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de fecha (opcional) -->
<div class="modal fade" id="filtroFechasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('asignaciones.estadisticas.operador', $estadisticas['operador']['id']) }}">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrar por Período</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" 
                               value="{{ request('fecha_inicio', \Carbon\Carbon::now()->subMonths(12)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" 
                               value="{{ request('fecha_fin', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .modal { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection
