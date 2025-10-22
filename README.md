# Sistema de Control Interno - Solupatch

Sistema de gestión y control interno para vehículos, personal, obras y mantenimientos.

## 🚀 Características Principales

- **Gestión de Vehículos**: Control completo de flota vehicular y maquinaria
- **Gestión de Personal**: Administración de operadores y responsables
- **Control de Obras**: Seguimiento de proyectos y asignaciones
- **Mantenimientos**: Registro de servicios preventivos y correctivos
- **Kilometrajes**: Control de kilometraje y alertas automáticas
- **Documentación**: Sistema de archivos por módulo
- **Reportes PDF/Excel**: Exportación de datos filtrados

## 📋 Requisitos del Sistema

- PHP >= 8.1
- MySQL >= 8.0
- Composer
- Node.js >= 18
- NPM

## 🔧 Instalación

```bash
# 1. Clonar el repositorio
git clone <repository-url>
cd petrotekno_control_interno

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JavaScript
npm install

# 4. Configurar archivo .env
cp .env.example .env
php artisan key:generate

# 5. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Compilar assets
npm run build

# 8. Crear enlace simbólico para storage
php artisan storage:link
```

## 👤 Usuario Administrador por Defecto

Después de ejecutar los seeders, se crea un usuario administrador:

- **Email**: admin@petrotekno.com
- **Contraseña**: Admin123!

## 📊 Seeders Disponibles

```bash
# Seeders básicos del sistema
php artisan db:seed --class=DatabaseSeeder

# Datos de prueba completos (Vehículos, Personal, Obras, Mantenimientos)
php artisan db:seed --class=DatosCompletosSeeder
```

## 🎨 Sistema de Mayúsculas Automáticas

El sistema incluye un trait `UppercaseAttributes` que convierte automáticamente los datos importantes a MAYÚSCULAS:

### Modelos con conversión automática:
- **Vehículos**: marca, modelo, placas, serie, ubicación, observaciones
- **Personal**: nombre completo, CURP, RFC, NSS, licencia, dirección
- **Obras**: nombre obra, ubicación, observaciones
- **Mantenimientos**: proveedor, descripción

### Comando de conversión masiva:
```bash
# Ver preview de cambios
php artisan datos:mayusculas --dry-run

# Convertir datos existentes
php artisan datos:mayusculas
```

Ver documentación completa en `MAYUSCULAS.md`

## 📁 Estructura del Proyecto

```
app/
├── Console/Commands/      # Comandos Artisan
├── Enums/                # Enumeraciones (EstadoVehiculo, etc.)
├── Exports/              # Exportaciones Excel
├── Http/Controllers/     # Controladores
├── Mail/                 # Emails
├── Models/               # Modelos Eloquent
├── Traits/               # Traits reutilizables
└── Services/             # Servicios

database/
├── migrations/           # Migraciones
└── seeders/             # Seeders

resources/
├── views/               # Vistas Blade
├── css/                 # Estilos
└── js/                  # JavaScript

routes/
├── web.php             # Rutas web
└── api.php             # Rutas API
```

## 🔐 Roles y Permisos

El sistema utiliza Spatie Laravel Permission con 3 roles principales:

1. **Administrador del sistema**: Acceso completo
2. **Supervisor**: Gestión y reportes
3. **Operador**: Consulta y operación básica

## 📝 Módulos Principales

### 1. Vehículos
- Registro de vehículos y maquinaria
- Control de kilometraje
- Alertas de mantenimiento
- Asignación a obras
- Gestión de documentos

### 2. Personal
- Registro de personal operativo
- Categorías (Operador, Responsable, etc.)
- Documentación (CURP, RFC, Licencias)
- Historial de asignaciones

### 3. Obras
- Gestión de proyectos
- Estados: Planificada, En Progreso, Completada, Suspendida, Cancelada
- Asignación de vehículos y operadores
- Control de avance

### 4. Mantenimientos
- Servicios preventivos y correctivos
- Sistemas: Motor, Transmisión, Hidráulico, General
- Control de costos
- Historial por vehículo
- Alertas automáticas

## 📊 Reportes Disponibles

- Inventario de vehículos
- Vehículos filtrados (PDF/Excel)
- Historial de mantenimientos
- Mantenimientos pendientes
- Historial de obras por vehículo
- Historial de operadores

## 🛠️ Comandos Útiles

```bash
# Desarrollo
php artisan serve
npm run dev

# Producción
npm run build

# Limpiar cachés
php artisan optimize:clear

# Convertir datos a mayúsculas
php artisan datos:mayusculas

# Crear datos de prueba
php artisan db:seed --class=DatosCompletosSeeder
```

## 🌐 Características Técnicas

- **Framework**: Laravel 11.x
- **Base de datos**: MySQL con Eloquent ORM
- **Frontend**: Blade + TailwindCSS + Alpine.js
- **Autenticación**: Laravel Breeze
- **Permisos**: Spatie Laravel Permission
- **PDFs**: DomPDF
- **Excel**: Maatwebsite Excel
- **Emails**: Laravel Mail + Resend

## 📧 Configuración de Email

El sistema usa Resend para envío de emails. Configura en `.env`:

```env
MAIL_MAILER=resend
RESEND_KEY=tu_clave_api
```

## 🗂️ Archivos de Datos

- `estados.json`: 32 estados de México
- `estados-municipios.json`: 2,464 municipios de México

Ambos archivos contienen datos en MAYÚSCULAS para consistencia.

## 📄 Documentación Adicional

- **MAYUSCULAS.md**: Documentación completa del sistema de mayúsculas
- **GUIA_RAPIDA_MAYUSCULAS.md**: Guía rápida de uso
- **IMPLEMENTACION_MAYUSCULAS.md**: Detalles de implementación
- **REGISTROS_CREADOS.md**: Listado de registros de prueba

## 🐛 Solución de Problemas

### Error de permisos en storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Error de clave de aplicación
```bash
php artisan key:generate
```

### Error de base de datos
Verificar credenciales en `.env` y ejecutar:
```bash
php artisan migrate:fresh --seed
```

## 📞 Soporte

Para problemas o consultas, contactar al equipo de desarrollo.

---

**Versión**: 1.0.0  
**Última actualización**: 15 de octubre de 2025
