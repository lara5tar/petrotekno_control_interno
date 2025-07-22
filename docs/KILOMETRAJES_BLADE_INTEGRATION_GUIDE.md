# 🎯 Guía Laravel Blade - Módulo de Kilometrajes

## 📋 **Enfoque: Renderizado del Lado del Servidor**

Esta guía está diseñada para implementar el módulo de kilometrajes usando **Laravel Blade tradicional** con formularios HTML estándar, sin JavaScript complejo ni AJAX. Todo el procesamiento se hace en el servidor.

---

## 🏗️ **1. Controlador Web (Sin API)**

```php
// app/Http/Controllers/Web/KilometrajeWebController.php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kilometraje;
use App\Models\Vehiculo;
use App\Models\Obra;
use App\Http\Requests\StoreKilometrajeRequest;
use App\Http\Requests\UpdateKilometrajeRequest;
use Illuminate\Http\Request;

class KilometrajeWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Kilometraje::with(['vehiculo', 'obra', 'usuarioCaptura']);
        
        // Filtros desde formulario GET
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }
        
        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }
        
        if ($request->filled('fecha_inicio')) {
            $query->where('fecha_captura', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->where('fecha_captura', '<=', $request->fecha_fin);
        }
        
        $kilometrajes = $query->orderBy('created_at', 'desc')->paginate(15);
        $kilometrajes->appends($request->query()); // Mantener filtros en paginación
        
        // Datos para formularios de filtro
        $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')->get();
        $obras = Obra::where('estatus', 'activa')->select('id', 'nombre_obra')->get();
        
        return view('kilometrajes.index', compact('kilometrajes', 'vehiculos', 'obras'));
    }

    public function create()
    {
        $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')->get();
        $obras = Obra::where('estatus', 'activa')->select('id', 'nombre_obra')->get();
        
        return view('kilometrajes.create', compact('vehiculos', 'obras'));
    }

    public function store(StoreKilometrajeRequest $request)
    {
        try {
            Kilometraje::create([
                'vehiculo_id' => $request->vehiculo_id,
                'kilometraje' => $request->kilometraje,
                'fecha_captura' => $request->fecha_captura,
                'obra_id' => $request->obra_id,
                'usuario_captura_id' => auth()->id(),
                'observaciones' => $request->observaciones,
            ]);

            return redirect()
                ->route('web.kilometrajes.index')
                ->with('success', '✅ Kilometraje registrado correctamente');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '❌ Error al registrar: ' . $e->getMessage());
        }
    }

    public function show(Kilometraje $kilometraje)
    {
        $kilometraje->load(['vehiculo', 'obra', 'usuarioCaptura']);
        
        // Historial reciente del mismo vehículo
        $historialReciente = Kilometraje::where('vehiculo_id', $kilometraje->vehiculo_id)
            ->where('id', '!=', $kilometraje->id)
            ->with(['obra'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('kilometrajes.show', compact('kilometraje', 'historialReciente'));
    }

    public function edit(Kilometraje $kilometraje)
    {
        $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')->get();
        $obras = Obra::where('estatus', 'activa')->select('id', 'nombre_obra')->get();
        
        return view('kilometrajes.edit', compact('kilometraje', 'vehiculos', 'obras'));
    }

    public function update(UpdateKilometrajeRequest $request, Kilometraje $kilometraje)
    {
        try {
            $kilometraje->update([
                'vehiculo_id' => $request->vehiculo_id,
                'kilometraje' => $request->kilometraje,
                'fecha_captura' => $request->fecha_captura,
                'obra_id' => $request->obra_id,
                'observaciones' => $request->observaciones,
            ]);

            return redirect()
                ->route('web.kilometrajes.show', $kilometraje)
                ->with('success', '✅ Kilometraje actualizado correctamente');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '❌ Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Kilometraje $kilometraje)
    {
        try {
            $kilometraje->delete();
            
            return redirect()
                ->route('web.kilometrajes.index')
                ->with('success', '✅ Kilometraje eliminado correctamente');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', '❌ Error al eliminar: ' . $e->getMessage());
        }
    }
}
```

---

## 🛣️ **2. Rutas Web**

