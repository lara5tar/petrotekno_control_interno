@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Transferir Asignación') }}</h4>
                    <p class="mb-0 text-muted">
                        Vehículo: <strong>{{ $asignacion->vehiculo->nombre_completo }}</strong> - 
                        Operador actual: <strong>{{ $asignacion->personal->nombre_completo }}</strong>
                    </p>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('asignaciones.transferir', $asignacion->id) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nuevo_operador_id" class="form-label">{{ __('Nuevo Operador') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('nuevo_operador_id') is-invalid @enderror" 
                                            id="nuevo_operador_id" 
                                            name="nuevo_operador_id" 
                                            required>
                                        <option value="">{{ __('Seleccionar operador...') }}</option>
                                        @foreach($operadoresDisponibles as $operador)
                                            <option value="{{ $operador->id }}" 
                                                    {{ old('nuevo_operador_id') == $operador->id ? 'selected' : '' }}>
                                                {{ $operador->nombre_completo }} - {{ $operador->categoria->nombre_categoria ?? 'Sin categoría' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nuevo_operador_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="kilometraje_transferencia" class="form-label">{{ __('Kilometraje de Transferencia') }} <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('kilometraje_transferencia') is-invalid @enderror" 
                                           id="kilometraje_transferencia" 
                                           name="kilometraje_transferencia" 
                                           value="{{ old('kilometraje_transferencia', $asignacion->vehiculo->kilometraje_actual) }}" 
                                           min="{{ $asignacion->kilometraje_inicial }}"
                                           required>
                                    <small class="form-text text-muted">
                                        Km inicial: {{ number_format($asignacion->kilometraje_inicial) }} - 
                                        Km actual del vehículo: {{ number_format($asignacion->vehiculo->kilometraje_actual) }}
                                    </small>
                                    @error('kilometraje_transferencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="motivo_transferencia" class="form-label">{{ __('Motivo de Transferencia') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('motivo_transferencia') is-invalid @enderror" 
                                    id="motivo_transferencia" 
                                    name="motivo_transferencia" 
                                    required>
                                <option value="">{{ __('Seleccionar motivo...') }}</option>
                                <option value="Cambio de turno" {{ old('motivo_transferencia') == 'Cambio de turno' ? 'selected' : '' }}>
                                    Cambio de turno
                                </option>
                                <option value="Asignación temporal" {{ old('motivo_transferencia') == 'Asignación temporal' ? 'selected' : '' }}>
                                    Asignación temporal
                                </option>
                                <option value="Capacitación" {{ old('motivo_transferencia') == 'Capacitación' ? 'selected' : '' }}>
                                    Capacitación
                                </option>
                                <option value="Ausencia del operador" {{ old('motivo_transferencia') == 'Ausencia del operador' ? 'selected' : '' }}>
                                    Ausencia del operador
                                </option>
                                <option value="Redistribución de carga" {{ old('motivo_transferencia') == 'Redistribución de carga' ? 'selected' : '' }}>
                                    Redistribución de carga
                                </option>
                                <option value="Emergencia" {{ old('motivo_transferencia') == 'Emergencia' ? 'selected' : '' }}>
                                    Emergencia
                                </option>
                                <option value="Otro" {{ old('motivo_transferencia') == 'Otro' ? 'selected' : '' }}>
                                    Otro
                                </option>
                            </select>
                            @error('motivo_transferencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="observaciones_transferencia" class="form-label">{{ __('Observaciones Adicionales') }}</label>
                            <textarea class="form-control @error('observaciones_transferencia') is-invalid @enderror" 
                                      id="observaciones_transferencia" 
                                      name="observaciones_transferencia" 
                                      rows="3" 
                                      maxlength="1000" 
                                      placeholder="Información adicional sobre la transferencia...">{{ old('observaciones_transferencia') }}</textarea>
                            <small class="form-text text-muted">Opcional - Máximo 1000 caracteres</small>
                            @error('observaciones_transferencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>La transferencia creará un registro en el historial de la asignación</li>
                                <li>Se actualizará automáticamente el kilometraje del vehículo si es necesario</li>
                                <li>El nuevo operador no debe tener otras asignaciones activas</li>
                                <li>Esta acción quedará registrada en el log de auditoría</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('asignaciones.show', $asignacion->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-exchange-alt"></i> {{ __('Transferir Asignación') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-incrementar el kilometraje sugerido
    const kmInput = document.getElementById('kilometraje_transferencia');
    const kmActual = {{ $asignacion->vehiculo->kilometraje_actual }};
    
    // Validación en tiempo real del kilometraje
    kmInput.addEventListener('input', function() {
        const valor = parseInt(this.value);
        const minimo = {{ $asignacion->kilometraje_inicial }};
        
        if (valor < minimo) {
            this.setCustomValidity(`El kilometraje debe ser mayor a ${minimo.toLocaleString()}`);
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Mostrar motivo personalizado si se selecciona "Otro"
    const motivoSelect = document.getElementById('motivo_transferencia');
    motivoSelect.addEventListener('change', function() {
        if (this.value === 'Otro') {
            // Convertir el select en input text temporalmente
            const input = document.createElement('input');
            input.type = 'text';
            input.className = this.className;
            input.name = this.name;
            input.id = this.id;
            input.placeholder = 'Especifique el motivo...';
            input.required = true;
            input.maxLength = 500;
            
            this.parentNode.replaceChild(input, this);
            input.focus();
        }
    });
});
</script>
@endsection
