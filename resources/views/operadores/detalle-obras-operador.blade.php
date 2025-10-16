@extends('layouts.app')

@section('title', 'Obras de ' . $operador->nombre_completo)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('operadores.obras-por-operador') }}">Obras por Operador</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $operador->nombre_completo }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-2">🏗️ Obras de {{ $operador->nombre_completo }}</h1>
                    <p class="text-muted mb-0">Historial completo de asignaciones a obras</p>
                </div>
                <div>
                    <a href="{{ route('operadores.obras-por-operador') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Información del operador -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>👨‍💼 Información del Operador</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{{ $operador->nombre_completo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Puesto:</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $operador->categoria->nombre_categoria ?? 'Sin puesto' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($operador->estatus === 'activo')
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($operador->estatus) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Vehículo Actual:</strong></td>
                                    <td>
                                        @if($estadisticas['vehiculo_actual'])
                                            <strong>{{ $estadisticas['vehiculo_actual']->marca }} {{ $estadisticas['vehiculo_actual']->modelo }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $estadisticas['vehiculo_actual']->placas }}</small>
                                        @else
                                            <span class="text-muted">Sin vehículo asignado</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>📊 Estadísticas Generales</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-1">{{ $estadisticas['total_obras'] }}</h3>
                                            <small>Obras Trabajadas</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-1">{{ $estadisticas['total_asignaciones'] }}</h3>
                                            <small>Total Asignaciones</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($estadisticas['obra_actual'])
                                <div class="mt-3">
                                    <div class="alert alert-info">
                                        <strong>🏗️ Obra Actual:</strong><br>
                                        {{ $estadisticas['obra_actual']->nombre }}<br>
                                        <small>{{ $estadisticas['obra_actual']->ubicacion }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Obras por operador -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">🏗️ Obras Trabajadas ({{ $historialObras->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($historialObras->count() > 0)
                        <div class="row">
                            @foreach($historialObras as $obra)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-left-primary">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-building text-primary"></i>
                                                {{ $obra['obra']->nombre }}
                                            </h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    {{ $obra['obra']->ubicacion }}
                                                </small>
                                            </p>
                                            
                                            <div class="mt-3">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <div class="border-end">
                                                            <h6 class="text-primary mb-0">{{ $obra['total_asignaciones'] }}</h6>
                                                            <small class="text-muted">Asignaciones</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <h6 class="text-success mb-0">{{ $obra['vehiculos_asignados']->count() }}</h6>
                                                        <small class="text-muted">Vehículos Usados</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i>
                                                    Primera asignación: {{ $obra['primera_asignacion']->format('d/m/Y') }}
                                                </small>
                                            </div>

                                            @if($obra['vehiculos_asignados']->count() > 0)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <strong>Vehículos:</strong><br>
                                                        @foreach($obra['vehiculos_asignados'] as $vehiculo)
                                                            <span class="badge bg-light text-dark me-1">
                                                                {{ $vehiculo->placas }}
                                                            </span>
                                                        @endforeach
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="verEstadisticasObra({{ $operador->id }}, {{ $obra['obra']->id }})">
                                                <i class="fas fa-chart-bar"></i> Ver Estadísticas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron obras</h5>
                            <p class="text-muted">Este operador aún no ha sido asignado a ninguna obra.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Historial detallado -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📋 Historial Detallado de Asignaciones</h5>
                </div>
                <div class="card-body">
                    @if($historialDetallado->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Obra</th>
                                        <th>Vehículo</th>
                                        <th>Tipo Movimiento</th>
                                        <th>Asignado por</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historialDetallado as $registro)
                                        <tr>
                                            <td>
                                                <strong>{{ $registro->fecha_asignacion->format('d/m/Y') }}</strong><br>
                                                <small class="text-muted">{{ $registro->fecha_asignacion->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $registro->obra->nombre_obra }}</strong><br>
                                                <small class="text-muted">{{ $registro->obra->ubicacion }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $registro->vehiculo->marca }} {{ $registro->vehiculo->modelo }}</strong><br>
                                                <small class="text-muted">{{ $registro->vehiculo->placas }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match($registro->tipo_movimiento) {
                                                        'asignacion_inicial' => 'bg-success',
                                                        'cambio_operador' => 'bg-warning',
                                                        'remocion_operador' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $registro->descripcion_movimiento }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $registro->usuarioAsigno->name ?? 'Sistema' }}<br>
                                                <small class="text-muted">{{ $registro->fecha_asignacion->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($registro->observaciones)
                                                    <small>{{ $registro->observaciones }}</small>
                                                @else
                                                    <span class="text-muted">Sin observaciones</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-3">
                            {{ $historialDetallado->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay historial detallado</h5>
                            <p class="text-muted">No se encontraron registros de asignaciones para este operador.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para estadísticas de obra -->
<div class="modal fade" id="estadisticasObraModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📊 Estadísticas en la Obra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="estadisticasObraContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
</style>

<script>
function verEstadisticasObra(operadorId, obraId) {
    const modal = new bootstrap.Modal(document.getElementById('estadisticasObraModal'));
    const content = document.getElementById('estadisticasObraContent');
    
    // Mostrar loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando estadísticas...</p>
        </div>
    `;
    
    modal.show();
    
    // Hacer petición AJAX
    fetch(`/api/operadores/${operadorId}/obras/${obraId}/estadisticas`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>${data.estadisticas.total_asignaciones}</h3>
                                    <small>Total Asignaciones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>${data.estadisticas.vehiculos_utilizados}</h3>
                                    <small>Vehículos Utilizados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>📅 Período de Trabajo</h6>
                        <p><strong>Primera asignación:</strong> ${data.estadisticas.primera_asignacion}</p>
                        <p><strong>Última asignación:</strong> ${data.estadisticas.ultima_asignacion}</p>
                        <p><strong>Días trabajados:</strong> ${data.estadisticas.dias_trabajados}</p>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error al cargar estadísticas</div>';
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
        });
}
</script>
@endsection
