@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">⚠️ Alertas de Mantenimiento</h1>
                    <p class="text-muted mb-0">Vehículos que requieren mantenimiento preventivo basado en kilometraje</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('kilometrajes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Kilometrajes
                    </a>
                    @can('crear_kilometrajes')
                    <a href="{{ route('kilometrajes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Resumen de Alertas -->
            @if($alertas->count() > 0)
            <div class="row mb-4">
                @php
                    $urgentes = $alertas->where('alerta.urgente', true)->count();
                    $proximas = $alertas->where('alerta.urgente', false)->count();
                    $totalVehiculos = $alertas->pluck('vehiculo.id')->unique()->count();
                @endphp
                
                <div class="col-md-3 mb-3">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Mantenimientos Urgentes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $urgentes }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Próximos Mantenimientos
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $proximas }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Vehículos Afectados
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVehiculos }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-car fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total de Alertas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $alertas->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Listado de Alertas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell"></i> Alertas de Mantenimiento por Vehículo
                    </h6>
                </div>
                <div class="card-body">
                    @if($alertas->count() > 0)
                    <div class="row">
                        @foreach($alertas->groupBy('vehiculo.id') as $vehiculoId => $alertasVehiculo)
                        @php $vehiculo = $alertasVehiculo->first()['vehiculo']; @endphp
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card alert-card {{ $alertasVehiculo->where('alerta.urgente', true)->count() > 0 ? 'border-danger' : 'border-warning' }}">
                                <div class="card-header py-2 {{ $alertasVehiculo->where('alerta.urgente', true)->count() > 0 ? 'bg-danger text-white' : 'bg-warning text-dark' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-car"></i> {{ $vehiculo->placas }}
                                        </h6>
                                        @if($alertasVehiculo->where('alerta.urgente', true)->count() > 0)
                                        <span class="badge bg-light text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> URGENTE
                                        </span>
                                        @endif
                                    </div>
                                    <small>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</small>
                                </div>
                                <div class="card-body">
                                    <!-- Kilometraje Actual -->
                                    <div class="mb-3 text-center">
                                        <div class="kilometraje-display">
                                            {{ number_format($alertasVehiculo->first()['ultimo_kilometraje']->kilometraje) }} km
                                        </div>
                                        <small class="text-muted">Kilometraje actual</small>
                                    </div>

                                    <!-- Alertas del vehículo -->
                                    @foreach($alertasVehiculo as $alertaData)
                                    @php $alerta = $alertaData['alerta']; @endphp
                                    <div class="alert {{ $alerta['urgente'] ? 'alert-danger' : 'alert-warning' }} py-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $alerta['tipo'] }}</strong>
                                                <br>
                                                <small>Próximo: {{ number_format($alerta['proximo_km']) }} km</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="badge {{ $alerta['urgente'] ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                    {{ number_format($alerta['km_restantes']) }} km
                                                </div>
                                                <div><small>restantes</small></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                    <!-- Acciones -->
                                    <div class="d-flex gap-1 mt-3">
                                        @can('ver_kilometrajes')
                                        <a href="{{ route('kilometrajes.historial', $vehiculo->id) }}" 
                                           class="btn btn-sm btn-outline-info flex-fill"
                                           title="Ver historial">
                                            <i class="fas fa-history"></i> Historial
                                        </a>
                                        @endcan
                                        
                                        @can('crear_kilometrajes')
                                        <a href="{{ route('kilometrajes.create') }}?vehiculo_id={{ $vehiculo->id }}" 
                                           class="btn btn-sm btn-outline-primary flex-fill"
                                           title="Nuevo registro">
                                            <i class="fas fa-plus"></i> Registro
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">¡Excelente! No hay alertas de mantenimiento</h5>
                        <p class="text-muted">
                            Todos los vehículos están al día con sus mantenimientos preventivos basados en kilometraje.
                        </p>
                        <div class="mt-4">
                            @can('ver_kilometrajes')
                            <a href="{{ route('kilometrajes.index') }}" class="btn btn-primary me-2">
                                <i class="fas fa-list"></i> Ver Kilometrajes
                            </a>
                            @endcan
                            @can('crear_kilometrajes')
                            <a href="{{ route('kilometrajes.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Nuevo Registro
                            </a>
                            @endcan
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Información Adicional -->
            @if($alertas->count() > 0)
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-info-circle"></i> Información sobre las Alertas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Mantenimientos Urgentes
                            </h6>
                            <p class="text-muted">
                                Se consideran urgentes cuando faltan 1,000 km o menos para el próximo mantenimiento. 
                                Estos vehículos deben programarse para mantenimiento inmediato.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">
                                <i class="fas fa-clock"></i> Próximos Mantenimientos
                            </h6>
                            <p class="text-muted">
                                Vehículos que se acercan al kilometraje de mantenimiento. Es recomendable comenzar 
                                a planificar el mantenimiento preventivo.
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-info">
                                <i class="fas fa-wrench"></i> Tipos de Mantenimiento
                            </h6>
                            <ul class="list-unstyled">
                                <li><strong>Motor:</strong> Cambio de aceite y filtros del motor</li>
                                <li><strong>Transmisión:</strong> Cambio de aceite de transmisión</li>
                                <li><strong>Hidráulico:</strong> Cambio de aceite del sistema hidráulico</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.alert-card {
    transition: transform 0.2s ease-in-out;
}

.alert-card:hover {
    transform: translateY(-2px);
}

.kilometraje-display {
    font-size: 1.5rem;
    font-weight: 700;
    color: #007bff;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.badge {
    font-size: 0.8rem;
}

.alert {
    border-radius: 0.5rem;
}

.gap-1 {
    gap: 0.25rem !important;
}

.text-xs {
    font-size: 0.75rem;
}

.no-gutters {
    margin-right: 0;
    margin-left: 0;
}

.no-gutters > .col,
.no-gutters > [class*="col-"] {
    padding-right: 0;
    padding-left: 0;
}

.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.mr-2 {
    margin-right: 0.5rem !important;
}

.h-100 {
    height: 100% !important;
}
</style>
@endpush
@endsection
