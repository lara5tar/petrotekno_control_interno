@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Listado de Mantenimientos</h4>
                    <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Mantenimiento
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" 
                                       class="form-control" 
                                       name="buscar" 
                                       placeholder="Buscar por proveedor o descripción" 
                                       value="{{ request('buscar') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="vehiculo_id" class="form-control">
                                    <option value="">Todos los vehículos</option>
                                    @foreach($vehiculosOptions as $vehiculo)
                                        <option value="{{ $vehiculo->id }}" 
                                                {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="tipo_servicio_id" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    @foreach($tiposServicioOptions as $tipo)
                                        <option value="{{ $tipo->id }}" 
                                                {{ request('tipo_servicio_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre_tipo_servicio }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" 
                                       class="form-control" 
                                       name="fecha_inicio_desde" 
                                       value="{{ request('fecha_inicio_desde') }}"
                                       title="Fecha desde">
                            </div>
                            <div class="col-md-2">
                                <input type="date" 
                                       class="form-control" 
                                       name="fecha_inicio_hasta" 
                                       value="{{ request('fecha_inicio_hasta') }}"
                                       title="Fecha hasta">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if(request()->hasAny(['buscar', 'vehiculo_id', 'tipo_servicio_id', 'fecha_inicio_desde', 'fecha_inicio_hasta']))
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-link btn-sm">
                                        <i class="fas fa-times"></i> Limpiar filtros
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>

                    <!-- Tabla de mantenimientos -->
                    @if($mantenimientos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vehículo</th>
                                        <th>Tipo de Servicio</th>
                                        <th>Proveedor</th>
                                        <th>Fecha Inicio</th>
                                        <th>Kilometraje</th>
                                        <th>Costo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mantenimientos as $mantenimiento)
                                        <tr>
                                            <td>{{ $mantenimiento->id }}</td>
                                            <td>
                                                @if($mantenimiento->vehiculo)
                                                    <strong>{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</strong><br>
                                                    <small class="text-muted">{{ $mantenimiento->vehiculo->placas }}</small>
                                                @else
                                                    <span class="text-muted">Vehículo no disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mantenimiento->tipoServicio)
                                                    {{ $mantenimiento->tipoServicio->nombre_tipo_servicio }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                            <td>{{ $mantenimiento->proveedor ?: 'No especificado' }}</td>
                                            <td>{{ $mantenimiento->fecha_inicio ? $mantenimiento->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ number_format($mantenimiento->kilometraje_servicio) }} km</td>
                                            <td>
                                                @if($mantenimiento->costo)
                                                    ${{ number_format($mantenimiento->costo, 2) }}
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mantenimiento->fecha_fin)
                                                    <span class="badge badge-success">Completado</span>
                                                @else
                                                    <span class="badge badge-warning">En Proceso</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('mantenimientos.show', $mantenimiento->id) }}" 
                                                       class="btn btn-info btn-sm" 
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" 
                                                       class="btn btn-warning btn-sm" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('¿Está seguro de eliminar este mantenimiento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-danger btn-sm" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {{ $mantenimientos->appends(request()->query())->links() }}
                        </div>

                        <!-- Información de paginación -->
                        <div class="mt-3 text-center text-muted">
                            <small>
                                Mostrando {{ $mantenimientos->firstItem() }} - {{ $mantenimientos->lastItem() }} 
                                de {{ $mantenimientos->total() }} mantenimientos
                            </small>
                        </div>

                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                            <h5>No hay mantenimientos registrados</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['buscar', 'vehiculo_id', 'tipo_servicio_id', 'fecha_inicio_desde', 'fecha_inicio_hasta']))
                                    No se encontraron mantenimientos con los filtros aplicados.
                                @else
                                    Comience registrando el primer mantenimiento de su flota vehicular.
                                @endif
                            </p>
                            <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Registrar Mantenimiento
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
