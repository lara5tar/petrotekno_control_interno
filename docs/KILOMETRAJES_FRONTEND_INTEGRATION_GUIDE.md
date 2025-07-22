# 📋 Guía de Integración Frontend - Módulo de Kilometrajes

## 🎯 Resumen
Esta guía proporciona toda la información necesaria para que el equipo de frontend integre el módulo de kilometrajes utilizando Laravel Blade y las APIs REST disponibles.

---

## 📊 Estructura de Datos

### 🚗 Modelo Kilometraje
```php
// Campos principales
id                    // ID único
vehiculo_id          // ID del vehículo
kilometraje          // Kilometraje actual del vehículo
fecha_captura        // Fecha de captura (Y-m-d)
usuario_captura_id   // ID del usuario que capturó
obra_id              // ID de la obra donde se capturó
observaciones        // Observaciones opcionales
created_at           // Fecha de creación
updated_at           // Fecha de actualización

// Relaciones cargadas
vehiculo             // Objeto vehículo completo
obra                 // Objeto obra completa
usuario_captura      // Objeto usuario que capturó
```

### 📋 Accessors Disponibles
```php
$kilometraje->fecha_captura_formatted  // "21/07/2025"
$kilometraje->getDiasDesdeCaptura()    // Días transcurridos desde captura
```

---

## 🔌 APIs REST Disponibles

### 📍 Base URL: `/api/kilometrajes`

### 1. 📖 Listar Kilometrajes
```http
GET /api/kilometrajes
```

