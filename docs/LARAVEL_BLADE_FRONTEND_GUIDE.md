# 🎨 Guía Frontend Laravel Blade - Sistema de Control Interno Petrotekno

## 📋 Resumen General

Esta guía está diseñada específicamente para el **equipo de frontend** que trabajará con **Laravel Blade** como tecnología de vistas. Todos los controllers han sido convertidos al **patrón híbrido** que soporta tanto solicitudes web (Blade) como API (JSON), permitiendo máxima flexibilidad en el desarrollo.

### ✅ **Estado Actual del Sistema:**
- ✅ **Controllers Híbridos**: Soportan Blade y API simultáneamente
- ✅ **Vistas Blade Base**: Estructura completa implementada
- ✅ **Sistema de Permisos**: Integrado en vistas y controllers
- ✅ **Validación Dual**: Cliente y servidor
- ✅ **Rutas Web**: Resourceful routes completas
- ✅ **Manejo de Errores**: Redirects y mensajes flash
- ✅ **Auditoría**: Logging automático de acciones

---

## 🏗️ **Arquitectura del Sistema Híbrido**

### **Patrón Híbrido Implementado**
Cada controller detecta automáticamente el tipo de solicitud y responde apropiadamente:

```php
// Ejemplo de método híbrido
public function index(Request $request)
{
    $data = $this->getData();
    
    // Si es solicitud API (AJAX/fetch con JSON)
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $pagination_info
        ]);
    }
    
    // Si es solicitud web (navegador)
    return view('modulo.index', compact('data'));
}
```

### **Beneficios del Patrón Híbrido**
- **Flexibilidad**: Misma lógica para web y API
- **Consistency**: Validaciones y permisos unificados
- **Escalabilidad**: Fácil migración a SPA si es necesario
- **Desarrollo Rápido**: Una sola implementación, doble funcionalidad

---

## 🚗 **Módulo de Vehículos - Implementación Blade**

### **Rutas Disponibles**
```php
// Rutas web resourceful
Route::resource('vehiculos', VehiculoController::class)
    ->middleware('auth');

// Rutas API complementarias
Route::prefix('api')->group(function () {
    Route::get('vehiculos/estatus', [VehiculoController::class, 'estatusOptions']);
    Route::resource('vehiculos', VehiculoController::class);
    Route::post('vehiculos/{id}/restore', [VehiculoController::class, 'restore']);
});
```

### **Vistas Implementadas**

