@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Obras</h4>
                    <a href="{{ route('obras.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Obra
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtros de búsqueda -->
                    <form method="GET" action="{{ route('obras.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Buscar obras..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="estatus" class="form-control">
                                    <option value="">Todos los estatus</option>
                                    @foreach($estatusOptions as $valor => $nombre)
                                        <option value="{{ $valor }}" 
                                                {{ request('estatus') == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="fecha_inicio_desde" class="form-control" 
                                       placeholder="Fecha desde" 
                                       value="{{ request('fecha_inicio_desde') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="fecha_inicio_hasta" class="form-control" 
                                       placeholder="Fecha hasta" 
                                       value="{{ request('fecha_inicio_hasta') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tabla de obras -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de la Obra</th>
                                    <th>Estatus</th>
                                    <th>Avance</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($obras as $obra)
                                <tr>
                                    <td>{{ $obra->id }}</td>
                                    <td>{{ $obra->nombre_obra }}</td>
                                    <td>
                                        <span class="badge 
                                            @switch($obra->estatus)
                                                @case('completada') bg-success @break
                                                @case('en_progreso') bg-primary @break
                                                @case('pausada') bg-warning @break
                                                @case('cancelada') bg-danger @break
                                                @default bg-secondary
                                            @endswitch
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                                        </span>
                                    </td>
                                    <td>{{ $obra->avance ?? 0 }}%</td>
                                    <td>{{ $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('obras.show', $obra) }}" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('obras.edit', $obra) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('obras.destroy', $obra) }}" 
                                                  method="POST" style="display: inline;"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta obra?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No se encontraron obras.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $obras->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
