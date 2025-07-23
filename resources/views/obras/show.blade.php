@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles de la Obra: {{ $obra->nombre_obra }}</h4>
                    <div>
                        <a href="{{ route('obras.edit', $obra) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('obras.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card-body">
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

                    <div class="row">
                        <!-- Información Principal -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>ID:</strong></div>
                                        <div class="col-sm-8">{{ $obra->id }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Nombre:</strong></div>
                                        <div class="col-sm-8">{{ $obra->nombre_obra }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Estatus:</strong></div>
                                        <div class="col-sm-8">
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
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Avance:</strong></div>
                                        <div class="col-sm-8">
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar 
                                                    @if($obra->avance >= 100) bg-success
                                                    @elseif($obra->avance >= 75) bg-info
                                                    @elseif($obra->avance >= 50) bg-warning
                                                    @else bg-danger
                                                    @endif
                                                " 
                                                role="progressbar" 
                                                style="width: {{ $obra->avance ?? 0 }}%">
                                                    {{ $obra->avance ?? 0 }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Fechas -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Fechas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Fecha Inicio:</strong></div>
                                        <div class="col-sm-8">
                                            {{ $obra->fecha_inicio ? $obra->fecha_inicio->format('d/m/Y') : 'No definida' }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Fecha Fin:</strong></div>
                                        <div class="col-sm-8">
                                            {{ $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : 'No definida' }}
                                        </div>
                                    </div>
                                    @if($obra->fecha_inicio && $obra->fecha_fin)
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Duración:</strong></div>
                                        <div class="col-sm-8">
                                            {{ $obra->fecha_inicio->diffInDays($obra->fecha_fin) }} días
                                        </div>
                                    </div>
                                    @endif
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Creada:</strong></div>
                                        <div class="col-sm-8">
                                            {{ $obra->fecha_creacion ? $obra->fecha_creacion->format('d/m/Y H:i') : 'No disponible' }}
                                        </div>
                                    </div>
                                    @if($obra->fecha_actualizacion)
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Actualizada:</strong></div>
                                        <div class="col-sm-8">
                                            {{ $obra->fecha_actualizacion->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Acciones</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('obras.edit', $obra) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar Obra
                                        </a>
                                        
                                        @if($obra->trashed())
                                            <form action="{{ route('obras.restore', $obra->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success"
                                                        onclick="return confirm('¿Restaurar esta obra?')">
                                                    <i class="fas fa-undo"></i> Restaurar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('obras.destroy', $obra) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('¿Estás seguro de eliminar esta obra?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