#### **1. Lista de Vehículos** - `resources/views/vehiculos/index.blade.php`
```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4><i class="fas fa-car"></i> Gestión de Vehículos</h4>
                    @can('crear_vehiculo')
                        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Vehículo
                        </a>
                    @endcan
                </div>
                
                <div class="card-body">
                    <!-- Filtros de búsqueda -->
                    <form method="GET" class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" name="buscar" class="form-control" 
                                   placeholder="Buscar..." value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="estatus_id" class="form-control">
                                <option value="">Todos los estatus</option>
                                @foreach($estatusOptions as $estatus)
                                    <option value="{{ $estatus->id }}" 
                                            {{ request('estatus_id') == $estatus->id ? 'selected' : '' }}>
                                        {{ $estatus->nombre_estatus }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="anio_desde" class="form-control" 
                                   placeholder="Año desde" value="{{ request('anio_desde') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="anio_hasta" class="form-control" 
                                   placeholder="Año hasta" value="{{ request('anio_hasta') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Lista de vehículos -->
                    <div class="row">
                        @forelse($vehiculos as $vehiculo)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card {{ $vehiculo->deleted_at ? 'bg-light' : '' }}">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            {{ $vehiculo->nombre_completo }}
                                            @if($vehiculo->deleted_at)
                                                <span class="badge badge-secondary">Eliminado</span>
                                            @else
                                                <span class="badge badge-{{ $vehiculo->estatus->activo ? 'success' : 'warning' }}">
                                                    {{ $vehiculo->estatus->nombre_estatus }}
                                                </span>
                                            @endif
                                        </h5>
                                        
                                        <p class="card-text">
                                            <small class="text-muted">Serie: {{ $vehiculo->n_serie }}</small><br>
                                            <small class="text-muted">
                                                Kilometraje: {{ number_format($vehiculo->kilometraje_actual) }} km
                                            </small>
                                        </p>

                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('vehiculos.show', $vehiculo) }}" 
                                               class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @can('editar_vehiculo')
                                                @if($vehiculo->deleted_at)
                                                    <form method="POST" 
                                                          action="{{ route('vehiculos.restore', $vehiculo) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success"
                                                                onclick="return confirm('¿Restaurar este vehículo?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('vehiculos.edit', $vehiculo) }}" 
                                                       class="btn btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                            
                                            @can('eliminar_vehiculo')
                                                @if(!$vehiculo->deleted_at)
                                                    <form method="POST" 
                                                          action="{{ route('vehiculos.destroy', $vehiculo) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                                onclick="return confirm('¿Eliminar este vehículo?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    No se encontraron vehículos
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $vehiculos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### **2. Crear Vehículo** - `resources/views/vehiculos/create.blade.php`
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus"></i> Crear Nuevo Vehículo</h4>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('vehiculos.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="marca">Marca <span class="text-danger">*</span></label>
                                    <input type="text" name="marca" id="marca" 
                                           class="form-control @error('marca') is-invalid @enderror"
                                           value="{{ old('marca') }}" required maxlength="50">
                                    @error('marca')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modelo">Modelo <span class="text-danger">*</span></label>
                                    <input type="text" name="modelo" id="modelo"
                                           class="form-control @error('modelo') is-invalid @enderror"
                                           value="{{ old('modelo') }}" required maxlength="50">
                                    @error('modelo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="anio">Año <span class="text-danger">*</span></label>
                                    <input type="number" name="anio" id="anio"
                                           class="form-control @error('anio') is-invalid @enderror"
                                           value="{{ old('anio') }}" required min="1990" max="{{ date('Y') + 1 }}">
                                    @error('anio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estatus_id">Estatus <span class="text-danger">*</span></label>
                                    <select name="estatus_id" id="estatus_id" 
                                            class="form-control @error('estatus_id') is-invalid @enderror" required>
                                        <option value="">Seleccionar estatus</option>
                                        @foreach($estatusOptions as $estatus)
                                            <option value="{{ $estatus->id }}" 
                                                    {{ old('estatus_id') == $estatus->id ? 'selected' : '' }}>
                                                {{ $estatus->nombre_estatus }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('estatus_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="n_serie">Número de Serie <span class="text-danger">*</span></label>
                                    <input type="text" name="n_serie" id="n_serie"
                                           class="form-control @error('n_serie') is-invalid @enderror"
                                           value="{{ old('n_serie') }}" required minlength="10" maxlength="30">
                                    <small class="form-text text-muted">Mínimo 10 caracteres</small>
                                    @error('n_serie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="placas">Placas <span class="text-danger">*</span></label>
                                    <input type="text" name="placas" id="placas"
                                           class="form-control @error('placas') is-invalid @enderror"
                                           value="{{ old('placas') }}" required maxlength="10" 
                                           style="text-transform: uppercase;">
                                    <small class="form-text text-muted">Ej: ABC-123 o 123-ABC</small>
                                    @error('placas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kilometraje_actual">Kilometraje Actual <span class="text-danger">*</span></label>
                                    <input type="number" name="kilometraje_actual" id="kilometraje_actual"
                                           class="form-control @error('kilometraje_actual') is-invalid @enderror"
                                           value="{{ old('kilometraje_actual') }}" required min="0" max="9999999">
                                    @error('kilometraje_actual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Intervalos de mantenimiento -->
                        <h5 class="mt-4 mb-3">Intervalos de Mantenimiento (Opcional)</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="intervalo_km_motor">Motor (km)</label>
                                    <input type="number" name="intervalo_km_motor" id="intervalo_km_motor"
                                           class="form-control @error('intervalo_km_motor') is-invalid @enderror"
                                           value="{{ old('intervalo_km_motor') }}" min="1000" max="1000000">
                                    @error('intervalo_km_motor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="intervalo_km_transmision">Transmisión (km)</label>
                                    <input type="number" name="intervalo_km_transmision" id="intervalo_km_transmision"
                                           class="form-control @error('intervalo_km_transmision') is-invalid @enderror"
                                           value="{{ old('intervalo_km_transmision') }}" min="1000" max="1000000">
                                    @error('intervalo_km_transmision')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="intervalo_km_hidraulico">Hidráulico (km)</label>
                                    <input type="number" name="intervalo_km_hidraulico" id="intervalo_km_hidraulico"
                                           class="form-control @error('intervalo_km_hidraulico') is-invalid @enderror"
                                           value="{{ old('intervalo_km_hidraulico') }}" min="1000" max="1000000">
                                    @error('intervalo_km_hidraulico')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="3"
                                      class="form-control @error('observaciones') is-invalid @enderror"
                                      maxlength="1000">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Vehículo
                            </button>
                            <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
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
    // Validación en tiempo real para placas
    const placasInput = document.getElementById('placas');
    placasInput.addEventListener('input', function() {
        const value = this.value.toUpperCase();
        const pattern = /^[A-Z0-9\-]{0,10}$/;
        
        if (!pattern.test(value)) {
            this.setCustomValidity('Solo se permiten letras, números y guiones');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Validación para número de serie
    const nSerieInput = document.getElementById('n_serie');
    nSerieInput.addEventListener('input', function() {
        if (this.value.length < 10) {
            this.setCustomValidity('Debe tener al menos 10 caracteres');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>
@endpush
```

