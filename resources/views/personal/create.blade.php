@extends('layouts.app')

@section('title', 'Crear Personal')

@section('content')
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Personal</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('personal.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="nombre_completo">Nombre Completo</label>
                            <input id="nombre_completo" type="text" class="form-control @error('nombre_completo') is-invalid @enderror" 
                                   name="nombre_completo" value="{{ old('nombre_completo') }}" required>
                            @error('nombre_completo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="categoria_id">Categoría</label>
                            <select id="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror" 
                                    name="categoria_id" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre_categoria }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="estatus">Estatus</label>
                            <select id="estatus" class="form-control @error('estatus') is-invalid @enderror" 
                                    name="estatus" required>
                                <option value="">Seleccionar estatus</option>
                                <option value="activo" {{ old('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estatus') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estatus')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                Crear Personal
                            </button>
                            <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@endsection
