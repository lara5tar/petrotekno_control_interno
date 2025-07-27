# 🏢 Sistema de Control Interno - Petrotekno

## 📋 Descripción del Proyecto

Sistema de gestión empresarial desarrollado en **Laravel 11** con **arquitectura híbrida** que soporta tanto **Laravel Blade** (server-side rendering) como **API REST** (client-side). Diseñado para la gestión completa de vehículos, mantenimientos, personal y obras de construcción.

---

## ✅ Estado Actual del Sistema

### **MÓDULOS COMPLETAMENTE IMPLEMENTADOS**

#### 🚗 **Gestión de Vehículos**
- ✅ CRUD completo con validaciones
- ✅ Soft deletes y restauración
- ✅ Catálogo de estatus
- ✅ Intervalos de mantenimiento
- ✅ Vistas Blade implementadas

#### 🔧 **Gestión de Mantenimientos**  
- ✅ CRUD completo con validaciones
- ✅ Relación con vehículos y servicios
- ✅ Control de costos y kilometrajes
- ✅ Alertas automáticas
- ✅ Vistas Blade implementadas

#### 👥 **Gestión de Personal**
- ✅ CRUD completo con validaciones
- ✅ Categorías de personal
- ✅ Integración con usuarios
- ✅ Datos personales completos
- ✅ Vistas Blade implementadas

#### 🏗️ **Gestión de Obras**
- ✅ CRUD completo con validaciones
- ✅ Estados de obra y progreso
- ✅ Control de fechas
- ✅ Filtros avanzados
- ✅ Vistas Blade implementadas

#### 🔐 **Sistema de Seguridad**
- ✅ Autenticación Laravel Session + Sanctum
- ✅ Roles y permisos granulares
- ✅ Middleware de autorización
- ✅ Auditoría automática de acciones

---

## 🚀 Arquitectura del Sistema

### **Patrón Híbrido Implementado**
```php
// Cada controller detecta automáticamente el tipo de solicitud
public function index(Request $request) {
    $data = $this->processData($request);
    
    // API Response (AJAX/fetch)
    if ($request->expectsJson()) {
        return response()->json(['success' => true, 'data' => $data]);
    }
    
    // Blade View (navegador tradicional)
    return view('modulo.index', compact('data'));
}
```

### **Stack Tecnológico**
- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Laravel Blade + Bootstrap/Tailwind CSS ready
- **Database:** MySQL con Eloquent ORM
- **Authentication:** Laravel Sanctum + Session Auth
- **Testing:** PHPUnit (100% cobertura en controllers)
- **API:** RESTful endpoints completos

---

## 🛠️ Instalación y Configuración

### **Prerrequisitos**
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (para assets)

### **Instalación Paso a Paso**
```bash
# 1. Clonar repositorio
git clone [url-repositorio]
cd petrotekno

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=petrotekno
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Compilar assets
npm run dev

# 7. Ejecutar servidor de desarrollo
php artisan serve
```

### **Datos de Prueba**
El sistema incluye seeders que crean:
- **Usuario Admin:** `admin@petrotekno.com` / `password123`
- **Roles y permisos** completos
- **Datos de ejemplo** para todos los módulos

---

## 📚 Documentación

### **Guías Disponibles**
- 📖 **[Guía Frontend Laravel Blade](docs/LARAVEL_BLADE_FRONTEND_GUIDE.md)** - Desarrollo con Blade
- 🎯 **[Módulos Listos para Frontend](docs/FRONTEND_READY_MODULES.md)** - Estado actual
- 🔗 **[Guía de Integración](docs/FRONTEND_INTEGRATION_GUIDE.md)** - API + Blade
- 🚗 **[API Vehículos](docs/VEHICULOS_API_DOCUMENTATION.md)** - Endpoints específicos
- 🔧 **[API Mantenimientos](docs/MANTENIMIENTOS_FRONTEND_INTEGRATION_GUIDE.md)** - Endpoints específicos

