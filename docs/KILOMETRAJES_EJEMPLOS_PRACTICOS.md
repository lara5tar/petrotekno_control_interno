# 💡 Ejemplos Prácticos - Integración Kilometrajes Frontend

## 🚀 Casos de Uso Comunes

### 1. 📋 Lista Principal con Filtros

```blade
{{-- resources/views/kilometrajes/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tachometer-alt"></i> Gestión de Kilometrajes</h1>
        @can('crear_kilometrajes')
            <a href="{{ route('kilometrajes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Registro
            </a>
        @endcan
    </div>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="vehiculo_id" class="form-select">
                        <option value="">🚗 Todos los vehículos</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" 
                                {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <select name="obra_id" class="form-select">
                        <option value="">🏗️ Todas las obras</option>
                        @foreach($obras as $obra)
                            <option value="{{ $obra->id }}" 
                                {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                {{ $obra->nombre_obra }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <input type="date" name="fecha_inicio" class="form-control" 
                           value="{{ request('fecha_inicio') }}" placeholder="Fecha inicio">
                </div>
                
                <div class="col-md-2">
                    <input type="date" name="fecha_fin" class="form-control" 
                           value="{{ request('fecha_fin') }}" placeholder="Fecha fin">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de resultados --}}
    <div class="card">
        <div class="card-body">
            @if($kilometrajes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>📅 Fecha</th>
                                <th>🚗 Vehículo</th>
                                <th>📊 Kilometraje</th>
                                <th>🏗️ Obra</th>
                                <th>👤 Registrado por</th>
                                <th>💬 Observaciones</th>
                                <th>⚙️ Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kilometrajes as $kilometraje)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ $kilometraje->fecha_captura_formatted }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</strong><br>
                                        <small class="text-muted">{{ $kilometraje->vehiculo->placas }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info fs-6">
                                            {{ number_format($kilometraje->kilometraje) }} km
                                        </span>
                                        @if($kilometraje->diferencia_kilometraje)
                                            <br><small class="text-success">
                                                +{{ number_format($kilometraje->diferencia_kilometraje) }} km
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $kilometraje->obra->nombre_obra }}</td>
                                    <td>{{ $kilometraje->usuarioCaptura->name }}</td>
                                    <td>
                                        <small>{{ Str::limit($kilometraje->observaciones, 50) }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('kilometrajes.show', $kilometraje) }}" 
                                               class="btn btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('editar_kilometrajes')
                                                <a href="{{ route('kilometrajes.edit', $kilometraje) }}" 
                                                   class="btn btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('eliminar_kilometrajes')
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="confirmarEliminar({{ $kilometraje->id }})" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $kilometrajes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No se encontraron registros</h4>
                    <p class="text-muted">Prueba ajustando los filtros o 
                        <a href="{{ route('kilometrajes.create') }}">registra el primer kilometraje</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

### 2. ➕ Formulario de Creación Inteligente

```blade
{{-- resources/views/kilometrajes/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus-circle"></i> Registrar Nuevo Kilometraje</h4>
                </div>

                <form action="{{ route('kilometrajes.store') }}" method="POST" id="kilometraje-form">
                    @csrf
                    <div class="card-body">
                        {{-- Vehículo con info dinámica --}}
                        <div class="mb-3">
                            <label for="vehiculo_id" class="form-label">🚗 Vehículo *</label>
                            <select name="vehiculo_id" id="vehiculo_id" 
                                    class="form-select @error('vehiculo_id') is-invalid @enderror" 
                                    required onchange="actualizarInfoVehiculo()">
                                <option value="">Seleccionar vehículo...</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" 
                                            data-ultimo-km="{{ $vehiculo->ultimo_kilometraje ?? 0 }}"
                                            data-marca="{{ $vehiculo->marca }}"
                                            data-modelo="{{ $vehiculo->modelo }}"
                                            data-placas="{{ $vehiculo->placas }}"
                                            {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- Info dinámica del vehículo --}}
                            <div id="vehiculo-info" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <strong>📊 Último kilometraje registrado:</strong> 
                                    <span id="ultimo-km-display">0</span> km
                                    <br>
                                    <small>El nuevo kilometraje debe ser mayor a este valor</small>
                                </div>
                            </div>
                        </div>

                        {{-- Kilometraje con validación visual --}}
                        <div class="mb-3">
                            <label for="kilometraje" class="form-label">📊 Kilometraje Actual *</label>
                            <div class="input-group">
                                <input type="number" name="kilometraje" id="kilometraje" 
                                       class="form-control @error('kilometraje') is-invalid @enderror" 
                                       value="{{ old('kilometraje') }}" 
                                       min="1" step="1" required 
                                       onblur="validarKilometraje()">
                                <span class="input-group-text">km</span>
                                @error('kilometraje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="kilometraje-feedback" class="form-text"></div>
                        </div>

                        {{-- Fecha con valor por defecto --}}
                        <div class="mb-3">
                            <label for="fecha_captura" class="form-label">📅 Fecha de Captura *</label>
                            <input type="date" name="fecha_captura" id="fecha_captura" 
                                   class="form-control @error('fecha_captura') is-invalid @enderror" 
                                   value="{{ old('fecha_captura', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('fecha_captura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Obra activa --}}
                        <div class="mb-3">
                            <label for="obra_id" class="form-label">🏗️ Obra *</label>
                            <select name="obra_id" id="obra_id" 
                                    class="form-select @error('obra_id') is-invalid @enderror" required>
                                <option value="">Seleccionar obra...</option>
                                @foreach($obras as $obra)
                                    <option value="{{ $obra->id }}" 
                                            {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                        {{ $obra->nombre_obra }}
                                        <small>({{ ucfirst($obra->estatus) }})</small>
                                    </option>
                                @endforeach
                            </select>
                            @error('obra_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Observaciones con contador --}}
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">💬 Observaciones</label>
                            <textarea name="observaciones" id="observaciones" 
                                      class="form-control @error('observaciones') is-invalid @enderror" 
                                      rows="4" maxlength="1000" 
                                      placeholder="Descripción del trabajo realizado, estado del vehículo, etc."
                                      oninput="actualizarContador()">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="char-counter">0</span>/1000 caracteres
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fas fa-save"></i> Registrar Kilometraje
                        </button>
                        <a href="{{ route('kilometrajes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Panel de ayuda --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-question-circle"></i> Ayuda</h5>
                </div>
                <div class="card-body">
                    <h6>📋 Instrucciones:</h6>
                    <ul class="list-unstyled">
                        <li>✅ Selecciona el vehículo</li>
                        <li>📊 Ingresa el kilometraje actual</li>
                        <li>📅 Confirma la fecha</li>
                        <li>🏗️ Selecciona la obra</li>
                        <li>💬 Añade observaciones (opcional)</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>⚠️ Importante:</h6>
                    <ul class="list-unstyled small text-muted">
                        <li>• El kilometraje debe ser mayor al último registrado</li>
                        <li>• La fecha no puede ser futura</li>
                        <li>• Solo se muestran obras activas</li>
                        <li>• Las observaciones ayudan en el mantenimiento</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar
    actualizarContador();
});

function actualizarInfoVehiculo() {
    const select = document.getElementById('vehiculo_id');
    const info = document.getElementById('vehiculo-info');
    const display = document.getElementById('ultimo-km-display');
    const kilometrajeInput = document.getElementById('kilometraje');
    
    if (select.value) {
        const option = select.options[select.selectedIndex];
        const ultimoKm = parseInt(option.dataset.ultimoKm) || 0;
        
        display.textContent = ultimoKm.toLocaleString();
        info.style.display = 'block';
        
        kilometrajeInput.min = ultimoKm + 1;
        kilometrajeInput.placeholder = `Debe ser mayor a ${ultimoKm.toLocaleString()} km`;
        
        // Limpiar si el valor actual es inválido
        if (kilometrajeInput.value && parseInt(kilometrajeInput.value) <= ultimoKm) {
            kilometrajeInput.value = '';
        }
    } else {
        info.style.display = 'none';
        kilometrajeInput.min = 1;
        kilometrajeInput.placeholder = '';
    }
}

function validarKilometraje() {
    const select = document.getElementById('vehiculo_id');
    const input = document.getElementById('kilometraje');
    const feedback = document.getElementById('kilometraje-feedback');
    
    if (!select.value || !input.value) return;
    
    const ultimoKm = parseInt(select.options[select.selectedIndex].dataset.ultimoKm) || 0;
    const nuevoKm = parseInt(input.value);
    
    if (nuevoKm <= ultimoKm) {
        input.classList.add('is-invalid');
        feedback.className = 'form-text text-danger';
        feedback.textContent = `⚠️ Debe ser mayor a ${ultimoKm.toLocaleString()} km`;
        return false;
    } else {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        feedback.className = 'form-text text-success';
        const diferencia = nuevoKm - ultimoKm;
        feedback.textContent = `✅ Diferencia: +${diferencia.toLocaleString()} km`;
        return true;
    }
}

function actualizarContador() {
    const textarea = document.getElementById('observaciones');
    const counter = document.getElementById('char-counter');
    
    counter.textContent = textarea.value.length;
    
    if (textarea.value.length > 900) {
        counter.classList.add('text-warning');
    } else {
        counter.classList.remove('text-warning');
    }
}

// Validación antes de enviar
document.getElementById('kilometraje-form').addEventListener('submit', function(e) {
    if (!validarKilometraje()) {
        e.preventDefault();
        alert('Por favor corrige los errores antes de continuar');
        return false;
    }
});
</script>
@endsection
```

### 3. 👁️ Vista de Detalle Completa

```blade
{{-- resources/views/kilometrajes/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-eye"></i> Detalle del Kilometraje</h4>
                    <div class="btn-group">
                        @can('editar_kilometrajes')
                            <a href="{{ route('kilometrajes.edit', $kilometraje) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endcan
                        @can('eliminar_kilometrajes')
                            <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="confirmarEliminar({{ $kilometraje->id }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-truck text-primary"></i> Información del Vehículo</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Marca/Modelo:</strong></td>
                                    <td>{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Placas:</strong></td>
                                    <td><span class="badge bg-secondary">{{ $kilometraje->vehiculo->placas }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>N° Serie:</strong></td>
                                    <td>{{ $kilometraje->vehiculo->n_serie }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="fas fa-building text-success"></i> Información de la Obra</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Obra:</strong></td>
                                    <td>{{ $kilometraje->obra->nombre_obra }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($kilometraje->obra->estatus) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($kilometraje->kilometraje) }}</h3>
                                    <p class="mb-0">Kilometraje Registrado</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $kilometraje->fecha_captura_formatted }}</h3>
                                    <p class="mb-0">Fecha de Captura</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $kilometraje->getDiasDesdeCaptura() }}</h3>
                                    <p class="mb-0">Días Transcurridos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($kilometraje->observaciones)
                        <hr>
                        <h5><i class="fas fa-comments text-warning"></i> Observaciones</h5>
                        <div class="alert alert-light">
                            {{ $kilometraje->observaciones }}
                        </div>
                    @endif

                    <hr>

                    <h5><i class="fas fa-user text-secondary"></i> Información de Registro</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Registrado por:</strong></td>
                            <td>{{ $kilometraje->usuarioCaptura->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de registro:</strong></td>
                            <td>{{ $kilometraje->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @if($kilometraje->updated_at != $kilometraje->created_at)
                            <tr>
                                <td><strong>Última actualización:</strong></td>
                                <td>{{ $kilometraje->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="card-footer">
                    <a href="{{ route('kilometrajes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Alertas de mantenimiento --}}
            @php
                $alertas = $kilometraje->calcularProximosMantenimientos();
            @endphp
            
            @if(count($alertas) > 0)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-wrench text-warning"></i> Alertas de Mantenimiento</h5>
                    </div>
                    <div class="card-body">
                        @foreach($alertas as $alerta)
                            <div class="alert {{ $alerta['urgente'] ? 'alert-danger' : 'alert-warning' }} alert-sm">
                                <strong>{{ $alerta['tipo'] }}:</strong><br>
                                <small>
                                    Próximo: {{ number_format($alerta['proximo_km']) }} km<br>
                                    Faltan: {{ number_format($alerta['km_restantes']) }} km
                                </small>
                                @if($alerta['urgente'])
                                    <br><span class="badge bg-danger">¡URGENTE!</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Historial reciente del vehículo --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history"></i> Historial Reciente</h5>
                </div>
                <div class="card-body">
                    @php
                        $historial = \App\Models\Kilometraje::where('vehiculo_id', $kilometraje->vehiculo_id)
                                    ->where('id', '!=', $kilometraje->id)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                    @endphp
                    
                    @if($historial->count() > 0)
                        <div class="timeline">
                            @foreach($historial as $hist)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ number_format($hist->kilometraje) }} km</h6>
                                        <p class="timeline-description">
                                            {{ $hist->fecha_captura_formatted }}<br>
                                            <small class="text-muted">{{ $hist->obra->nombre_obra }}</small>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Este es el primer registro del vehículo</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este registro?\n\nEsta acción no se puede deshacer.')) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/kilometrajes/${id}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 15px;
    width: 2px;
    height: calc(100% - 10px);
    background: #e9ecef;
}

.timeline-title {
    font-size: 14px;
    font-weight: bold;
    margin: 0;
}

.timeline-description {
    font-size: 12px;
    margin: 5px 0 0 0;
}
</style>
@endsection
```

### 4. 📊 AJAX para Actualizaciones Dinámicas

```javascript
// public/js/kilometrajes-advanced.js

class KilometrajesManager {
    constructor() {
        this.baseUrl = '/api/kilometrajes';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadVehiculos();
    }

    setupEventListeners() {
        // Filtros en tiempo real
        document.addEventListener('change', (e) => {
            if (e.target.matches('[data-filter]')) {
                this.applyFilters();
            }
        });

        // Validación de kilometraje
        const kilometrajeInput = document.getElementById('kilometraje');
        if (kilometrajeInput) {
            kilometrajeInput.addEventListener('input', 
                this.debounce(() => this.validateKilometraje(), 500)
            );
        }
    }

    async loadVehiculos() {
        try {
            const response = await fetch('/api/vehiculos?activos=1');
            const data = await response.json();
            
            if (data.success) {
                this.updateVehiculoSelect(data.data);
            }
        } catch (error) {
            console.error('Error cargando vehículos:', error);
        }
    }

    updateVehiculoSelect(vehiculos) {
        const select = document.getElementById('vehiculo_id');
        if (!select) return;

        select.innerHTML = '<option value="">Seleccionar vehículo...</option>';
        
        vehiculos.forEach(vehiculo => {
            const option = document.createElement('option');
            option.value = vehiculo.id;
            option.textContent = `${vehiculo.marca} ${vehiculo.modelo} - ${vehiculo.placas}`;
            option.dataset.ultimoKm = vehiculo.ultimo_kilometraje || 0;
            select.appendChild(option);
        });
    }

    async validateKilometraje() {
        const vehiculoId = document.getElementById('vehiculo_id')?.value;
        const kilometraje = document.getElementById('kilometraje')?.value;
        
        if (!vehiculoId || !kilometraje) return;

        try {
            const response = await fetch(
                `/api/kilometrajes/validate?vehiculo_id=${vehiculoId}&kilometraje=${kilometraje}`
            );
            const data = await response.json();
            
            this.showValidationResult(data);
        } catch (error) {
            console.error('Error validando kilometraje:', error);
        }
    }

    showValidationResult(result) {
        const input = document.getElementById('kilometraje');
        const feedback = document.getElementById('kilometraje-feedback');
        
        if (!input || !feedback) return;

        if (result.valid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.className = 'form-text text-success';
            feedback.textContent = `✅ ${result.message}`;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            feedback.className = 'form-text text-danger';
            feedback.textContent = `⚠️ ${result.message}`;
        }
    }

    async applyFilters() {
        const filters = this.getFilters();
        const queryString = new URLSearchParams(filters).toString();
        
        try {
            const response = await fetch(`${this.baseUrl}?${queryString}`);
            const data = await response.json();
            
            if (data.success) {
                this.updateTable(data.data);
                this.updatePagination(data);
            }
        } catch (error) {
            console.error('Error aplicando filtros:', error);
        }
    }

    getFilters() {
        const filters = {};
        
        document.querySelectorAll('[data-filter]').forEach(element => {
            if (element.value) {
                filters[element.name] = element.value;
            }
        });

        return filters;
    }

    updateTable(kilometrajes) {
        const tbody = document.querySelector('#kilometrajes-table tbody');
        if (!tbody) return;

        if (kilometrajes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No se encontraron registros</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = kilometrajes.map(km => `
            <tr>
                <td>
                    <small class="text-muted">${this.formatDate(km.fecha_captura)}</small>
                </td>
                <td>
                    <strong>${km.vehiculo.marca} ${km.vehiculo.modelo}</strong><br>
                    <small class="text-muted">${km.vehiculo.placas}</small>
                </td>
                <td>
                    <span class="badge bg-info fs-6">
                        ${this.formatNumber(km.kilometraje)} km
                    </span>
                    ${km.diferencia_kilometraje ? 
                        `<br><small class="text-success">+${this.formatNumber(km.diferencia_kilometraje)} km</small>` 
                        : ''
                    }
                </td>
                <td>${km.obra.nombre_obra}</td>
                <td>${km.usuario_captura.name}</td>
                <td>
                    <small>${this.truncate(km.observaciones || '', 50)}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="/kilometrajes/${km.id}" class="btn btn-outline-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/kilometrajes/${km.id}/edit" class="btn btn-outline-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="confirmarEliminar(${km.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Utilidades
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('es-MX');
    }

    formatNumber(number) {
        return new Intl.NumberFormat('es-MX').format(number);
    }

    truncate(text, length) {
        return text.length > length ? text.substring(0, length) + '...' : text;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new KilometrajesManager();
});

// Función global para eliminar (llamada desde el HTML)
async function confirmarEliminar(id) {
    const confirmacion = await Swal.fire({
        title: '¿Eliminar registro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (confirmacion.isConfirmed) {
        try {
            const response = await fetch(`/api/kilometrajes/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire('Eliminado', data.message, 'success');
                location.reload(); // O actualizar la tabla dinámicamente
            } else {
                await Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            console.error('Error eliminando:', error);
            await Swal.fire('Error', 'Ocurrió un error al eliminar el registro', 'error');
        }
    }
}
```

### 5. 📱 CSS Responsivo

```css
/* public/css/kilometrajes-responsive.css */

.kilometrajes-container {
    padding: 15px;
}

/* Cards responsivos */
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}

/* Badges y indicadores */
.badge {
    font-size: 0.85em;
    padding: 6px 10px;
}

.ultimo-kilometraje-display {
    font-size: 1.5em;
    font-weight: bold;
    color: #2c3e50;
}

/* Tabla responsiva */
@media (max-width: 768px) {
    .table-responsive table,
    .table-responsive thead,
    .table-responsive tbody,
    .table-responsive th,
    .table-responsive td,
    .table-responsive tr {
        display: block;
    }

    .table-responsive thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    .table-responsive tr {
        border: 1px solid #ccc;
        margin-bottom: 10px;
        padding: 10px;
    }

    .table-responsive td {
        border: none;
        position: relative;
        padding-left: 50% !important;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .table-responsive td:before {
        content: attr(data-label);
        position: absolute;
        left: 6px;
        width: 45%;
        text-align: left;
        font-weight: bold;
    }

    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .btn-group .btn {
        flex: 1;
        min-width: auto;
    }
}

/* Formularios */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.is-valid {
    border-color: #28a745;
}

.is-invalid {
    border-color: #dc3545;
}

/* Filtros */
.filtros-panel {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
}

@media (max-width: 576px) {
    .filtros-panel .row > div {
        margin-bottom: 15px;
    }
    
    .filtros-panel .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

/* Timeline para historial */
.timeline {
    position: relative;
    padding-left: 25px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

/* Alertas de mantenimiento */
.alert-sm {
    padding: 8px 12px;
    font-size: 0.875em;
    margin-bottom: 10px;
}

.mantenimiento-urgente {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

/* Loader */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Estados */
.estado-activo { color: #28a745; }
.estado-inactivo { color: #6c757d; }
.estado-mantenimiento { color: #ffc107; }
.estado-emergencia { color: #dc3545; }

/* Utilidades */
.text-shadow {
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.hover-scale:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}

.cursor-pointer {
    cursor: pointer;
}
```

---

## 🎯 **Próximos Pasos para el Frontend**

1. **Crear las vistas Blade** usando los ejemplos proporcionados
2. **Implementar el JavaScript** para funcionalidad dinámica
3. **Aplicar los estilos CSS** para una interfaz moderna
4. **Probar la integración** con las APIs existentes
5. **Personalizar** según las necesidades específicas del proyecto

El backend está **100% listo y probado** para soportar todas estas funcionalidades. ✅🚀
