@extends('layouts.app')

@section('title', 'Obras por Operador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2">📊 Obras por Operador</h1>
                    <p class="text-muted mb-0">Historial de asignaciones de obras a operadores</p>
                </div>
                <div>
                    <a href="{{ route('operadores.filtrar-por-obra') }}" class="btn btn-outline-primary">
                        <i class="fas fa-filter"></i> Filtrar por Obra
                    </a>
                </div>
            </div>

            <!-- Estadísticas generales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Operadores</h5>
                                    <h2 class="mb-0">{{ $operadoresConObras->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Asignaciones</h5>
                                    <h2 class="mb-0">{{ $operadoresConObras->sum('total_asignaciones_obra') }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tasks fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Promedio por Operador</h5>
                                    <h2 class="mb-0">{{ $operadoresConObras->count() > 0 ? round($operadoresConObras->sum('total_asignaciones_obra') / $operadoresConObras->count(), 1) : 0 }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Operadores Activos</h5>
                                    <h2 class="mb-0">{{ $operadoresConObras->where('estatus', 'activo')->count() }}</h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de operadores -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">👨‍💼 Operadores con Historial de Obras</h5>
                </div>
                <div class="card-body">
                    @if($operadoresConObras->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Operador</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Total Asignaciones</th>
                                        <th>Vehículo Actual</th>
                                        <th>Última Actividad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operadoresConObras as $operador)
                                        @php
                                            $vehiculoActual = $operador->vehiculoActual();
                                            $ultimaAsignacion = $operador->historialOperadorVehiculo()
                                                ->whereNotNull('obra_id')
                                                ->latest('fecha_asignacion')
                                                ->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ substr($operador->nombre_completo, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $operador->nombre_completo }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $operador->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $operador->categoria->nombre_categoria ?? 'Sin categoría' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($operador->estatus === 'activo')
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($operador->estatus) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $operador->total_asignaciones_obra }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($vehiculoActual)
                                                    <small>
                                                        <strong>{{ $vehiculoActual->marca }} {{ $vehiculoActual->modelo }}</strong><br>
                                                        <span class="text-muted">{{ $vehiculoActual->placas }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">Sin vehículo asignado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ultimaAsignacion)
                                                    <small>
                                                        {{ $ultimaAsignacion->fecha_asignacion->format('d/m/Y') }}<br>
                                                        <span class="text-muted">{{ $ultimaAsignacion->fecha_asignacion->diffForHumans() }}</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">Sin actividad</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operadores.obras-operador.show', $operador) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver detalle de obras">
                                                    <i class="fas fa-eye"></i> Ver Obras
                                                </a>
                                                <a href="{{ route('personal.show', $operador) }}" 
                                                   class="btn btn-sm btn-outline-secondary" title="Ver perfil completo">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay operadores con historial de obras</h5>
                            <p class="text-muted">Los operadores aparecerán aquí cuando se asignen a obras con vehículos.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background-color: #6c757d;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endsection