### **Estructura del Proyecto**
```
app/
├── Http/Controllers/          # Controllers híbridos (Blade + API)
├── Models/                    # Modelos Eloquent con relaciones
├── Http/Requests/            # Form Requests para validación
└── Http/Middleware/          # Middleware de permisos

resources/views/              # Vistas Blade completas
├── vehiculos/               # CRUD vehículos
├── mantenimientos/          # CRUD mantenimientos  
├── personal/                # CRUD personal
└── obras/                   # CRUD obras

routes/
├── web.php                  # Rutas Blade (resourceful)
└── api.php                  # Rutas API (JSON)

tests/
├── Feature/                 # Tests de controllers y funcionalidades
└── Unit/                    # Tests unitarios
```

---

## 🧪 Testing

### **Ejecutar Tests**
```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter VehiculoController
php artisan test --filter MantenimientoController

# Tests con cobertura
php artisan test --coverage
```

### **Cobertura Actual**
- ✅ **Controllers:** 100% cobertura
- ✅ **Validaciones:** Completas
- ✅ **Permisos:** Testados
- ✅ **API Endpoints:** Validados

---

## 🛣️ Rutas Principales

### **Interfaz Web (Blade)**
```
/vehiculos              # Gestión de vehículos
/mantenimientos         # Gestión de mantenimientos
/personal               # Gestión de personal  
/obras                  # Gestión de obras
```

### **API Endpoints**
```
/api/vehiculos          # CRUD vehículos + restauración
/api/mantenimientos     # CRUD mantenimientos + estadísticas
/api/personal           # CRUD personal + categorías
/api/obras              # CRUD obras + estados
```

---

## 🔐 Sistema de Permisos

### **Permisos Implementados**
```
# Vehículos
- ver_vehiculos, crear_vehiculos, editar_vehiculos, eliminar_vehiculos

# Mantenimientos  
- ver_mantenimientos, crear_mantenimientos, actualizar_mantenimientos, eliminar_mantenimientos

# Personal
- ver_personal, crear_personal, actualizar_personal, eliminar_personal

# Obras
- ver_obras, crear_obras, actualizar_obras, eliminar_obras
```

### **Uso en Blade**
```blade
@can('crear_vehiculos')
    <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
        Nuevo Vehículo
    </a>
@endcan
```

---

## 👨‍💻 Para Desarrolladores Frontend

### **Opción 1: Laravel Blade (Recomendado para MVP)**
- ✅ **Vistas implementadas** y funcionales
- ✅ **Formularios completos** con validaciones
- ✅ **Solo necesita CSS/JS** personalizado
- ✅ **SEO friendly** por default

### **Opción 2: SPA + API**
- ✅ **Endpoints REST** completos
- ✅ **Autenticación Sanctum** configurada  
- ✅ **Respuestas JSON** consistentes
- ✅ **CORS configurado** para desarrollo

---

## 📊 Base de Datos

### **Tablas Principales**
- `users` - Usuarios del sistema
- `roles` - Roles y permisos  
- `personal` - Empleados de la empresa
- `vehiculos` - Vehículos con soft deletes
- `mantenimientos` - Historial de servicios
- `obras` - Proyectos de construcción
- `log_acciones` - Auditoría automática

### **Relaciones Implementadas**
- Usuario → Personal → Categoría
- Vehículo → Mantenimientos → Tipos de Servicio  
- Obra → Asignaciones → Personal + Vehículos

---

## 🚀 Próximos Pasos Recomendados

### **Para Frontend:**
1. **Personalizar diseño** con framework CSS elegido
2. **Agregar JavaScript** para interacciones avanzadas
3. **Implementar notificaciones** push/email
4. **Optimizar UX** con loading states y confirmaciones

### **Para Backend:**
1. **Completar módulo Asignaciones** (75% implementado)
2. **Implementar módulo Documentos** (estructura lista)  
3. **Agregar reportes** y estadísticas avanzadas
4. **Configurar deployment** y CI/CD

---

## 📞 Soporte

Para soporte técnico o consultas sobre implementación:
- 📋 **Revisar documentación** en `/docs`
- 🧪 **Ejecutar tests** para verificar funcionamiento
- 📝 **Crear issue** en GitHub para reportar problemas

---

**✨ El sistema está completamente listo para desarrollo frontend. Todos los módulos principales están implementados, testeados y documentados.**
