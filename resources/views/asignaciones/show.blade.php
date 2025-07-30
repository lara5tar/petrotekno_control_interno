@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Detalles de Asignación') }} #{{ $asignacion->id ?? 'N/A' }}</h4>
                    <div>
                        @if(isset($asignacion) && $asignacion->esta_activa)
                            <a href="{{ route('asignaciones.edit', $asignacion->id) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn btn-success me-2" onclick="liberarAsignacion({{ $asignacion->id }})">
                                <i class="fas fa-unlock"></i> Liberar
                            </button>
                        @endif
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6">
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Estado:</strong></div>
                                        <div class="col-sm-8">
                                            @if(isset($asignacion) && $asignacion->esta_activa)
                                                <span class="badge bg-success fs-6">Activa</span>
                                            @else
                                                <span class="badge bg-secondary fs-6">Liberada</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Fecha Asignación:</strong></div>
                                        <div class="col-sm-8">{{ isset($asignacion) && $asignacion->fecha_asignacion ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y H:i') : 'N/A' }}</div>
                                    </div>
                                    @if(isset($asignacion) && !$asignacion->esta_activa)
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Fecha Liberación:</strong></div>
                                            <div class="col-sm-8">{{ $asignacion->fecha_liberacion ? \Carbon\Carbon::parse($asignacion->fecha_liberacion)->format('d/m/Y H:i') : 'N/A' }}</div>
                                        </div>
                                    @endif
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Registrado por:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->encargado->personal->nombre_completo ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Vehículo -->
                        <div class="col-md-6">
                            <div class="card border-info mb-3">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-truck"></i> Vehículo Asignado</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Vehículo:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->vehiculo->nombre_completo ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Placas:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->vehiculo->placas ?? 'Sin placas' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Año:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->vehiculo->anio ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Serie:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->vehiculo->n_serie ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Operador -->
                        <div class="col-md-6">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-user"></i> Operador</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Nombre:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->personal->nombre_completo ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Categoría:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->personal->categoria->nombre_categoria ?? 'Sin categoría' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Estatus:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $asignacion->personal->estatus == 'activo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($asignacion->personal->estatus ?? 'N/A') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la Obra -->
                        <div class="col-md-6">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-building"></i> Obra</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Obra:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->obra->nombre_obra ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Estatus:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $asignacion->obra->estatus == 'activa' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($asignacion->obra->estatus ?? 'N/A') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Fecha Inicio:</strong></div>
                                        <div class="col-sm-8">{{ $asignacion->obra->fecha_inicio ? \Carbon\Carbon::parse($asignacion->obra->fecha_inicio)->format('d/m/Y') : 'N/A' }}</div>
                                    </div>
                                    @if($asignacion->obra->fecha_fin)
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Fecha Fin:</strong></div>
                                            <div class="col-sm-8">{{ \Carbon\Carbon::parse($asignacion->obra->fecha_fin)->format('d/m/Y') }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Información de Kilometraje -->
                        <div class="col-md-12">
                            <div class="card border-dark mb-3">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Control de Kilometraje</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Kilometraje Inicial</h6>
                                                <h3 class="text-primary">{{ number_format($asignacion->kilometraje_inicial ?? 0) }}</h3>
                                                <small class="text-muted">km</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Kilometraje Final</h6>
                                                <h3 class="text-{{ $asignacion->kilometraje_final ? 'success' : 'muted' }}">
                                                    {{ $asignacion->kilometraje_final ? number_format($asignacion->kilometraje_final) : '-' }}
                                                </h3>
                                                <small class="text-muted">km</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Kilometraje Recorrido</h6>
                                                <h3 class="text-{{ $asignacion->kilometraje_recorrido ? 'info' : 'muted' }}">
                                                    {{ $asignacion->kilometraje_recorrido ? number_format($asignacion->kilometraje_recorrido) : '-' }}
                                                </h3>
                                                <small class="text-muted">km</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6 class="text-muted">Duración</h6>
                                                <h3 class="text-dark">
                                                    @if(isset($asignacion))
                                                        @php
                                                            $fechaInicio = \Carbon\Carbon::parse($asignacion->fecha_asignacion);
                                                            $fechaFin = $asignacion->fecha_liberacion ? \Carbon\Carbon::parse($asignacion->fecha_liberacion) : \Carbon\Carbon::now();
                                                            $duracion = $fechaInicio->diffInDays($fechaFin);
                                                        @endphp
                                                        {{ $duracion }}
                                                    @else
                                                        -
                                                    @endif
                                                </h3>
                                                <small class="text-muted">días</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        @if(isset($asignacion) && $asignacion->observaciones)
                            <div class="col-md-12">
                                <div class="card border-secondary mb-3">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0"><i class="fas fa-comments"></i> Observaciones</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $asignacion->observaciones }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para liberar asignación -->
@if(isset($asignacion) && $asignacion->esta_activa)
<div class="modal fade" id="liberarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Liberar Asignación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="liberarForm" method="POST" action="{{ route('asignaciones.liberar', $asignacion->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kilometraje_final" class="form-label">Kilometraje Final *</label>
                        <input type="number" class="form-control" id="kilometraje_final" name="kilometraje_final" 
                               min="{{ $asignacion->kilometraje_inicial }}" required>
                        <small class="form-text text-muted">
                            Debe ser mayor a {{ number_format($asignacion->kilometraje_inicial) }} km (kilometraje inicial)
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_liberacion" class="form-label">Observaciones de Liberación</label>
                        <textarea class="form-control" id="observaciones_liberacion" name="observaciones_liberacion" 
                                  rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Liberar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function liberarAsignacion(id) {
        // Limpiar formulario
        document.getElementById('kilometraje_final').value = '';
        document.getElementById('observaciones_liberacion').value = '';
        
        // Mostrar modal
        new bootstrap.Modal(document.getElementById('liberarModal')).show();
    }
</script>
@endpush
