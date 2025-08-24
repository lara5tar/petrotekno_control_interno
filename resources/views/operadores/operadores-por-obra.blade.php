@extends('layouts.app')

@section('title', 'Operadores en ' . ($obra->nombre ?? 'Obra'))

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
                            <li class="breadcrumb-item active">Operadores en {{ $obra->nombre ?? 'Obra' }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-2">👷‍♂️ Operadores en {{ $obra->nombre ?? 'Obra' }}</h1>
                    <p class="text-muted mb-0">Historial de operadores que han trabajado en esta obra</p>
                </div>
                <div>
                    <a href="{{ route('operadores.obras-por-operador') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Información de la obra -->
            @if($obra)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>🏗️ Información de la Obra</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Nombre:</strong></td>
                                        <td>{{ $obra->nombre }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ubicación:</strong></td>
                                        <td>{{ $obra->ubicacion }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado:</strong></td>
                                        <td>
                                            @if($obra->estado === 'activa')
                                                <span class="badge bg-success">Activa</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($obra->estado) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($obra->observaciones)
                                        <tr>
                                            <td><strong>Observaciones:</strong></td>
                                            <td>{{ $obra->observaciones }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h5>📊 Estadísticas de la Obra</h5>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-1">{{ $operadoresEnObra->count() }}</h3>
                                                <small>Operadores Trabajados</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-1">{{ $operadoresEnObra->sum('asignaciones_en_obra') }}</h3>
                                                <small>Total Asignaciones</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Lista de operadores -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">👨‍💼 Operadores que han trabajado en esta obra</h5>
                </div>
                <div class="card-body">
                    @if($operadoresEnObra->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Operador</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Asignaciones en Obra</th>
                                        <th>Primera Asignación</th>
                                        <th>Última Asignación</th>
                                        <th>Vehículos Usados</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operadoresEnObra as $operador)
                                        @php
                                            $historialEnObra = $operador->historialOperadorVehiculo;
                                            $primeraAsignacion = $historialEnObra->first();
                                            $ultimaAsignacion = $historialEnObra->last();
                                            $vehiculosUsados = $historialEnObra->pluck('vehiculo')->unique('id');
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
                                                    {{ $operador->asignaciones_en_obra }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($primeraAsignacion)
                                                    <strong>{{ $primeraAsignacion->fecha_asignacion->format('d/m/Y') }}</strong><br>
                                                    <small class="text-muted">{{ $primeraAsignacion->fecha_asignacion->format('H:i') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ultimaAsignacion)
                                                    <strong>{{ $ultimaAsignacion->fecha_asignacion->format('d/m/Y') }}</strong><br>
                                                    <small class="text-muted">{{ $ultimaAsignacion->fecha_asignacion->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $vehiculosUsados->count() }}</span>
                                                @if($vehiculosUsados->count() > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        @foreach($vehiculosUsados->take(2) as $vehiculo)
                                                            {{ $vehiculo->placas }}@if(!$loop->last), @endif
                                                        @endforeach
                                                        @if($vehiculosUsados->count() > 2)
                                                            <br>+{{ $vehiculosUsados->count() - 2 }} más
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operadores.obras-operador.show', $operador) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver todas las obras del operador">
                                                    <i class="fas fa-eye"></i> Ver Obras
                                                </a>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="verDetalleEnObra({{ $operador->id }}, {{ $obra->id ?? 0 }})"
                                                        title="Ver detalle en esta obra">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay operadores en esta obra</h5>
                            <p class="text-muted">Aún no se han asignado operadores a esta obra.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Filtro para seleccionar otra obra -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">🔍 Cambiar Obra</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('operadores.filtrar-por-obra') }}">
                        <div class="row">
                            <div class="col-md-8">
                                <select name="obra_id" class="form-select" required>
                                    <option value="">Seleccionar otra obra...</option>
                                    @foreach(\App\Models\Obra::orderBy('nombre')->get() as $obraOption)
                                        <option value="{{ $obraOption->id }}" 
                                                @if(isset($obra) && $obra->id == $obraOption->id) selected @endif>
                                            {{ $obraOption->nombre }} - {{ $obraOption->ubicacion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('operadores.obras-por-operador') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalle del operador en obra -->
<div class="modal fade" id="detalleOperadorObraModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👨‍💼 Detalle del Operador en la Obra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleOperadorObraContent">
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

<script>
function verDetalleEnObra(operadorId, obraId) {
    const modal = new bootstrap.Modal(document.getElementById('detalleOperadorObraModal'));
    const content = document.getElementById('detalleOperadorObraContent');
    
    // Mostrar loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando detalle...</p>
        </div>
    `;
    
    modal.show();
    
    // Hacer petición AJAX
    fetch(`/api/operadores/${operadorId}/obras/${obraId}/estadisticas`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>${data.estadisticas.total_asignaciones}</h3>
                                    <small>Asignaciones en esta Obra</small>
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
                    <div>
                        <h6>📅 Período de Trabajo en esta Obra</h6>
                        <p><strong>Primera asignación:</strong> ${data.estadisticas.primera_asignacion}</p>
                        <p><strong>Última asignación:</strong> ${data.estadisticas.ultima_asignacion}</p>
                        <p><strong>Días trabajados:</strong> ${data.estadisticas.dias_trabajados}</p>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Error al cargar el detalle</div>';
            }
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
        });
}
</script>
@endsection
