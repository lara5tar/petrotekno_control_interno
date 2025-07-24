# 🚀 **GUÍA BLADE - SISTEMA DE ALERTAS DE MANTENIMIENTO**
## Backend Completamente Preparado para Laravel Blade

---

## 📋 **CONFIRMACIÓN: ✅ 100% LISTO PARA BLADE**

**¡EXCELENTE NOTICIA!** El backend del sistema de alertas de mantenimiento está **completamente preparado** para ser consumido desde Laravel Blade. Todos los endpoints, modelos, servicios y configuraciones están operativos y probados.

---

## ⚠️ **ACCIÓN INMEDIATA REQUERIDA**

### **CRÍTICO: Actualizar Formularios de Mantenimiento Existentes**

Los formularios `resources/views/mantenimientos/create.blade.php` y `edit.blade.php` **REQUIEREN** agregar el nuevo campo obligatorio `sistema_vehiculo`.

#### **Código a Agregar Después del Campo "Tipo de Servicio":**

```blade
<!-- Sistema del Vehículo - NUEVO CAMPO OBLIGATORIO -->
<div class="form-group row">
    <label for="sistema_vehiculo" class="col-md-4 col-form-label text-md-right">
        Sistema del Vehículo <span class="text-danger">*</span>
    </label>
    <div class="col-md-6">
        <select id="sistema_vehiculo" 
                class="form-control @error('sistema_vehiculo') is-invalid @enderror" 
                name="sistema_vehiculo" required>
            <option value="">Seleccione el sistema</option>
            <option value="motor" {{ old('sistema_vehiculo', $mantenimiento->sistema_vehiculo ?? '') == 'motor' ? 'selected' : '' }}>
                🔧 Motor
            </option>
            <option value="transmision" {{ old('sistema_vehiculo', $mantenimiento->sistema_vehiculo ?? '') == 'transmision' ? 'selected' : '' }}>
                ⚙️ Transmisión
            </option>
            <option value="hidraulico" {{ old('sistema_vehiculo', $mantenimiento->sistema_vehiculo ?? '') == 'hidraulico' ? 'selected' : '' }}>
                💧 Hidráulico
            </option>
            <option value="general" {{ old('sistema_vehiculo', $mantenimiento->sistema_vehiculo ?? '') == 'general' ? 'selected' : '' }}>
                🛠️ General
            </option>
        </select>
        @error('sistema_vehiculo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> 
            Este campo determina el tipo de mantenimiento y activa las alertas automáticas.
        </small>
    </div>
</div>
```

---

## 🌐 **ENDPOINTS API LISTOS PARA BLADE**

### **📊 Configuración de Alertas**

#### **1. Obtener todas las configuraciones**
```javascript
// AJAX call desde Blade
fetch('/api/configuracion-alertas', {
    headers: {
        'Authorization': `Bearer ${userToken}`,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    // data.data contiene todas las configuraciones organizadas
    console.log(data.data.general.alerta_inmediata.valor); // true/false
    console.log(data.data.horarios.hora_envio_diario.valor); // "08:00"
    console.log(data.data.destinatarios.emails_principales.valor); // array de emails
});
```

#### **2. Obtener resumen de alertas actuales**
```javascript
// Para dashboard de alertas
fetch('/api/configuracion-alertas/resumen-alertas', {
    headers: {
        'Authorization': `Bearer ${userToken}`,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    // data.data.resumen contiene contadores
    const resumen = data.data.resumen;
    document.getElementById('alertas-criticas').textContent = resumen.por_urgencia.critica;
    document.getElementById('alertas-altas').textContent = resumen.por_urgencia.alta;
    document.getElementById('vehiculos-afectados').textContent = resumen.vehiculos_afectados;
    
    // data.data.alertas contiene array de alertas detalladas
    const alertas = data.data.alertas;
    alertas.forEach(alerta => {
        console.log(`${alerta.vehiculo_info.nombre_completo}: ${alerta.urgencia}`);
    });
});
```

#### **3. Actualizar configuraciones**
```javascript
// Guardar destinatarios
fetch('/api/configuracion-alertas/destinatarios', {
    method: 'PUT',
    headers: {
        'Authorization': `Bearer ${userToken}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        emails_principales: ["admin@empresa.com", "mantenimiento@empresa.com"],
        emails_copia: ["supervisor@empresa.com"]
    })
});