```php
// routes/web.php

// Grupo de rutas web para kilometrajes (con middleware de autenticación)
Route::middleware(['auth'])->prefix('kilometrajes')->name('web.kilometrajes.')->group(function () {
    Route::get('/', [KilometrajeWebController::class, 'index'])->name('index');
    Route::get('/crear', [KilometrajeWebController::class, 'create'])->name('create');
    Route::post('/', [KilometrajeWebController::class, 'store'])->name('store');
    Route::get('/{kilometraje}', [KilometrajeWebController::class, 'show'])->name('show');
    Route::get('/{kilometraje}/editar', [KilometrajeWebController::class, 'edit'])->name('edit');
    Route::put('/{kilometraje}', [KilometrajeWebController::class, 'update'])->name('update');
    Route::delete('/{kilometraje}', [KilometrajeWebController::class, 'destroy'])->name('destroy');
});
```

---

## 🖼️ **3. Vistas Blade**

### 📋 **Index - Lista Principal**

```blade
{{-- resources/views/kilometrajes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestión de Kilometrajes')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tachometer-alt text-primary"></i> Kilometrajes</h1>
        <a href="{{ route('web.kilometrajes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Kilometraje
        </a>
    </div>

    {{-- Mensajes Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-filter"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('web.kilometrajes.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Vehículo</label>
                        <select name="vehiculo_id" class="form-select">
                            <option value="">Todos los vehículos</option>
                            @foreach($vehiculos as $vehiculo)
                                <option value="{{ $vehiculo->id }}" 
                                        {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placas }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Obra</label>
                        <select name="obra_id" class="form-select">
                            <option value="">Todas las obras</option>
                            @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" 
                                        {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                    {{ $obra->nombre_obra }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control" 
                               value="{{ request('fecha_inicio') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control" 
                               value="{{ request('fecha_fin') }}">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                @if(request()->hasAny(['vehiculo_id', 'obra_id', 'fecha_inicio', 'fecha_fin']))
                    <div class="row mt-2">
                        <div class="col-12">
                            <a href="{{ route('web.kilometrajes.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> Limpiar Filtros
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabla de Resultados --}}
    <div class="card">
        <div class="card-body">
            @if($kilometrajes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Vehículo</th>
                                <th>Kilometraje</th>
                                <th>Obra</th>
                                <th>Registrado por</th>
                                <th>Observaciones</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kilometrajes as $kilometraje)
                                <tr>
                                    <td>
                                        <strong>{{ $kilometraje->fecha_captura_formatted }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $kilometraje->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</strong>
                                        <br>
                                        <span class="badge bg-secondary">{{ $kilometraje->vehiculo->placas }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            {{ number_format($kilometraje->kilometraje) }} km
                                        </span>
                                    </td>
                                    <td>{{ $kilometraje->obra->nombre_obra }}</td>
                                    <td>{{ $kilometraje->usuarioCaptura->name }}</td>
                                    <td>
                                        @if($kilometraje->observaciones)
                                            <span title="{{ $kilometraje->observaciones }}">
                                                {{ Str::limit($kilometraje->observaciones, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('web.kilometrajes.show', $kilometraje) }}" 
                                               class="btn btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('web.kilometrajes.edit', $kilometraje) }}" 
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmarEliminacion('{{ $kilometraje->id }}')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                    <h4 class="text-muted">No se encontraron kilometrajes</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['vehiculo_id', 'obra_id', 'fecha_inicio', 'fecha_fin']))
                            Prueba ajustando los filtros de búsqueda
                        @else
                            <a href="{{ route('web.kilometrajes.create') }}">Registra el primer kilometraje</a>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal de confirmación para eliminar --}}
<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este registro de kilometraje?
                <br><strong>Esta acción no se puede deshacer.</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="eliminarForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(id) {
    const form = document.getElementById('eliminarForm');
    form.action = `/kilometrajes/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    modal.show();
}
</script>
@endsection
```

### ➕ **Create - Formulario de Creación**

```blade
{{-- resources/views/kilometrajes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Registrar Kilometraje')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus-circle text-success"></i> Registrar Nuevo Kilometraje</h4>
                </div>

                <form action="{{ route('web.kilometrajes.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        {{-- Mensajes de Error --}}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Vehículo --}}
                        <div class="mb-3">
                            <label for="vehiculo_id" class="form-label">
                                <i class="fas fa-truck text-primary"></i> Vehículo <span class="text-danger">*</span>
                            </label>
                            <select name="vehiculo_id" id="vehiculo_id" 
                                    class="form-select @error('vehiculo_id') is-invalid @enderror" required>
                                <option value="">Seleccionar vehículo...</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" 
                                            {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kilometraje --}}
                        <div class="mb-3">
                            <label for="kilometraje" class="form-label">
                                <i class="fas fa-tachometer-alt text-info"></i> Kilometraje Actual <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="kilometraje" id="kilometraje" 
                                       class="form-control @error('kilometraje') is-invalid @enderror" 
                                       value="{{ old('kilometraje') }}" 
                                       min="1" step="1" required 
                                       placeholder="Ej: 45000">
                                <span class="input-group-text">km</span>
                            </div>
                            @error('kilometraje')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> 
                                Ingresa el kilometraje actual mostrado en el tablero del vehículo
                            </div>
                        </div>

                        {{-- Fecha de Captura --}}
                        <div class="mb-3">
                            <label for="fecha_captura" class="form-label">
                                <i class="fas fa-calendar text-warning"></i> Fecha de Captura <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fecha_captura" id="fecha_captura" 
                                   class="form-control @error('fecha_captura') is-invalid @enderror" 
                                   value="{{ old('fecha_captura', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('fecha_captura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Obra --}}
                        <div class="mb-3">
                            <label for="obra_id" class="form-label">
                                <i class="fas fa-building text-success"></i> Obra <span class="text-danger">*</span>
                            </label>
                            <select name="obra_id" id="obra_id" 
                                    class="form-select @error('obra_id') is-invalid @enderror" required>
                                <option value="">Seleccionar obra...</option>
                                @foreach($obras as $obra)
                                    <option value="{{ $obra->id }}" 
                                            {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                        {{ $obra->nombre_obra }}
                                    </option>
                                @endforeach
                            </select>
                            @error('obra_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Observaciones --}}
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">
                                <i class="fas fa-comment text-secondary"></i> Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" 
                                      class="form-control @error('observaciones') is-invalid @enderror" 
                                      rows="4" maxlength="1000" 
                                      placeholder="Describe el trabajo realizado, estado del vehículo, etc. (opcional)">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Máximo 1000 caracteres</div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Registrar Kilometraje
                        </button>
                        <a href="{{ route('web.kilometrajes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Panel de ayuda --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-question-circle"></i> Instrucciones</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li><strong>Selecciona el vehículo</strong> del que vas a registrar el kilometraje</li>
                        <li><strong>Ingresa el kilometraje actual</strong> mostrado en el tablero</li>
                        <li><strong>Verifica la fecha</strong> (por defecto es hoy)</li>
                        <li><strong>Selecciona la obra</strong> donde se encuentra el vehículo</li>
                        <li><strong>Añade observaciones</strong> si es necesario</li>
                    </ol>
                    
                    <hr>
                    
                    <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Importante:</h6>
                    <ul class="small">
                        <li>El kilometraje debe ser mayor al último registrado</li>
                        <li>La fecha no puede ser futura</li>
                        <li>Solo se muestran obras activas</li>
                        <li>Las observaciones son opcionales pero recomendadas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 👁️ **Show - Vista de Detalle**

```blade
{{-- resources/views/kilometrajes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalle del Kilometraje')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-eye text-info"></i> Detalle del Kilometraje</h4>
                    <div class="btn-group">
                        <a href="{{ route('web.kilometrajes.edit', $kilometraje) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="confirmarEliminacion('{{ $kilometraje->id }}')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Información Principal --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body">
                                    <h2>{{ number_format($kilometraje->kilometraje) }}</h2>
                                    <p class="mb-0"><i class="fas fa-tachometer-alt"></i> Kilometraje</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body">
                                    <h2>{{ $kilometraje->fecha_captura_formatted }}</h2>
                                    <p class="mb-0"><i class="fas fa-calendar"></i> Fecha</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body">
                                    <h2>{{ $kilometraje->getDiasDesdeCaptura() }}</h2>
                                    <p class="mb-0"><i class="fas fa-clock"></i> Días</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Información del Vehículo --}}
                    <h5><i class="fas fa-truck text-primary"></i> Información del Vehículo</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-striped">
                            <tr>
                                <th width="30%">Marca/Modelo:</th>
                                <td>{{ $kilometraje->vehiculo->marca }} {{ $kilometraje->vehiculo->modelo }}</td>
                            </tr>
                            <tr>
                                <th>Placas:</th>
                                <td><span class="badge bg-secondary">{{ $kilometraje->vehiculo->placas }}</span></td>
                            </tr>
                            <tr>
                                <th>Número de Serie:</th>
                                <td>{{ $kilometraje->vehiculo->n_serie }}</td>
                            </tr>
                            <tr>
                                <th>Año:</th>
                                <td>{{ $kilometraje->vehiculo->anio }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- Información de la Obra --}}
                    <h5><i class="fas fa-building text-success"></i> Información de la Obra</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-striped">
                            <tr>
                                <th width="30%">Obra:</th>
                                <td>{{ $kilometraje->obra->nombre_obra }}</td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($kilometraje->obra->estatus) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- Observaciones --}}
                    @if($kilometraje->observaciones)
                        <h5><i class="fas fa-comments text-warning"></i> Observaciones</h5>
                        <div class="alert alert-light mb-4">
                            {{ $kilometraje->observaciones }}
                        </div>
                    @endif

                    {{-- Información de Registro --}}
                    <h5><i class="fas fa-user text-secondary"></i> Información de Registro</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <tr>
                                <th width="30%">Registrado por:</th>
                                <td>{{ $kilometraje->usuarioCaptura->name }}</td>
                            </tr>
                            <tr>
                                <th>Fecha de registro:</th>
                                <td>{{ $kilometraje->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            @if($kilometraje->updated_at != $kilometraje->created_at)
                                <tr>
                                    <th>Última actualización:</th>
                                    <td>{{ $kilometraje->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('web.kilometrajes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Historial Reciente --}}
            @if($historialReciente->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Historial Reciente del Vehículo</h5>
                    </div>
                    <div class="card-body">
                        @foreach($historialReciente as $hist)
                            <div class="border-start border-3 border-primary ps-3 mb-3">
                                <h6 class="mb-1">{{ number_format($hist->kilometraje) }} km</h6>
                                <p class="text-muted mb-1">
                                    <small>
                                        <i class="fas fa-calendar"></i> {{ $hist->fecha_captura_formatted }}<br>
                                        <i class="fas fa-building"></i> {{ $hist->obra->nombre_obra }}
                                    </small>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Panel de Información --}}
            <div class="card">
                <div class="card-header bg-light">
                    <h5><i class="fas fa-info-circle"></i> Información</h5>
                </div>
                <div class="card-body">
                    <p><strong>Este registro muestra:</strong></p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Kilometraje del vehículo en esa fecha</li>
                        <li><i class="fas fa-check text-success"></i> Obra donde se encontraba</li>
                        <li><i class="fas fa-check text-success"></i> Usuario que lo registró</li>
                        <li><i class="fas fa-check text-success"></i> Observaciones del estado/trabajo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para eliminar --}}
<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este registro de kilometraje?
                <br><strong>Esta acción no se puede deshacer.</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="eliminarForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(id) {
    const form = document.getElementById('eliminarForm');
    form.action = `/kilometrajes/${id}`;
    
    const modal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    modal.show();
}
</script>
@endsection
```

---

## 🎨 **4. CSS Básico (Solo lo Necesario)**

```css
/* public/css/kilometrajes.css - Solo estilos necesarios */

.kilometrajes-container {
    padding: 20px;
}

/* Cards con sombra suave */
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    margin-bottom: 20px;
}

/* Header de cards */
.card-header {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    border: none;
}

/* Badges más visibles */
.badge {
    font-size: 0.85em;
    padding: 6px 10px;
}

/* Responsivo simple para tablas */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

/* Estados visuales */
.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }
.text-info { color: #17a2b8 !important; }
```

---

## ✅ **5. Checklist de Implementación**

### 📋 **Para el Frontend:**

1. **Crear el Controlador Web:**
   - [ ] Crear `app/Http/Controllers/Web/KilometrajeWebController.php`
   - [ ] Implementar todos los métodos (index, create, store, show, edit, update, destroy)

2. **Configurar Rutas Web:**
   - [ ] Añadir rutas en `routes/web.php` con el prefijo `web.kilometrajes.`

3. **Crear las Vistas Blade:**
   - [ ] `resources/views/kilometrajes/index.blade.php`
   - [ ] `resources/views/kilometrajes/create.blade.php` 
   - [ ] `resources/views/kilometrajes/show.blade.php`
   - [ ] `resources/views/kilometrajes/edit.blade.php`

4. **Estilos CSS:**
   - [ ] Añadir CSS básico en `public/css/kilometrajes.css`
   - [ ] Incluir en el layout principal

5. **Navegación:**
   - [ ] Añadir enlaces en el menú principal
   - [ ] Configurar breadcrumbs si es necesario

---

## 🚀 **Ventajas del Enfoque Laravel Blade:**

✅ **Renderizado del servidor** - Más rápido y seguro
✅ **Sin JavaScript complejo** - Mantenimiento más fácil
✅ **Formularios HTML estándar** - Compatible con todos los navegadores
✅ **Validación del servidor** - Mayor seguridad
✅ **SEO friendly** - Mejor indexación
✅ **Menos complejidad** - Desarrollo más directo

**¡El backend ya está 100% listo para soportar estas vistas Blade!** 🎉
