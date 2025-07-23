@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Crear Nueva Obra</h4>
                    <a href="{{ route('obras.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al listado
                    </a>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('obras.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre_obra" class="form-label">Nombre de la Obra *</label>
                                <input type="text" 
                                       class="form-control @error('nombre_obra') is-invalid @enderror" 
                                       id="nombre_obra" 
                                       name="nombre_obra" 
                                       value="{{ old('nombre_obra') }}" 
                                       required>
                                @error('nombre_obra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="estatus" class="form-label">Estatus *</label>
                                <select class="form-control @error('estatus') is-invalid @enderror" 
                                        id="estatus" 
                                        name="estatus" 
                                        required>
                                    <option value="">Selecciona un estatus</option>
                                    @foreach($estatusOptions as $valor => $nombre)
                                        <option value="{{ $valor }}" 
                                                {{ old('estatus') == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estatus')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="avance" class="form-label">Avance (%)</label>
                                <input type="number" 
                                       class="form-control @error('avance') is-invalid @enderror" 
                                       id="avance" 
                                       name="avance" 
                                       value="{{ old('avance', 0) }}" 
                                       min="0" 
                                       max="100">
                                @error('avance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" 
                                       class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}" 
                                       required>
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                <input type="date" 
                                       class="form-control @error('fecha_fin') is-invalid @enderror" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear Obra
                                </button>
                                <a href="{{ route('obras.index') }}" class="btn btn-secondary ms-2">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-actualizar el avance a 100% cuando se marca como completada
    document.getElementById('estatus').addEventListener('change', function() {
        const avanceInput = document.getElementById('avance');
        if (this.value === 'completada') {
            avanceInput.value = 100;
        } else if (this.value === 'planificada') {
            avanceInput.value = 0;
        }
    });
</script>
@endpush
