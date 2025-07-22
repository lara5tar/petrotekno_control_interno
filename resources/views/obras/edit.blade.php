@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Editar Obra: {{ $obraModel->nombre_obra }}</h4>
                    <div>
                        <a href="{{ route('obras.show', $obraModel) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver detalles
                        </a>
                        <a href="{{ route('obras.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al listado
                        </a>
                    </div>
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

                    <form action="{{ route('obras.update', $obraModel) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre_obra" class="form-label">Nombre de la Obra *</label>
                                <input type="text" 
                                       class="form-control @error('nombre_obra') is-invalid @enderror" 
                                       id="nombre_obra" 
                                       name="nombre_obra" 
                                       value="{{ old('nombre_obra', $obraModel->nombre_obra) }}" 
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
                                    @foreach($estatusOptions as $option)
                                        <option value="{{ $option['valor'] }}" 
                                                {{ old('estatus', $obraModel->estatus) == $option['valor'] ? 'selected' : '' }}>
                                            {{ $option['nombre'] }}
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
                                       value="{{ old('avance', $obraModel->avance ?? 0) }}" 
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
                                       value="{{ old('fecha_inicio', $obraModel->fecha_inicio ? $obraModel->fecha_inicio->format('Y-m-d') : '') }}" 
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
                                       value="{{ old('fecha_fin', $obraModel->fecha_fin ? $obraModel->fecha_fin->format('Y-m-d') : '') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Mostrar progreso actual -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Progreso Actual:</label>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar 
                                        @if($obraModel->avance >= 100) bg-success
                                        @elseif($obraModel->avance >= 75) bg-info
                                        @elseif($obraModel->avance >= 50) bg-warning
                                        @else bg-danger
                                        @endif
                                    " 
                                    role="progressbar" 
                                    style="width: {{ $obraModel->avance ?? 0 }}%"
                                    id="progressBar">
                                        {{ $obraModel->avance ?? 0 }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Obra
                                </button>
                                <a href="{{ route('obras.show', $obraModel) }}" class="btn btn-secondary ms-2">
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
        const progressBar = document.getElementById('progressBar');
        
        if (this.value === 'completada') {
            avanceInput.value = 100;
        } else if (this.value === 'planificada') {
            avanceInput.value = 0;
        }
        
        // Actualizar barra de progreso visual
        updateProgressBar();
    });

    // Actualizar barra de progreso cuando cambia el avance
    document.getElementById('avance').addEventListener('input', function() {
        updateProgressBar();
    });

    function updateProgressBar() {
        const avance = document.getElementById('avance').value || 0;
        const progressBar = document.getElementById('progressBar');
        
        progressBar.style.width = avance + '%';
        progressBar.textContent = avance + '%';
        
        // Cambiar color según el avance
        progressBar.className = 'progress-bar ';
        if (avance >= 100) {
            progressBar.classList.add('bg-success');
        } else if (avance >= 75) {
            progressBar.classList.add('bg-info');
        } else if (avance >= 50) {
            progressBar.classList.add('bg-warning');
        } else {
            progressBar.classList.add('bg-danger');
        }
    }
</script>
@endpush
