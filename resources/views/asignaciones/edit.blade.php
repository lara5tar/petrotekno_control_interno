@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Editar Asignación') }} #{{ $asignacion->id ?? 'N/A' }}</h4>
                    <div>
                        <a href="{{ isset($asignacion) ? route('asignaciones.show', $asignacion->id) : route('asignaciones.index') }}" class="btn btn-info me-2">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(isset($asignacion) && !$asignacion->esta_activa)
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atención:</strong> Esta asignación ya está liberada y no puede ser modificada.
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($asignacion) ? route('asignaciones.update', $asignacion->id) : '' }}">
                        @csrf
                        @method('PUT')

                        <!-- Información actual del vehículo (solo lectura) -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Vehículo Actual') }}</label>
                            <div class="form-control bg-light">
                                {{ $asignacion->vehiculo->nombre_completo ?? 'N/A' }} - {{ $asignacion->vehiculo->placas ?? 'Sin placas' }}
                            </div>
                            <small class="form-text text-muted">El vehículo no puede ser cambiado una vez creada la asignación</small>
                        </div>

                        <!-- Selección de Obra -->
                        <div class="mb-3">
                            <label for="obra_id" class="form-label">{{ __('Obra') }} *</label>
                            <select class="form-select @error('obra_id') is-invalid @enderror" 
                                    id="obra_id" name="obra_id" required
                                    {{ isset($asignacion) && !$asignacion->esta_activa ? 'disabled' : '' }}>
                                <option value="">Seleccionar obra...</option>
                                @foreach($obrasActivas ?? [] as $obra)
                                    <option value="{{ $obra->id }}" 
                                            {{ (old('obra_id', $asignacion->obra_id ?? '') == $obra->id) ? 'selected' : '' }}>
                                        {{ $obra->nombre_obra }}
                                        @if($obra->fecha_fin)
                                            (Hasta: {{ \Carbon\Carbon::parse($obra->fecha_fin)->format('d/m/Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('obra_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Selección de Personal (Operador) -->
                        <div class="mb-3">
                            <label for="personal_id" class="form-label">{{ __('Operador') }} *</label>
                            <select class="form-select @error('personal_id') is-invalid @enderror" 
                                    id="personal_id" name="personal_id" required
                                    {{ isset($asignacion) && !$asignacion->esta_activa ? 'disabled' : '' }}>
                                <option value="">Seleccionar operador...</option>
                                @foreach($personalDisponible ?? [] as $personal)
                                    <option value="{{ $personal->id }}" 
                                            {{ (old('personal_id', $asignacion->personal_id ?? '') == $personal->id) ? 'selected' : '' }}>
                                        {{ $personal->nombre_completo }}
                                        ({{ $personal->categoria->nombre_categoria ?? 'Sin categoría' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('personal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Se incluye el operador actual más otros disponibles</small>
                        </div>

                        <!-- Kilometraje Inicial -->
                        <div class="mb-3">
                            <label for="kilometraje_inicial" class="form-label">{{ __('Kilometraje Inicial') }} *</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('kilometraje_inicial') is-invalid @enderror" 
                                       id="kilometraje_inicial" 
                                       name="kilometraje_inicial" 
                                       value="{{ old('kilometraje_inicial', $asignacion->kilometraje_inicial ?? '') }}" 
                                       min="0" 
                                       required
                                       {{ isset($asignacion) && !$asignacion->esta_activa ? 'readonly' : '' }}>
                                <span class="input-group-text">km</span>
                            </div>
                            @error('kilometraje_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($asignacion) && $asignacion->vehiculo)
                                <small class="form-text text-muted">
                                    Kilometraje actual del vehículo: {{ number_format($asignacion->vehiculo->kilometraje_actual ?? 0) }} km
                                </small>
                            @endif
                        </div>

                        <!-- Fecha de Asignación (solo lectura) -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Fecha de Asignación') }}</label>
                            <div class="form-control bg-light">
                                {{ isset($asignacion) && $asignacion->fecha_asignacion ? \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d/m/Y H:i') : 'N/A' }}
                            </div>
                            <small class="form-text text-muted">La fecha de asignación no puede ser modificada</small>
                        </div>

                        <!-- Estado actual -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Estado Actual') }}</label>
                            <div class="form-control bg-light">
                                @if(isset($asignacion))
                                    @if($asignacion->esta_activa)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-secondary">Liberada</span>
                                        @if($asignacion->fecha_liberacion)
                                            - {{ \Carbon\Carbon::parse($asignacion->fecha_liberacion)->format('d/m/Y H:i') }}
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">{{ __('Observaciones') }}</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="3" 
                                      maxlength="1000"
                                      {{ isset($asignacion) && !$asignacion->esta_activa ? 'readonly' : '' }}>{{ old('observaciones', $asignacion->observaciones ?? '') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 1000 caracteres</small>
                        </div>

                        @if(isset($asignacion) && $asignacion->esta_activa)
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('asignaciones.show', $asignacion->id) }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Actualizar Asignación
                                </button>
                            </div>
                        @else
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ isset($asignacion) ? route('asignaciones.show', $asignacion->id) : route('asignaciones.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        @endif
                    </form>

                    @if(isset($asignacion) && $asignacion->esta_activa)
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success w-100" onclick="liberarAsignacion({{ $asignacion->id }})">
                                    <i class="fas fa-unlock"></i> Liberar Asignación
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger w-100" onclick="eliminarAsignacion({{ $asignacion->id }})">
                                    <i class="fas fa-trash"></i> Eliminar Asignación
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para liberar asignación -->
@if(isset($asignacion) && $asignacion->esta_activa)
<div class="modal fade" id="liberarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Liberar Asignación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="liberarForm" method="POST" action="{{ route('asignaciones.liberar', $asignacion->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kilometraje_final" class="form-label">Kilometraje Final *</label>
                        <input type="number" class="form-control" id="kilometraje_final" name="kilometraje_final" 
                               min="{{ $asignacion->kilometraje_inicial }}" required>
                        <small class="form-text text-muted">
                            Debe ser mayor a {{ number_format($asignacion->kilometraje_inicial) }} km (kilometraje inicial)
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_liberacion" class="form-label">Observaciones de Liberación</label>
                        <textarea class="form-control" id="observaciones_liberacion" name="observaciones_liberacion" 
                                  rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Liberar Asignación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function liberarAsignacion(id) {
        // Limpiar formulario
        document.getElementById('kilometraje_final').value = '';
        document.getElementById('observaciones_liberacion').value = '';
        
        // Mostrar modal
        new bootstrap.Modal(document.getElementById('liberarModal')).show();
    }

    function eliminarAsignacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta asignación?\n\nEsta acción no se puede deshacer.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('asignaciones.index') }}/${id}`;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            form.appendChild(methodInput);
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
