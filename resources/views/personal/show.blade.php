@extends('layouts.app')

@section('title', 'Detalle Personal')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalle del Personal</h3>
                    <div class="card-tools">
                        <a href="{{ route('personal.edit', $personal) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $personal->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <td>{{ $personal->nombre_completo }}</td>
                                </tr>
                                <tr>
                                    <th>Categoría</th>
                                    <td>{{ $personal->categoria->nombre_categoria ?? 'Sin categoría' }}</td>
                                </tr>
                                <tr>
                                    <th>Estatus</th>
                                    <td>
                                        <span class="badge badge-{{ $personal->estatus === 'Activo' ? 'success' : ($personal->estatus === 'Inactivo' ? 'secondary' : 'warning') }}">
                                            {{ $personal->estatus }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha Creación</th>
                                    <td>{{ $personal->fecha_creacion ? $personal->fecha_creacion->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Última Actualización</th>
                                    <td>{{ $personal->fecha_actualizacion ? $personal->fecha_actualizacion->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            @if($personal->usuario)
                                <h5>Información de Usuario</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Email</th>
                                        <td>{{ $personal->usuario->correo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rol</th>
                                        <td>{{ $personal->usuario->rol->nombre_rol ?? 'Sin rol' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estado Usuario</th>
                                        <td>
                                            <span class="badge badge-{{ $personal->usuario->trashed() ? 'danger' : 'success' }}">
                                                {{ $personal->usuario->trashed() ? 'Inactivo' : 'Activo' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Este personal no tiene una cuenta de usuario asociada.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('personal.edit', $personal) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Personal
                    </a>
                    
                    @if(!$personal->usuario)
                        <form action="{{ route('personal.destroy', $personal) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('¿Estás seguro de eliminar este personal?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Ver Todos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
