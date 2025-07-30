@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Nueva Asignación') }}</h4>
                    <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
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

                    <form method="POST" action="{{ route('asignaciones.store') }}">
                        @csrf

                        <!-- Selección de Vehículo -->
                        <div class="mb-3">
                            <label for="vehiculo_id" class="form-label">{{ __('Vehículo') }} *</label>
                            <select class="form-select @error('vehiculo_id') is-invalid @enderror" 
                                    id="vehiculo_id" name="vehiculo_id" required>
                                <option value="">Seleccionar vehículo...</option>
                                @foreach($vehiculosDisponibles ?? [] as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" 
                                            {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}
                                            data-km-actual="{{ $vehiculo->kilometraje_actual ?? 0 }}">
                                        {{ $vehiculo->nombre_completo }} - {{ $vehiculo->placas ?? 'Sin placas' }}
                                        ({{ number_format($vehiculo->kilometraje_actual ?? 0) }} km)
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Solo se muestran vehículos disponibles (sin asignaciones activas)</small>
                        </div>

                        <!-- Selección de Obra -->
                        <div class="mb-3">
                            <label for="obra_id" class="form-label">{{ __('Obra') }} *</label>
                            @if($obraPreseleccionada)
                                <div class="alert alert-info mb-2">
                                    <i class="fas fa-info-circle"></i> Obra preseleccionada: <strong>{{ $obraPreseleccionada->nombre_obra }}</strong>
                                </div>
                            @endif
                            <select class="form-select @error('obra_id') is-invalid @enderror" 
                                    id="obra_id" name="obra_id" required>
                                <option value="">Seleccionar obra...</option>
                                @foreach($obrasActivas ?? [] as $obra)
                                    @php
                                        $selected = false;
                                        if (old('obra_id')) {
                                            $selected = old('obra_id') == $obra->id;
                                        } elseif ($obraPreseleccionada) {
                                            $selected = $obraPreseleccionada->id == $obra->id;
                                        }
                                    @endphp
                                    <option value="{{ $obra->id }}" {{ $selected ? 'selected' : '' }}>
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
                            <small class="form-text text-muted">Solo se muestran obras activas</small>
                        </div>

                        <!-- Selección de Personal (Operador) -->
                        <div class="mb-3">
                            <label for="personal_id" class="form-label">{{ __('Operador') }} *</label>
                            <select class="form-select @error('personal_id') is-invalid @enderror" 
                                    id="personal_id" name="personal_id" required>
                                <option value="">Seleccionar operador...</option>
                                @foreach($personalDisponible ?? [] as $personal)
                                    <option value="{{ $personal->id }}" {{ old('personal_id') == $personal->id ? 'selected' : '' }}>
                                        {{ $personal->nombre_completo }}
                                        ({{ $personal->categoria->nombre_categoria ?? 'Sin categoría' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('personal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Solo se muestra personal disponible (sin asignaciones activas)</small>
                        </div>

                        <!-- Kilometraje Inicial -->
                        <div class="mb-3">
                            <label for="kilometraje_inicial" class="form-label">{{ __('Kilometraje Inicial') }} *</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('kilometraje_inicial') is-invalid @enderror" 
                                       id="kilometraje_inicial" 
                                       name="kilometraje_inicial" 
                                       value="{{ old('kilometraje_inicial') }}" 
                                       min="0" 
                                       required>
                                <span class="input-group-text">km</span>
                            </div>
                            @error('kilometraje_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="km-sugerido">
                                Selecciona un vehículo para ver el kilometraje actual
                            </small>
                        </div>

                        <!-- Fecha de Asignación -->
                        <div class="mb-3">
                            <label for="fecha_asignacion" class="form-label">{{ __('Fecha de Asignación') }} *</label>
                            <input type="datetime-local" 
                                   class="form-control @error('fecha_asignacion') is-invalid @enderror" 
                                   id="fecha_asignacion" 
                                   name="fecha_asignacion" 
                                   value="{{ old('fecha_asignacion', now()->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('fecha_asignacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">{{ __('Observaciones') }}</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="3" 
                                      maxlength="1000">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Máximo 1000 caracteres</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('asignaciones.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Asignación
                            </button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const vehiculoSelect = document.getElementById('vehiculo_id');
        const kilometrajeInput = document.getElementById('kilometraje_inicial');
        const kmSugerido = document.getElementById('km-sugerido');

        vehiculoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                const kmActual = selectedOption.getAttribute('data-km-actual');
                kilometrajeInput.value = kmActual;
                kmSugerido.textContent = `Kilometraje actual del vehículo: ${Number(kmActual).toLocaleString()} km`;
                kmSugerido.className = 'form-text text-info';
            } else {
                kilometrajeInput.value = '';
                kmSugerido.textContent = 'Selecciona un vehículo para ver el kilometraje actual';
                kmSugerido.className = 'form-text text-muted';
            }
        });

        // Validación en tiempo real del kilometraje
        kilometrajeInput.addEventListener('input', function() {
            const vehiculoSelect = document.getElementById('vehiculo_id');
            const selectedOption = vehiculoSelect.options[vehiculoSelect.selectedIndex];
            
            if (selectedOption.value) {
                const kmActual = parseInt(selectedOption.getAttribute('data-km-actual'));
                const kmIngresado = parseInt(this.value);
                
                if (kmIngresado < kmActual) {
                    this.setCustomValidity('El kilometraje inicial no puede ser menor al kilometraje actual del vehículo');
                } else {
                    this.setCustomValidity('');
                }
            }
        });
    });
</script>
@endpush
