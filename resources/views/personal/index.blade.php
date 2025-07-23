@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Listado de Personal</h4>
                    <a href="{{ route('personal.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Personal
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Buscar por nombre" 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="categoria_id" class="form-control">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categoriasOptions as $categoria)
                                        <option value="{{ $categoria->id }}" 
                                                {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre_categoria }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="estatus" class="form-control">
                                    <option value="">Todos los estatus</option>
                                    @foreach($estatusDisponibles as $estatus)
                                        <option value="{{ $estatus }}" 
                                                {{ request('estatus') == $estatus ? 'selected' : '' }}>
                                            {{ ucfirst($estatus) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="col-md-1">
                                <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de personal -->
                    @if($personal->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Categoría</th>
                                        <th>Estatus</th>
                                        <th>Usuario Asociado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personal as $persona)
                                        <tr>
                                            <td>{{ $persona->id }}</td>
                                            <td><strong>{{ $persona->nombre_completo }}</strong></td>
                                            <td>
                                                @if($persona->categoria)
                                                    {{ $persona->categoria->nombre_categoria }}
                                                @else
                                                    <span class="text-muted">Sin categoría</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($persona->estatus == 'activo')
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($persona->usuario)
                                                    <span class="badge badge-info">Sí</span>
                                                @else
                                                    <span class="badge badge-light">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('personal.show', $persona->id) }}" 
                                                       class="btn btn-info btn-sm" 
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('personal.edit', $persona->id) }}" 
                                                       class="btn btn-warning btn-sm" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$persona->usuario)
                                                        <form method="POST" 
                                                              action="{{ route('personal.destroy', $persona->id) }}" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('¿Está seguro de eliminar este personal?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-danger btn-sm" 
                                                                    title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {{ $personal->appends(request()->query())->links() }}
                        </div>

                        <!-- Información de paginación -->
                        <div class="mt-3 text-center text-muted">
                            <small>
                                Mostrando {{ $personal->firstItem() }} - {{ $personal->lastItem() }} 
                                de {{ $personal->total() }} personal
                            </small>
                        </div>

                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>No hay personal registrado</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'categoria_id', 'estatus']))
                                    No se encontró personal con los filtros aplicados.
                                @else
                                    Comience registrando el primer miembro del personal.
                                @endif
                            </p>
                            <a href="{{ route('personal.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Registrar Personal
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