// Guardar horarios
fetch('/api/configuracion-alertas/horarios', {
    method: 'PUT',
    headers: {
        'Authorization': `Bearer ${userToken}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        hora_envio_diario: "08:00",
        dias_semana: ["lunes", "martes", "miercoles", "jueves", "viernes"]
    })
});

// Guardar configuración general
fetch('/api/configuracion-alertas/general', {
    method: 'PUT',
    headers: {
        'Authorization': `Bearer ${userToken}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        alerta_inmediata: true,
        recordatorios_activos: true,
        cooldown_horas: 4
    })
});
```

#### **4. Probar envío de alertas**
```javascript
// Simulación de envío
fetch('/api/configuracion-alertas/probar-envio', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${userToken}`,
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(`Prueba completada: ${data.data.alertas_count} alertas encontradas`);
        console.log(`Destinatarios: ${data.data.emails_destino.to.join(', ')}`);
    }
});
```

---

## 🎨 **PÁGINAS BLADE SUGERIDAS**

### **1. Dashboard de Alertas**
**Crear:** `resources/views/alertas/dashboard.blade.php`

**Features clave:**
- 📊 **Widgets de resumen**: Contadores por urgencia (crítica, alta, normal)
- 📋 **Tabla de alertas**: Lista detallada de todas las alertas activas
- 🔄 **Actualización automática**: AJAX cada 30 segundos
- 🚛 **Información de vehículos**: Marca, modelo, placas, kilometraje
- ⚡ **Acciones rápidas**: Enlace directo a crear mantenimiento

**Estructura HTML básica:**
```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Resumen Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h4 id="alertas-criticas">0</h4>
                    <span>Críticas</span>
                </div>
            </div>
        </div>
        <!-- Más cards... -->
    </div>
    
    <!-- Tabla de Alertas -->
    <div class="card">
        <div class="card-body">
            <table class="table" id="tabla-alertas">
                <thead>
                    <tr>
                        <th>Vehículo</th>
                        <th>Sistema</th>
                        <th>Km Actual</th>
                        <th>Urgencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="alertas-tbody">
                    <!-- Datos via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function cargarAlertas() {
    const response = await fetch('/api/configuracion-alertas/resumen-alertas');
    const data = await response.json();
    // Actualizar tabla y contadores
}

// Cargar cada 30 segundos
setInterval(cargarAlertas, 30000);
document.addEventListener('DOMContentLoaded', cargarAlertas);
</script>
@endsection
```

### **2. Configuración de Alertas**
**Crear:** `resources/views/alertas/configuracion.blade.php`

**Features clave:**
- 📧 **Configurar destinatarios**: Emails principales y copia
- ⏰ **Configurar horarios**: Hora de envío y días activos
- ⚙️ **Configuración general**: Alertas inmediatas, recordatorios, anti-spam
- 🧪 **Probar envío**: Simulación para verificar configuración
- 📱 **Tabs de navegación**: Organización clara por secciones

---

## 🛣️ **RUTAS WEB NECESARIAS**

Agregar en `routes/web.php`:

```php
// Rutas para sistema de alertas (Blade)
Route::middleware(['auth', 'permission:ver_alertas_mantenimiento'])->group(function () {
    Route::get('/alertas', [AlertasController::class, 'dashboard'])->name('alertas.dashboard');
    Route::get('/alertas/configuracion', [AlertasController::class, 'configuracion'])->name('alertas.configuracion');
});
```

---

## 🎛️ **CONTROLADOR BLADE**

**Crear:** `app/Http/Controllers/AlertasController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    protected $alertasService;
    protected $configService;

    public function __construct(
        AlertasMantenimientoService $alertasService,
        ConfiguracionAlertasService $configService
    ) {
        $this->alertasService = $alertasService;
        $this->configService = $configService;
    }

    public function dashboard()
    {
        return view('alertas.dashboard');
    }

    public function configuracion()
    {
        return view('alertas.configuracion');
    }
}
```

---

## 📱 **NAVEGACIÓN - Actualizar Menú Principal**

```blade
<!-- En layouts/app.blade.php -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
        <i class="fas fa-tools"></i> Mantenimiento
    </a>
    <div class="dropdown-menu">
        <a class="dropdown-item" href="{{ route('mantenimientos.index') }}">
            <i class="fas fa-list"></i> Ver Mantenimientos
        </a>
        <a class="dropdown-item" href="{{ route('mantenimientos.create') }}">
            <i class="fas fa-plus"></i> Nuevo Mantenimiento
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('alertas.dashboard') }}">
            <i class="fas fa-bell text-warning"></i> Alertas Activas
            <span class="badge badge-warning" id="menu-alertas-count">0</span>
        </a>
        <a class="dropdown-item" href="{{ route('alertas.configuracion') }}">
            <i class="fas fa-cog"></i> Configurar Alertas
        </a>
    </div>
</li>
```

---

## 🔐 **PERMISOS REQUERIDOS**

Los usuarios necesitan estos permisos para acceder a las funcionalidades:

- `ver_alertas_mantenimiento` - Ver dashboard de alertas
- `ver_configuraciones` - Ver configuraciones
- `editar_configuraciones` - Modificar configuraciones
- `gestionar_alertas_mantenimiento` - Probar envío de alertas

---

## 🔄 **COMPORTAMIENTO AUTOMÁTICO**

### **Al crear/editar mantenimiento:**
1. 👤 Usuario envía formulario con `sistema_vehiculo`
2. ✅ Validaciones verifican datos
3. 💾 Se guarda en base de datos
4. 🔄 **Observer automático** detecta cambios
5. 📊 **Se actualiza kilometraje** del vehículo (si es mayor)
6. ⚡ **Job en background** recalcula alertas
7. 📧 **Si hay alertas nuevas** → envía email inmediato (si está habilitado)

### **Envío diario programado:**
- 🕗 **Todos los días a las 08:00** (configurable)
- 📊 **Se verifican todos los vehículos**
- 📄 **Se crea reporte PDF**
- 📧 **Se envía email** a destinatarios configurados

---

## 📧 **FORMATO DE ALERTAS**

### **Estructura de alerta individual:**
```json
{
    "vehiculo_id": 1,
    "sistema": "Motor",
    "vehiculo_info": {
        "marca": "Toyota",
        "modelo": "Hilux",
        "placas": "ABC-123",
        "nombre_completo": "Toyota Hilux - ABC-123"
    },
    "kilometraje_actual": 25000,
    "intervalo_configurado": 10000,
    "ultimo_mantenimiento": {
        "fecha": "15/06/2025",
        "kilometraje": 15000,
        "descripcion": "Cambio de aceite"
    },
    "proximo_mantenimiento_km": 25000,
    "km_vencido_por": 0,
    "urgencia": "normal", // normal|alta|critica
    "porcentaje_sobrepaso": 0.0,
    "fecha_deteccion": "23/07/2025 15:30:45"
}
```

### **Criterios de urgencia:**
- 🔵 **Normal**: Mantenimiento al día o próximo (≤100% del intervalo)
- 🟡 **Alta**: Vencido hasta 25% (101%-125% del intervalo)
- 🔴 **Crítica**: Vencido más del 25% (>125% del intervalo)

---

## 🛠️ **COMANDOS ÚTILES PARA DEBUGGING**

```bash
# Ver configuración actual
php artisan tinker
>>> App\Services\ConfiguracionAlertasService::obtenerTodas()

# Verificar alertas de un vehículo específico
>>> App\Services\AlertasMantenimientoService::verificarVehiculo(1)

# Envío manual de alertas (simulación)
php artisan alertas:enviar-diarias --dry-run

# Forzar envío ignorando horarios
php artisan alertas:enviar-diarias --force

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

---

## 📋 **CHECKLIST DE IMPLEMENTACIÓN**

### **✅ Paso 1: Formularios Existentes (CRÍTICO)**
- [ ] Agregar campo `sistema_vehiculo` en `create.blade.php`
- [ ] Agregar campo `sistema_vehiculo` en `edit.blade.php`
- [ ] Probar formularios actualizados

### **📊 Paso 2: Dashboard de Alertas**
- [ ] Crear `resources/views/alertas/dashboard.blade.php`
- [ ] Implementar AJAX para cargar alertas
- [ ] Agregar widgets de resumen
- [ ] Configurar actualización automática

### **⚙️ Paso 3: Configuración**
- [ ] Crear `resources/views/alertas/configuracion.blade.php`
- [ ] Implementar formularios para cada sección
- [ ] Conectar con endpoints de configuración
- [ ] Agregar función de prueba

### **🛣️ Paso 4: Navegación**
- [ ] Crear `AlertasController` para Blade
- [ ] Agregar rutas en `web.php`
- [ ] Actualizar menú principal
- [ ] Configurar permisos

### **🧪 Paso 5: Testing**
- [ ] Probar creación de mantenimientos
- [ ] Verificar dashboard de alertas
- [ ] Probar configuración
- [ ] Verificar permisos y autenticación

---

## 🎯 **RESUMEN FINAL**

### ✅ **CONFIRMADO Y OPERATIVO:**
- ✅ **Backend completamente funcional**
- ✅ **API endpoints probados y documentados**
- ✅ **Sistema de alertas automático activo**
- ✅ **Configuración centralizada y flexible**
- ✅ **Validaciones robustas implementadas**
- ✅ **Tests unitarios y de integración pasando**

### 🚧 **PENDIENTE (Solo Frontend):**
- 🔧 **Agregar campo en formularios de mantenimiento**
- 📊 **Crear páginas de dashboard y configuración**
- 🛣️ **Configurar rutas y controlador Blade**
- 📱 **Actualizar navegación y menús**

### 🚀 **LISTO PARA IMPLEMENTAR:**
- Dashboard interactivo con AJAX
- Configuración completa via formularios
- Integración con sistema de permisos
- Alertas en tiempo real (opcional)

---

## 🆘 **SOPORTE**

### **Contacto para dudas técnicas:**
- Revisar documentación detallada en `/docs/SISTEMA_ALERTAS_MANTENIMIENTO_FRONTEND_GUIDE.md`
- Consultar logs en `storage/logs/laravel.log`
- Usar comandos artisan para debugging

### **Errores comunes y soluciones:**
1. **"sistema_vehiculo is required"** → Agregar campo en formularios
2. **"Unauthorized" en API** → Verificar token y permisos
3. **Alertas no se muestran** → Revisar configuración de destinatarios

---

**🎉 ¡EL SISTEMA ESTÁ 100% PREPARADO PARA LARAVEL BLADE!**

**El equipo frontend puede comenzar la implementación inmediatamente. Solo necesita agregar el campo obligatorio en los formularios existentes y crear las nuevas páginas sugeridas.**
