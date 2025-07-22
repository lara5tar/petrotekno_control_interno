@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Mantenimiento #{{ $mantenimiento->id }}</h4>
                    <div class="btn-group" role="group">
                        <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('mantenimientos.index') }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Información del Vehículo -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-car"></i> Información del Vehículo</h5>
                                </div>
                                <div class="card-body">
                                    @if($mantenimiento->vehiculo)
                                        <p><strong>Marca:</strong> {{ $mantenimiento->vehiculo->marca }}</p>
                                        <p><strong>Modelo:</strong> {{ $mantenimiento->vehiculo->modelo }}</p>
                                        <p><strong>Año:</strong> {{ $mantenimiento->vehiculo->anio }}</p>
                                        <p><strong>Placas:</strong> {{ $mantenimiento->vehiculo->placas }}</p>
                                        <p><strong>Número de Serie:</strong> {{ $mantenimiento->vehiculo->n_serie }}</p>
                                        <p class="mb-0"><strong>Kilometraje Actual:</strong> {{ number_format($mantenimiento->vehiculo->kilometraje_actual) }} km</p>
                                    @else
                                        <p class="text-muted">Información del vehículo no disponible</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Información del Servicio -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-tools"></i> Información del Servicio</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Tipo de Servicio:</strong> 
                                        @if($mantenimiento->tipoServicio)
                                            {{ $mantenimiento->tipoServicio->nombre_tipo_servicio }}
                                        @else
                                            <span class="text-muted">No especificado</span>
                                        @endif
                                    </p>
                                    <p><strong>Proveedor:</strong> {{ $mantenimiento->proveedor ?: 'No especificado' }}</p>
                                    <p><strong>Kilometraje del Servicio:</strong> {{ number_format($mantenimiento->kilometraje_servicio) }} km</p>
                                    <p class="mb-0">
                                        <strong>Costo:</strong> 
                                        @if($mantenimiento->costo)
                                            ${{ number_format($mantenimiento->costo, 2) }}
                                        @else
                                            <span class="text-muted">No especificado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fechas y Estado -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-calendar-alt"></i> Fechas y Estado</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Fecha de Inicio:</strong><br>
                                            {{ $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'No especificada' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Fecha de Fin:</strong><br>
                                            {{ $mantenimiento->fecha_fin ? $mantenimiento->fecha_fin->format('d/m/Y') : 'En proceso' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Estado:</strong><br>
                                            @if($mantenimiento->fecha_fin)
                                                <span class="badge badge-success badge-lg">Completado</span>
                                            @else
                                                <span class="badge badge-warning badge-lg">En Proceso</span>
                                            @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    @if($mantenimiento->descripcion)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5><i class="fas fa-file-text"></i> Descripción del Mantenimiento</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $mantenimiento->descripcion }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Documentos Relacionados -->
                    @if($mantenimiento->documentos && $mantenimiento->documentos->count() > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5><i class="fas fa-file"></i> Documentos Relacionados</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tipo</th>
                                                        <th>Descripción</th>
                                                        <th>Fecha Vencimiento</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($mantenimiento->documentos as $documento)
                                                        <tr>
                                                            <td>
                                                                @if($documento->tipoDocumento)
                                                                    {{ $documento->tipoDocumento->nombre_tipo_documento }}
                                                                @else
                                                                    <span class="text-muted">No especificado</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $documento->descripcion ?: 'Sin descripción' }}</td>
                                                            <td>
                                                                @if($documento->fecha_vencimiento)
                                                                    {{ $documento->fecha_vencimiento->format('d/m/Y') }}
                                                                @else
                                                                    <span class="text-muted">Sin vencimiento</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($documento->ruta_archivo)
                                                                    <a href="{{ asset($documento->ruta_archivo) }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-download"></i> Descargar
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">No disponible</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Información de Registro -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle"></i> Información de Registro</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Fecha de Creación:</strong></p>
                                            <p class="text-muted">{{ $mantenimiento->created_at ? $mantenimiento->created_at->format('d/m/Y H:i:s') : 'No disponible' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Última Modificación:</strong></p>
                                            <p class="text-muted">{{ $mantenimiento->updated_at ? $mantenimiento->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" 
                                   class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Mantenimiento
                                </a>
                                <form method="POST" 
                                      action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este mantenimiento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                                <a href="{{ route('mantenimientos.index') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-list"></i> Volver al Listado
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
