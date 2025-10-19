@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">📈 Historial de Kilometrajes</h1>
                    <p class="text-muted mb-0">
                        Historial completo para {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('kilometrajes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                    @can('crear_kilometrajes')
                    <a href="{{ route('kilometrajes.create') }}?vehiculo_id={{ $vehiculo->id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <!-- Información del Vehículo -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-car"></i> Información del Vehículo
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="vehicle-avatar mb-3">🚗</div>
                                <h5 class="mb-1">{{ $vehiculo->placas }}</h5>
                                <p class="text-muted mb-0">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p>
                                <small class="text-muted">Año {{ $vehiculo->anio }}</small>
                            </div>
                            
                            <hr>
                            
                            <div class="info-row">
                                <span class="info-label">Estado:</span>
                                @php
                                    $statusColors = [
                                        'Disponible' => 'success',
                                        'Asignado' => 'primary', 
                                        'En Mantenimiento' => 'warning',
                                        'Fuera de Servicio' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$vehiculo->estatus->nombre_estatus] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ $vehiculo->estatus->nombre_estatus }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Kilometraje Actual:</span>
                                <span class="fw-bold">{{ number_format($vehiculo->kilometraje_actual) }} km</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Total de Registros:</span>
                                <span class="badge bg-info">{{ $kilometrajes->total() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    @if($kilometrajes->count() > 1)
                    <div class="card shadow mt-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-secondary">
                                <i class="fas fa-chart-line"></i> Estadísticas
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $primerRegistro = $kilometrajes->items()[count($kilometrajes->items()) - 1] ?? null;
                                $ultimoRegistro = $kilometrajes->items()[0] ?? null;
                                
                                if ($primerRegistro && $ultimoRegistro && $primerRegistro->id !== $ultimoRegistro->id) {
                                    $totalKm = $ultimoRegistro->kilometraje - $primerRegistro->kilometraje;
                                    $diasTranscurridos = $primerRegistro->fecha_captura->diffInDays($ultimoRegistro->fecha_captura);
                                    $promedioKmDia = $diasTranscurridos > 0 ? round($totalKm / $diasTranscurridos, 1) : 0;
                                } else {
                                    $totalKm = 0;
                                    $promedioKmDia = 0;
                                }
                            @endphp
                            
                            <div class="stat-item">
                                <div class="stat-value">{{ number_format($totalKm) }} km</div>
                                <div class="stat-label">Recorrido Total</div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-value">{{ $promedioKmDia }} km/día</div>
                                <div class="stat-label">Promedio Diario</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Historial de Registros -->
                <div class="col-md-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-history"></i> Historial de Registros
                                <span class="badge bg-info ms-2">{{ $kilometrajes->total() }} registros</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($kilometrajes->count() > 0)
                            <div class="timeline">
                                @foreach($kilometrajes as $km)
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <i class="fas fa-tachometer-alt"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <span class="badge bg-primary fs-6">
                                                            {{ number_format($km->kilometraje) }} km
                                                        </span>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar"></i> 
                                                        {{ $km->fecha_captura->format('d/m/Y') }}
                                                        ({{ $km->fecha_captura->diffForHumans() }})
                                                    </small>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    @can('ver_kilometrajes')
                                                    <a href="{{ route('kilometrajes.show', $km) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    
                                                    @can('editar_kilometrajes')
                                                    <a href="{{ route('kilometrajes.edit', $km) }}" 
                                                       class="btn btn-outline-warning btn-sm"
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="timeline-body">
                                            @if($km->obra)
                                            <div class="mb-2">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-building"></i> {{ $km->obra->nombre_obra }}
                                                </span>
                                            </div>
                                            @endif
                                            
                                            @if($km->observaciones)
                                            <p class="mb-2 text-muted">
                                                <i class="fas fa-sticky-note"></i> 
                                                {{ $km->observaciones }}
                                            </p>
                                            @endif
                                            
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> 
                                                Registrado por {{ $km->usuarioCaptura->name }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Paginación -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Mostrando {{ $kilometrajes->firstItem() }} a {{ $kilometrajes->lastItem() }} 
                                    de {{ $kilometrajes->total() }} registros
                                </div>
                                {{ $kilometrajes->links() }}
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Sin registros de kilometraje</h5>
                                <p class="text-muted">Este vehículo aún no tiene registros de kilometraje.</p>
                                @can('crear_kilometrajes')
                                <a href="{{ route('kilometrajes.create') }}?vehiculo_id={{ $vehiculo->id }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Registro
                                </a>
                                @endcan
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.vehicle-avatar {
    font-size: 3rem;
    width: 5rem;
    height: 5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    margin: 0 auto;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
}

.stat-item {
    text-align: center;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #007bff;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #007bff, #6c757d);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
    padding-left: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -2.5rem;
    top: 0.25rem;
    width: 2rem;
    height: 2rem;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    border: 3px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    border-left: 3px solid #007bff;
}

.timeline-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 0.75rem;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.badge {
    font-size: 0.85em;
}

.btn-group .btn {
    margin-right: 2px;
}
</style>
@endpush
@endsection