**Query Parameters:**
- `vehiculo_id`: Filtrar por vehículo específico
- `obra_id`: Filtrar por obra específica  
- `fecha_inicio`: Filtrar desde fecha (Y-m-d)
- `fecha_fin`: Filtrar hasta fecha (Y-m-d)
- `search`: Búsqueda general por observaciones
- `per_page`: Elementos por página (default: 15)
- `page`: Página actual

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "vehiculo_id": 5,
      "kilometraje": 45000,
      "fecha_captura": "2025-07-21",
      "usuario_captura_id": 2,
      "obra_id": 3,
      "observaciones": "Mantenimiento preventivo realizado",
      "created_at": "2025-07-21T10:30:00.000000Z",
      "updated_at": "2025-07-21T10:30:00.000000Z",
      "vehiculo": {
        "id": 5,
        "marca": "Caterpillar",
        "modelo": "320D",
        "placas": "ABC-123",
        "n_serie": "CAT12345"
      },
      "obra": {
        "id": 3,
        "nombre_obra": "Construcción Plaza Central",
        "estatus": "en_progreso"
      },
      "usuario_captura": {
        "id": 2,
        "name": "Juan Pérez",
        "email": "juan@petrotekno.com"
      }
    }
  ],
  "links": {
    "first": "http://petrotekno.test/api/kilometrajes?page=1",
    "last": "http://petrotekno.test/api/kilometrajes?page=5",
    "prev": null,
    "next": "http://petrotekno.test/api/kilometrajes?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 67
  },
  "message": "Kilometrajes obtenidos exitosamente"
}
```

### 2. ➕ Crear Kilometraje
```http
POST /api/kilometrajes
```

**Headers requeridos:**
```http
Content-Type: application/json
Authorization: Bearer {token}
X-CSRF-TOKEN: {csrf_token}
```

**Body:**
```json
{
  "vehiculo_id": 5,
  "kilometraje": 45500,
  "fecha_captura": "2025-07-21",
  "obra_id": 3,
  "observaciones": "Revisión semanal programada"
}
```

**Validaciones:**
- `vehiculo_id`: requerido, debe existir
- `kilometraje`: requerido, entero, mayor al último kilometraje del vehículo
- `fecha_captura`: requerida, fecha válida (Y-m-d)
- `obra_id`: requerido, debe existir
- `observaciones`: opcional, máximo 1000 caracteres

**Respuesta exitosa (201):**
```json
{
  "success": true,
  "data": {
    "id": 15,
    "vehiculo_id": 5,
    "kilometraje": 45500,
    "fecha_captura": "2025-07-21",
    "usuario_captura_id": 2,
    "obra_id": 3,
    "observaciones": "Revisión semanal programada",
    "created_at": "2025-07-21T14:30:00.000000Z",
    "updated_at": "2025-07-21T14:30:00.000000Z",
    "vehiculo": { /* datos del vehículo */ },
    "obra": { /* datos de la obra */ },
    "usuario_captura": { /* datos del usuario */ }
  },
  "message": "Kilometraje registrado exitosamente"
}
```

### 3. 👁️ Ver Kilometraje Específico
```http
GET /api/kilometrajes/{id}
```

**Respuesta exitosa (200):** Mismo formato que crear

### 4. ✏️ Actualizar Kilometraje
```http
PUT /api/kilometrajes/{id}
```

**Body:** Mismos campos que crear
**Validaciones:** Mismas que crear

### 5. 🗑️ Eliminar Kilometraje
```http
DELETE /api/kilometrajes/{id}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Kilometraje eliminado exitosamente"
}
```

---

## 🌐 Rutas Web para Blade

### Rutas preparadas (pendientes de vistas):
```php
// Grupo: /kilometrajes
Route::get('/', 'index')->name('kilometrajes.index');           // Lista
Route::get('/create', 'create')->name('kilometrajes.create');   // Formulario crear
Route::post('/', 'store')->name('kilometrajes.store');          // Procesar crear
Route::get('/{id}', 'show')->name('kilometrajes.show');         // Ver detalle
Route::get('/{id}/edit', 'edit')->name('kilometrajes.edit');    // Formulario editar
Route::put('/{id}', 'update')->name('kilometrajes.update');     // Procesar editar
Route::delete('/{id}', 'destroy')->name('kilometrajes.destroy'); // Eliminar
```

### Controlador preparado:
- `app/Http/Controllers/KilometrajeController.php`
- Métodos web devuelven `view()` cuando las vistas estén creadas
- Métodos API devuelven JSON

---

## 🎨 Vistas Blade Sugeridas

### 📂 Estructura de archivos sugerida:
```
resources/views/kilometrajes/
├── index.blade.php          # Lista de kilometrajes
├── create.blade.php         # Formulario crear
├── edit.blade.php           # Formulario editar
├── show.blade.php           # Ver detalle
└── partials/
    ├── form.blade.php       # Formulario reutilizable
    ├── filters.blade.php    # Filtros de búsqueda
    └── table.blade.php      # Tabla de datos
```

### 🖼️ Plantilla base index.blade.php:
```blade
@extends('layouts.app')

