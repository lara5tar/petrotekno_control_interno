# Petrotekno Control Interno

Sistema de control interno para gestión de vehículos y personal de Petrotekno.

## 🚀 Características

- **Gestión de Vehículos**: Control y seguimiento de la flota vehicular
- **Gestión de Personal**: Administración de empleados y categorías
- **Sistema de Roles**: Control de acceso basado en roles
- **Dashboard Interactivo**: Panel de control con métricas en tiempo real
- **Responsive Design**: Interfaz adaptable a dispositivos móviles

## 🛠️ Tecnologías

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + TailwindCSS
- **Base de Datos**: SQLite (desarrollo)
- **Autenticación**: Laravel Auth

## 📦 Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd petrotekno_control_interno
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar archivo de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Ejecutar migraciones y seeders**
```bash
php artisan migrate:fresh --seed
```

5. **Iniciar el servidor de desarrollo**
```bash
php artisan serve
```

## 🔐 Credenciales de Prueba

### Usuario Administrador
- **Email**: admin@petrotekno.com
- **Contraseña**: password123

### Usuario Supervisor
- **Email**: supervisor@petrotekno.com
- **Contraseña**: password123

## 🎨 Estructura Frontend

### Componentes Blade
- `sidebar.blade.php`: Menú lateral de navegación
- `sidebar-item.blade.php`: Items del menú
- Layouts responsivos con TailwindCSS

### Estilos
- **Colores corporativos**:
  - Amarillo Petrotekno: `#FCCA00`
  - Negro Petrotekno: `#161615`
- **Framework**: TailwindCSS
- **Iconos**: SVG inline

### Vistas Principales
- `auth/login.blade.php`: Página de inicio de sesión
- `home.blade.php`: Dashboard principal
- `layouts/app.blade.php`: Layout principal
- `vehiculos/index.blade.php`: Gestión de vehículos

## 🔧 Desarrollo

### Frontend
Para trabajar en el frontend, seguir las instrucciones en `.github/copilot-instructions.md`.

### Comandos útiles
```bash
# Limpiar cache
php artisan config:clear
php artisan cache:clear

# Generar componentes Blade
php artisan make:component ComponentName

# Ejecutar seeders específicos
php artisan db:seed --class=AdminUserSeeder
```

## 📁 Estructura de Carpetas

```
resources/
├── css/
│   └── app.css              # Estilos TailwindCSS
├── js/
│   └── app.js               # JavaScript vanilla
└── views/
    ├── auth/
    │   └── login.blade.php
    ├── components/
    │   ├── sidebar.blade.php
    │   └── sidebar-item.blade.php
    ├── layouts/
    │   └── app.blade.php
    ├── vehiculos/
    │   └── index.blade.php
    └── home.blade.php
```

## 🚦 Estado del Proyecto

- ✅ Sistema de autenticación
- ✅ Dashboard principal
- ✅ Gestión de usuarios y roles
- ✅ Interfaz responsive
- 🔄 Gestión de vehículos (en desarrollo)
- 🔄 Reportes y estadísticas (planificado)

## 📝 Notas de Desarrollo

- El sistema usa SQLite para desarrollo
- La lógica de debug ha sido removida del login
- Todos los seeders están configurados para generar datos de prueba
- El frontend está optimizado para dispositivos móviles

---

**Versión**: 1.0  
**Última actualización**: Julio 2025