---

## 🔧 **Módulo de Mantenimientos - Implementación Blade**

### **Vista Index de Mantenimientos**
```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4><i class="fas fa-wrench"></i> Gestión de Mantenimientos</h4>
                </div>
                <div class="col-auto">
                    @can('crear_mantenimientos')
                        <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Mantenimiento
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtros avanzados -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <select name="vehiculo_id" class="form-control">
                        <option value="">Todos los vehículos</option>
                        @foreach($vehiculosOptions as $vehiculo)
                            <option value="{{ $vehiculo->id }}" 
                                    {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                {{ $vehiculo->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tipo_servicio_id" class="form-control">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposServicioOptions as $tipo)
                            <option value="{{ $tipo->id }}" 
                                    {{ request('tipo_servicio_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre_tipo_servicio }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_inicio_desde" class="form-control" 
                           value="{{ request('fecha_inicio_desde') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('mantenimientos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </form>

            <!-- Lista de mantenimientos -->
            @forelse($mantenimientos as $mantenimiento)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    {{ $mantenimiento->vehiculo->nombre_completo }}
                                    <span class="badge badge-primary">
                                        {{ $mantenimiento->tipoServicio->nombre_tipo_servicio }}
                                    </span>
                                </h5>
                                <p class="card-text">{{ $mantenimiento->descripcion }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ $mantenimiento->fecha_inicio->format('d/m/Y') }}
                                    @if($mantenimiento->proveedor)
                                        | <i class="fas fa-user"></i> {{ $mantenimiento->proveedor }}
                                    @endif
                                    | <i class="fas fa-tachometer-alt"></i> {{ number_format($mantenimiento->kilometraje_servicio) }} km
                                    @if($mantenimiento->costo)
                                        | <i class="fas fa-dollar-sign"></i> ${{ number_format($mantenimiento->costo, 2) }}
                                    @endif
                                </small>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('mantenimientos.show', $mantenimiento) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('actualizar_mantenimientos')
                                        <a href="{{ route('mantenimientos.edit', $mantenimiento) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('eliminar_mantenimientos')
                                        <form method="POST" action="{{ route('mantenimientos.destroy', $mantenimiento) }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('¿Eliminar este mantenimiento?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    No se encontraron mantenimientos
                </div>
            @endforelse

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $mantenimientos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🏢 **Módulo de Personal - Implementación Blade**

### **Vista Create de Personal**
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-user-plus"></i> Registrar Nuevo Personal</h4>
        </div>
        
        <div class="card-body">
            <form method="POST" action="{{ route('personal.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombres">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" id="nombres" 
                                   class="form-control @error('nombres') is-invalid @enderror"
                                   value="{{ old('nombres') }}" required maxlength="100">
                            @error('nombres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" id="apellidos"
                                   class="form-control @error('apellidos') is-invalid @enderror"
                                   value="{{ old('apellidos') }}" required maxlength="100">
                            @error('apellidos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cedula">Cédula <span class="text-danger">*</span></label>
                            <input type="text" name="cedula" id="cedula"
                                   class="form-control @error('cedula') is-invalid @enderror"
                                   value="{{ old('cedula') }}" required maxlength="20">
                            @error('cedula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categoria_id">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_id" id="categoria_id" 
                                    class="form-control @error('categoria_id') is-invalid @enderror" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categoriasOptions as $categoria)
                                    <option value="{{ $categoria->id }}" 
                                            {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre_categoria }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono') }}" maxlength="20">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" maxlength="100">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                   value="{{ old('fecha_nacimiento') }}">
                            @error('fecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="salario">Salario</label>
                            <input type="number" name="salario" id="salario"
                                   class="form-control @error('salario') is-invalid @enderror"
                                   value="{{ old('salario') }}" step="0.01" min="0">
                            @error('salario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea name="direccion" id="direccion" rows="2"
                              class="form-control @error('direccion') is-invalid @enderror"
                              maxlength="300">{{ old('direccion') }}</textarea>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Personal
                    </button>
                    <a href="{{ route('personal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

---

## 🏗️ **Módulo de Obras - Implementación Blade**

### **Vista Index de Obras**
```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h4><i class="fas fa-building"></i> Gestión de Obras</h4>
                </div>
                <div class="col-auto">
                    @can('crear_obras')
                        <a href="{{ route('obras.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Obra
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" class="row mb-4">
                <div class="col-md-4">
                    <input type="text" name="buscar" class="form-control" 
                           placeholder="Buscar obra..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <select name="estatus" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="planificada" {{ request('estatus') == 'planificada' ? 'selected' : '' }}>
                            Planificada
                        </option>
                        <option value="en_progreso" {{ request('estatus') == 'en_progreso' ? 'selected' : '' }}>
                            En Progreso
                        </option>
                        <option value="suspendida" {{ request('estatus') == 'suspendida' ? 'selected' : '' }}>
                            Suspendida
                        </option>
                        <option value="completada" {{ request('estatus') == 'completada' ? 'selected' : '' }}>
                            Completada
                        </option>
                        <option value="cancelada" {{ request('estatus') == 'cancelada' ? 'selected' : '' }}>
                            Cancelada
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_inicio_desde" class="form-control" 
                           value="{{ request('fecha_inicio_desde') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_inicio_hasta" class="form-control" 
                           value="{{ request('fecha_inicio_hasta') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary btn-block">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>

            <!-- Lista de obras -->
            <div class="row">
                @forelse($obras as $obra)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">{{ $obra->nombre_obra }}</h6>
                                    @php
                                        $badgeClass = match($obra->estatus) {
                                            'planificada' => 'secondary',
                                            'en_progreso' => 'primary',
                                            'suspendida' => 'warning',
                                            'completada' => 'success',
                                            'cancelada' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $obra->estatus)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Progreso:</small>
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $obra->avance ?? 0 }}%">
                                            {{ $obra->avance ?? 0 }}%
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Inicio:</small><br>
                                        <small>{{ $obra->fecha_inicio->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Fin:</small><br>
                                        <small>
                                            {{ $obra->fecha_fin ? $obra->fecha_fin->format('d/m/Y') : 'Sin fecha' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <div class="btn-group btn-group-sm d-flex">
                                    <a href="{{ route('obras.show', $obra) }}" class="btn btn-info flex-fill">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    @can('actualizar_obras')
                                        <a href="{{ route('obras.edit', $obra) }}" class="btn btn-primary flex-fill">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    @endcan
                                    @can('eliminar_obras')
                                        <form method="POST" action="{{ route('obras.destroy', $obra) }}" 
                                              class="flex-fill">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-block"
                                                    onclick="return confirm('¿Eliminar esta obra?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            No se encontraron obras
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $obras->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🔐 **Sistema de Permisos en Blade**

### **Directivas de Autorización**
```blade
<!-- Verificar permiso específico -->
@can('crear_vehiculos')
    <button class="btn btn-primary">Crear Vehículo</button>
@endcan

<!-- Verificar múltiples permisos -->
@canany(['editar_vehiculos', 'eliminar_vehiculos'])
    <div class="actions">
        @can('editar_vehiculos')
            <button class="btn btn-primary">Editar</button>
        @endcan
        @can('eliminar_vehiculos')
            <button class="btn btn-danger">Eliminar</button>
        @endcan
    </div>
@endcanany

<!-- Verificar permiso negativo -->
@cannot('eliminar_vehiculos')
    <span class="text-muted">No puede eliminar</span>
@endcannot

<!-- Verificar si es el propio usuario -->
@if(auth()->user()->id === $usuario->id)
    <span class="badge badge-primary">Tu perfil</span>
@endif
```

### **Middleware de Permisos en Controladores**
Los controllers ya implementan verificación de permisos:
```php
// Verificación automática en cada método
if (!$this->hasPermission('crear_vehiculos')) {
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Sin permisos'], 403);
    }
    return redirect()->route('home')->withErrors(['error' => 'Sin permisos']);
}
```

---

## 📨 **Manejo de Mensajes Flash**

### **En los Controllers (Ya Implementado)**
```php
// Mensaje de éxito
return redirect()->route('vehiculos.index')
    ->with('success', 'Vehículo creado exitosamente');

// Mensaje de error
return redirect()->back()
    ->with('error', 'Error al procesar la solicitud')
    ->withInput();

// Errores de validación
return redirect()->back()
    ->withErrors($validator)
    ->withInput();
```

### **En las Vistas Blade**
Agregar este componente en el layout principal:
```blade
<!-- resources/views/layouts/partials/messages.blade.php -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> 
        <strong>Por favor corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
```

Incluir en el layout principal:
```blade
<!-- resources/views/layouts/app.blade.php -->
<main class="py-4">
    <div class="container">
        @include('layouts.partials.messages')
        @yield('content')
    </div>
</main>
```

---

## 🎨 **Componentes Reutilizables**

### **Componente de Paginación**
```blade
<!-- resources/views/components/pagination.blade.php -->
@if($items->hasPages())
    <div class="d-flex justify-content-between align-items-center">
        <div class="pagination-info">
            <small class="text-muted">
                Mostrando {{ $items->firstItem() ?? 0 }} a {{ $items->lastItem() ?? 0 }} 
                de {{ $items->total() }} resultados
            </small>
        </div>
        <div class="pagination-links">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>
@endif
```

Uso en vistas:
```blade
@component('components.pagination', ['items' => $vehiculos])
@endcomponent
```

### **Componente de Filtros**
```blade
<!-- resources/views/components/filters.blade.php -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row align-items-end">
            @yield('filter-fields')
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>
```

---

## 🔄 **Funcionalidades AJAX (Opcional)**

Para funcionalidades dinámicas sin recarga de página:

### **Ejemplo: Eliminar con AJAX**
```html
<button onclick="deleteVehiculo({{ $vehiculo->id }})" class="btn btn-danger btn-sm">
    <i class="fas fa-trash"></i>
</button>

<script>
async function deleteVehiculo(id) {
    if (!confirm('¿Está seguro de eliminar este vehículo?')) return;
    
    try {
        const response = await fetch(`/api/vehiculos/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + getUserToken(),
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            showSuccess('Vehículo eliminado exitosamente');
            // Recargar la tabla o eliminar la fila
            location.reload();
        } else {
            showError('Error al eliminar el vehículo');
        }
    } catch (error) {
        showError('Error de conexión');
    }
}

function getUserToken() {
    return localStorage.getItem('auth_token') || document.querySelector('meta[name="api-token"]').content;
}

function showSuccess(message) {
    // Implementar sistema de notificaciones
    alert(message); // Temporalmente
}

function showError(message) {
    // Implementar sistema de notificaciones
    alert(message); // Temporalmente
}
</script>
```

---

## 📚 **Recursos Adicionales**

### **Helpers de Blade Útiles**
```blade
<!-- Formatear números -->
{{ number_format($vehiculo->kilometraje_actual) }}

<!-- Formatear fechas -->
{{ $mantenimiento->fecha_inicio->format('d/m/Y') }}
{{ $mantenimiento->created_at->diffForHumans() }}

<!-- Condicionales inline -->
{{ $vehiculo->deleted_at ? 'Eliminado' : 'Activo' }}

<!-- Loop con información adicional -->
@foreach($vehiculos as $vehiculo)
    <div>{{ $vehiculo->nombre_completo }}</div>
    @if($loop->last)
        <hr>
    @endif
@endforeach

<!-- Incluir subvistas -->
@include('partials.vehiculo-card', ['vehiculo' => $vehiculo])

<!-- Yield con default -->
@yield('title', 'Sistema Petrotekno')

<!-- Verificar si existe variable -->
@isset($vehiculos)
    <p>Hay {{ $vehiculos->count() }} vehículos</p>
@endisset

<!-- Variables de PHP -->
@php
    $totalVehiculos = $vehiculos->count();
@endphp
```

### **Assets y Vite** 
```blade
<!-- En el layout principal -->
@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- CSS adicional -->
@push('styles')
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endpush

<!-- Scripts adicionales -->
@push('scripts')
<script src="{{ asset('js/custom.js') }}"></script>
@endpush
```

---

## 🚀 **Próximos Pasos Recomendados**

### **1. Implementar Layout Base**
Crear un layout master con:
- Navegación principal
- Sidebar con menús por permisos
- Sistema de notificaciones
- Meta tags y assets

### **2. Completar Módulos Restantes**
- AsignacionController (estructura básica ya existe)
- DocumentoController (estructura básica ya existe)
- KilometrajeController

### **3. Mejoras de UX**
- Confirmaciones modales
- Validación JavaScript
- Autocomplete en buscadores
- Filtros avanzados
- Exportación de datos

### **4. Componentes Adicionales**
- Tablas responsivas
- Cards reutilizables
- Formularios dinámicos
- Dashboard widgets

---

**🎯 El sistema está completamente preparado para desarrollo frontend con Laravel Blade. Todos los controllers soportan el patrón híbrido y las vistas base están implementadas para comenzar inmediatamente.**