@section('title', 'Gestión de Kilometrajes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt"></i> Kilometrajes
                    </h3>
                    <a href="{{ route('kilometrajes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Registrar Kilometraje
                    </a>
                </div>

                <!-- Filtros -->
                <div class="card-body">
                    @include('kilometrajes.partials.filters')
                </div>

                <!-- Tabla -->
                <div class="card-body">
                    @include('kilometrajes.partials.table')
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    <!-- Aquí va la paginación -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/kilometrajes.js') }}"></script>
@endpush
```

### 📝 Formulario create.blade.php:
```blade
@extends('layouts.app')

@section('title', 'Registrar Kilometraje')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Registrar Nuevo Kilometraje
                    </h3>
                </div>

                <form action="{{ route('kilometrajes.store') }}" method="POST">
                    @csrf
                    @include('kilometrajes.partials.form')
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar
                        </button>
                        <a href="{{ route('kilometrajes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel de ayuda -->
        <div class="col-md-4">
            @include('kilometrajes.partials.help')
        </div>
    </div>
</div>
@endsection
```

### 📋 Parcial form.blade.php:
```blade
<div class="card-body">
    <!-- Vehículo -->
    <div class="form-group">
        <label for="vehiculo_id">
            <i class="fas fa-truck"></i> Vehículo *
        </label>
        <select name="vehiculo_id" id="vehiculo_id" class="form-control @error('vehiculo_id') is-invalid @enderror" required>
            <option value="">Seleccionar vehículo...</option>
            {{-- Los vehículos se cargan desde el controlador --}}
            @foreach($vehiculos ?? [] as $vehiculo)
                <option value="{{ $vehiculo->id }}" 
                        {{ old('vehiculo_id', $kilometraje->vehiculo_id ?? '') == $vehiculo->id ? 'selected' : '' }}
                        data-ultimo-km="{{ $vehiculo->ultimo_kilometraje ?? 0 }}">
                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - {{ $vehiculo->placas }}
                </option>
            @endforeach
        </select>
        @error('vehiculo_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">
            Último kilometraje: <span id="ultimo-kilometraje">0</span> km
        </small>
    </div>

    <!-- Kilometraje -->
    <div class="form-group">
        <label for="kilometraje">
            <i class="fas fa-tachometer-alt"></i> Kilometraje Actual *
        </label>
        <input type="number" name="kilometraje" id="kilometraje" 
               class="form-control @error('kilometraje') is-invalid @enderror" 
               value="{{ old('kilometraje', $kilometraje->kilometraje ?? '') }}" 
               min="0" step="1" required>
        @error('kilometraje')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">
            Debe ser mayor al último kilometraje registrado
        </small>
    </div>

    <!-- Fecha -->
    <div class="form-group">
        <label for="fecha_captura">
            <i class="fas fa-calendar"></i> Fecha de Captura *
        </label>
        <input type="date" name="fecha_captura" id="fecha_captura" 
               class="form-control @error('fecha_captura') is-invalid @enderror" 
               value="{{ old('fecha_captura', $kilometraje->fecha_captura ?? date('Y-m-d')) }}" 
               required>
        @error('fecha_captura')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Obra -->
    <div class="form-group">
        <label for="obra_id">
            <i class="fas fa-building"></i> Obra *
        </label>
        <select name="obra_id" id="obra_id" class="form-control @error('obra_id') is-invalid @enderror" required>
            <option value="">Seleccionar obra...</option>
            @foreach($obras ?? [] as $obra)
                <option value="{{ $obra->id }}" 
                        {{ old('obra_id', $kilometraje->obra_id ?? '') == $obra->id ? 'selected' : '' }}>
                    {{ $obra->nombre_obra }}
                </option>
            @endforeach
        </select>
        @error('obra_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Observaciones -->
    <div class="form-group">
        <label for="observaciones">
            <i class="fas fa-comment"></i> Observaciones
        </label>
        <textarea name="observaciones" id="observaciones" 
                  class="form-control @error('observaciones') is-invalid @enderror" 
                  rows="3" maxlength="1000" 
                  placeholder="Observaciones sobre el registro del kilometraje...">{{ old('observaciones', $kilometraje->observaciones ?? '') }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">
            <span id="char-count">0</span>/1000 caracteres
        </small>
    </div>
</div>
```

---

## 🔧 Datos para Frontend

### 📦 Datos que debe pasar el controlador a las vistas:

```php
// Para index
public function index()
{
    $kilometrajes = Kilometraje::with(['vehiculo', 'obra', 'usuarioCaptura'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
    
    $vehiculos = Vehiculo::select('id', 'marca', 'modelo', 'placas')
        ->orderBy('marca')
        ->get();
    
    $obras = Obra::select('id', 'nombre_obra')
        ->where('estatus', '!=', 'cancelada')
        ->orderBy('nombre_obra')
        ->get();
    
    return view('kilometrajes.index', compact('kilometrajes', 'vehiculos', 'obras'));
}

// Para create/edit
public function create()
{
    $vehiculos = Vehiculo::with(['estatus'])
        ->where('estatus_id', 1) // Solo activos
        ->select('id', 'marca', 'modelo', 'placas', 'kilometraje_actual')
        ->orderBy('marca')
        ->get();
    
    $obras = Obra::select('id', 'nombre_obra')
        ->whereIn('estatus', ['planificada', 'en_progreso'])
        ->orderBy('nombre_obra')
        ->get();
    
    return view('kilometrajes.create', compact('vehiculos', 'obras'));
}
```

### 🎯 JavaScript sugerido:

```javascript
// public/js/kilometrajes.js
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar último kilometraje al seleccionar vehículo
    const vehiculoSelect = document.getElementById('vehiculo_id');
    const ultimoKmSpan = document.getElementById('ultimo-kilometraje');
    const kilometrajeInput = document.getElementById('kilometraje');
    
    if (vehiculoSelect) {
        vehiculoSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const ultimoKm = selectedOption.dataset.ultimoKm || 0;
            
            ultimoKmSpan.textContent = Number(ultimoKm).toLocaleString();
            kilometrajeInput.min = parseInt(ultimoKm) + 1;
            
            if (kilometrajeInput.value && parseInt(kilometrajeInput.value) <= parseInt(ultimoKm)) {
                kilometrajeInput.value = '';
                kilometrajeInput.placeholder = `Debe ser mayor a ${Number(ultimoKm).toLocaleString()} km`;
            }
        });
    }
    
    // Contador de caracteres para observaciones
    const observacionesTextarea = document.getElementById('observaciones');
    const charCount = document.getElementById('char-count');
    
    if (observacionesTextarea && charCount) {
        observacionesTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 950) {
                charCount.classList.add('text-warning');
            } else {
                charCount.classList.remove('text-warning');
            }
        });
        
        // Inicializar contador
        charCount.textContent = observacionesTextarea.value.length;
    }
    
    // Validación en tiempo real
    if (kilometrajeInput) {
        kilometrajeInput.addEventListener('blur', function() {
            validateKilometraje(this.value);
        });
    }
});

function validateKilometraje(value) {
    const vehiculoSelect = document.getElementById('vehiculo_id');
    if (!vehiculoSelect.value) return;
    
    const ultimoKm = parseInt(vehiculoSelect.options[vehiculoSelect.selectedIndex].dataset.ultimoKm) || 0;
    
    if (parseInt(value) <= ultimoKm) {
        showAlert('error', `El kilometraje debe ser mayor a ${ultimoKm.toLocaleString()} km`);
        return false;
    }
    
    return true;
}

function showAlert(type, message) {
    // Implementar sistema de alertas Toast/SweetAlert
    console.log(`${type}: ${message}`);
}
```

---

## 🔐 Permisos Requeridos

### 👤 Permisos de usuario necesarios:
- `ver_kilometrajes`: Para acceder a la lista y ver detalles
- `crear_kilometrajes`: Para crear nuevos registros
- `editar_kilometrajes`: Para modificar registros existentes
- `eliminar_kilometrajes`: Para eliminar registros

### 🛡️ Middleware aplicado:
- `auth:sanctum`: Autenticación requerida
- `check.permission:permiso_especifico`: Verificación de permisos por acción

---

## 🎨 Estilos CSS Sugeridos

```css
/* public/css/kilometrajes.css */
.kilometrajes-container {
    padding: 20px;
}

.kilometraje-card {
    transition: all 0.3s ease;
    border-left: 4px solid #3498db;
}

.kilometraje-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.ultimo-kilometraje {
    font-size: 1.2em;
    font-weight: bold;
    color: #2c3e50;
}

.kilometraje-badge {
    font-size: 0.9em;
    padding: 4px 8px;
}

.diferencia-positiva {
    color: #27ae60;
}

.diferencia-negativa {
    color: #e74c3c;
}

.filtros-panel {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .kilometrajes-container {
        padding: 10px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .card-header .btn {
        margin-top: 10px;
        width: 100%;
    }
}
```

---

## 📱 Funcionalidades Avanzadas Sugeridas

### 🔍 Filtros Avanzados:
```blade
{{-- resources/views/kilometrajes/partials/filters.blade.php --}}
<form method="GET" action="{{ route('kilometrajes.index') }}" class="row filtros-panel">
    <div class="col-md-3">
        <label for="filter_vehiculo">Vehículo:</label>
        <select name="vehiculo_id" id="filter_vehiculo" class="form-control">
            <option value="">Todos los vehículos</option>
            @foreach($vehiculos as $vehiculo)
                <option value="{{ $vehiculo->id }}" {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-3">
        <label for="filter_obra">Obra:</label>
        <select name="obra_id" id="filter_obra" class="form-control">
            <option value="">Todas las obras</option>
            @foreach($obras as $obra)
                <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                    {{ $obra->nombre_obra }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-2">
        <label for="fecha_inicio">Desde:</label>
        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
    </div>
    
    <div class="col-md-2">
        <label for="fecha_fin">Hasta:</label>
        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
    </div>
    
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        <a href="{{ route('kilometrajes.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Limpiar
        </a>
    </div>
</form>
```

### 📊 Dashboard de Estadísticas:
- Kilometraje promedio por vehículo
- Alertas de mantenimiento
- Vehículos más utilizados
- Progreso por obra

### 📈 Gráficos Sugeridos:
- Evolución del kilometraje por vehículo (Chart.js/ApexCharts)
- Distribución de uso por obra
- Alertas de mantenimiento pendientes

---

## 🚨 Validaciones Frontend

### ✅ JavaScript de Validación:
```javascript
// Validaciones recomendadas
function validateForm() {
    const vehiculoId = document.getElementById('vehiculo_id').value;
    const kilometraje = document.getElementById('kilometraje').value;
    const fechaCaptura = document.getElementById('fecha_captura').value;
    const obraId = document.getElementById('obra_id').value;
    
    if (!vehiculoId) {
        showError('Debe seleccionar un vehículo');
        return false;
    }
    
    if (!kilometraje || kilometraje <= 0) {
        showError('El kilometraje debe ser mayor a 0');
        return false;
    }
    
    if (!fechaCaptura) {
        showError('Debe seleccionar una fecha');
        return false;
    }
    
    if (!obraId) {
        showError('Debe seleccionar una obra');
        return false;
    }
    
    return true;
}
```

---

## 🔗 Enlaces de Referencia

### 📚 Archivos Clave del Backend:
- **Modelo**: `app/Models/Kilometraje.php`
- **Controlador**: `app/Http/Controllers/KilometrajeController.php`
- **FormRequests**: `app/Http/Requests/StoreKilometrajeRequest.php`, `app/Http/Requests/UpdateKilometrajeRequest.php`
- **Factory**: `database/factories/KilometrajeFactory.php`
- **Migración**: `database/migrations/2025_07_22_024546_create_kilometrajes_table.php`
- **Routes**: `routes/api.php`, `routes/web.php`

### 🧪 Tests de Referencia:
- **Feature**: `tests/Feature/KilometrajeControllerTest.php`
- **Unit**: `tests/Unit/KilometrajeModelTest.php`

---

## ⚠️ Notas Importantes

1. **Permisos**: Verificar que el usuario tenga los permisos necesarios antes de mostrar botones de acción
2. **Validación**: El kilometraje debe ser siempre mayor al último registrado para el vehículo
3. **CSRF**: No olvidar incluir `@csrf` en todos los formularios
4. **Sanitización**: Los datos se sanitizan automáticamente en el backend
5. **Logging**: Todas las acciones se registran automáticamente en el log de auditoría
6. **Transacciones**: Las operaciones críticas están protegidas con transacciones de BD

---

## 🎯 Siguiente Paso

Una vez implementadas las vistas Blade, el módulo de kilometrajes estará completamente funcional tanto en API como en interfaz web.

**¡El backend está 100% listo y probado para la integración! 🚀**
